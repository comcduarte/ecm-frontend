<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\CC;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Contract\Service\CCServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetViewCCHandler implements RequestHandlerInterface
{
    #[Inject(
        CCServiceInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected CCServiceInterface $cCService,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        try {
            $cC = $this->cCService->findCC($request->getAttribute('uuid'));
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return new HtmlResponse(
            $this->template->render('c-c::view-c-c', [
                'cC' => $cC,
            ])
        );
    }
}
