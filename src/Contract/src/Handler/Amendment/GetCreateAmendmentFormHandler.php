<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Amendment;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\CreateAmendmentForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateAmendmentFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateAmendmentForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateAmendmentForm $form,
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->form->setAttribute('action', $this->router->generateUri('amendment::get-create'));
        
        return new HtmlResponse(
            $this->template->render('amendment::create-form', [
                'form' => $this->form->prepare()
            ])
        );
    }
}