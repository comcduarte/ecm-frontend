<?php
declare(strict_types = 1);
namespace Frontend\Workflow\Controller;

use Dot\Authorization\AuthorizationInterface;
use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Service\ContractServiceInterface;
use Frontend\Page\Service\PageServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Core\Contract\Entity\Contract;

class WorkflowPageController extends AbstractActionController
{
    #[Inject(
        ContractServiceInterface::class,
        PageServiceInterface::class, 
        RouterInterface::class, 
        TemplateRendererInterface::class,
        AuthorizationInterface::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected PageServiceInterface $pageService, 
        protected RouterInterface $router, 
        protected TemplateRendererInterface $template,
        protected AuthorizationInterface $authorizationService,
    ){}

    public function indexAction(): ResponseInterface
    {
        $isGranted = $this->authorizationService->isGranted('approve'); 
        
        $result = $this->contractService->getContracts([]);
        
        $contracts = [];
        foreach ($result as $contract) {
            $a = new Contract();
            $a->setProject_name('wHOSY WHATSIT');
            $a->setFolder_id('12345');
            
            $contracts[] = $a;
            unset($a);
        }
        
       return new HtmlResponse($this->template->render(
            'workflow::dashboard', 
            [
                'active' => 'workflow',
                'isGranted' => $isGranted,
                'contracts' => $contracts,
            ]
            ));
    }
}