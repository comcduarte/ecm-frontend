<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\UploadFileForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetImportContractHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        UploadFileForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected UploadFileForm $form,
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->form
            ->setAttribute('action', $this->router->generateUri('contract::import-contract'));
        
        return new HtmlResponse(
            $this->template->render('contract::import-contract', [
                'form' => $this->form->prepare(),
            ])
        );
    }
}