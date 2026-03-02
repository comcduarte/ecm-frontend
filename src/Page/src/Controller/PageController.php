<?php

declare(strict_types=1);

namespace Frontend\Page\Controller;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Page\Service\PageServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Michelf\Markdown;
use Psr\Http\Message\ResponseInterface;

class PageController extends AbstractActionController
{
    #[Inject(
        PageServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
    )]
    public function __construct(
        protected PageServiceInterface $pageService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template
    ) {
    }

    public function indexAction(): ResponseInterface
    {
        
        $parser = new Markdown();
        $contents = file_get_contents(__DIR__ . '/../../../../CHANGELOG.md');
        $html = $parser->defaultTransform($contents);
        
        return new HtmlResponse($this->template->render(
        //                 'home::dashboard',
            'app::home',
            [
                'active' => 'home',
                'changelog' => $html,
            ],
            ));
    }

    public function homeAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('app::home', ['routeName' => 'index'])
        );
    }

    public function aboutUsAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::about')
        );
    }
    
    public function helpAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::help')    
        );
    }
    
    public function diagAction(): ResponseInterface
    {
        
        $diagnostics = [
            
        ];
        
        $ini = [
            'upload_max_filesize',
            'post_max_size',
            'memory_limit',
            'max_execution_time',
        ];
        
        foreach ($ini as $name) {
            $diagnostics[] = [
                'name' => $name,
                'value' => ini_get($name),
            ];
        }
            
        
        
        return new HtmlResponse(
            $this->template->render(
                'page::diag',
                [
                    'active' => 'help',
                    'diagnostics' => $diagnostics,
                ]
                )
            );
    }

    public function premiumContentAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::premium-content')
        );
    }

    public function whoWeAreAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::who-we-are')
        );
    }
}
