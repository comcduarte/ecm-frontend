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

class HomeController extends AbstractActionController
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
            ){}
            
        public function dashboardAction(): ResponseInterface
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
}