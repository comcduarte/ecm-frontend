<?php
declare(strict_types = 1);
namespace Frontend\Page\Controller;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\UploadFileForm;
use Frontend\Page\Service\PageServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

class CreatePageController extends AbstractActionController
{
    #[Inject(
        PageServiceInterface::class, 
        RouterInterface::class, 
        TemplateRendererInterface::class,
        UploadFileForm::class,
        )]
    public function __construct(
        protected PageServiceInterface $pageService, 
        protected RouterInterface $router, 
        protected TemplateRendererInterface $template,
        protected UploadFileForm $form,
    ){}
    
    public function formsAction(): ResponseInterface
    {
        return new HtmlResponse($this->template->render('create::forms'));
    }
    
    public function importAction(): ResponseInterface
    {
        $upload_file_form = $this->form;
        $upload_file_form->setAttribute('action', $this->router->generateUri('contract::import-contract'));
        $upload_file_form->prepare();
        
        
        return new HtmlResponse(
            $this->template->render('create::import',[
                'form' => $upload_file_form,
            ]));
    }
    
    public function templatesAction(): ResponseInterface
    {
        return new HtmlResponse($this->template->render('create::templates'));
    }
}