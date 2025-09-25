<?php
declare(strict_types = 1);
namespace Frontend\Page\Controller;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Page\Service\PageServiceInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\HtmlResponse;


class CreatePageController extends AbstractActionController
{
    #[Inject(PageServiceInterface::class, RouterInterface::class, TemplateRendererInterface::class)]
    public function __construct(protected PageServiceInterface $pageService, protected RouterInterface $router, protected TemplateRendererInterface $template)
    {}
    
    public function formsAction(): ResponseInterface
    {
        return new HtmlResponse($this->template->render('create::forms'));
    }
    
    public function importAction(): ResponseInterface
    {
        return new HtmlResponse($this->template->render('create::import'));
    }
    
    public function templatesAction(): ResponseInterface
    {
        return new HtmlResponse($this->template->render('create::templates'));
    }
}