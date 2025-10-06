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
        
        $contracts = $this->contractService->search([
            'ancestor_folder_id' => '336171795885',        //-- WORKFLOW > ONBASE > DEPT > IT
            'template_key' => "ecm-application",
            'field' => "dept",
            'scope' => "enterprise_1328932288",
            'query' => "contractnumber = :val",
            'query_params' => [
                'val' => '2017005',
            ],
        ]);
        
       return new HtmlResponse($this->template->render(
            'workflow::dashboard', 
            [
                'active' => 'workflow',
                'isGranted' => $isGranted,
                'contracts' => $contracts[0],
            ]
            ));
    }
}