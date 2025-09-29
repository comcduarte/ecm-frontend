<?php
declare(strict_types = 1);
namespace Frontend\Template\Handler;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Template\Form\CreateTemplateForm;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

class TemplatePageHandler extends AbstractActionController
{
    #[Inject(
        TemplateServiceInterface::class, 
        RouterInterface::class, 
        Templaterendererinterface::class,
        CreateTemplateForm::class,
        )]
    public function __construct(
        protected TemplateServiceInterface $templateService, 
        protected RouterInterface $router, 
        protected TemplateRendererInterface $template,
        protected CreateTemplateForm $createTemplateForm,
        )
    {}
    
    public function createAction(): ResponseInterface
    {
        $this->createTemplateForm
        ->setAttribute('action', $this->router->generateUri('template', ['action' => 'create']));
        
        return new HtmlResponse(
            $this->template->render('template::create-template-form', [
                'form' => $this->createTemplateForm->prepare(),
            ])
        );
    }
    
    public function deleteAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('template::delete-template-form')
            );
    }
    
    public function editAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('template::edit-template-form')
            );
    }
    
    public function listAction(): ResponseInterface
    {
        $request = $this->getRequest();
        
        return new HtmlResponse(
            $this->template->render('template::list-template', [
                'templates' => $this->templateService->getTemplates($request->getQueryParams()),
            ]
            ));
    }
    
    public function viewAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('template::view-template')
            );
    }
}