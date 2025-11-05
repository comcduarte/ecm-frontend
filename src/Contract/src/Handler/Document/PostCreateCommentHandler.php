<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Document;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Form\CreateCommentForm;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\BaseResource;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Comment;
use Throwable;

class PostCreateCommentHandler implements RequestHandlerInterface
{
    #[Inject(
        AccessTokenService::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateCommentForm::class,
        RouterInterface::class,
        )]
    public function __construct(
        protected AccessTokenService $accessTokenService,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateCommentForm $form,
        protected RouterInterface $router,
        ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = (array) $request->getParsedBody();
            $this->form->setData($data);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                
                $item = new BaseResource();
                $item->setId($request->getAttribute('id'));
                $item->setType('file');
                
                $access_token = $this->accessTokenService->getAccessToken();
                $comment = new Comment($access_token);
                $result = $comment->create_comment($data['MESSAGE'], $item);
                
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }                
                
                $this->messenger->addSuccess('Comment Created');
                return new RedirectResponse($request->getUri());
            }
        } catch (Throwable $e) {
            $this->messenger->addError($e->getMessage());
            return new RedirectResponse($request->getUri());
        }
    }
}