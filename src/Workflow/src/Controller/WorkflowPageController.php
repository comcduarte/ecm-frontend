<?php
declare(strict_types = 1);
namespace Frontend\Workflow\Controller;

use Core\Contract\Enum\QueueFolderEnum;
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

    #[Inject(ContractServiceInterface::class, PageServiceInterface::class, 
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
        return $this->render(
            'workflow::dashboard',
            [
                'active' => 'workflow',
                'contracts' => $this->search(QueueFolderEnum::ECM_IT),
            ],
        );
    }
    
    public function legalAction(): ResponseInterface
    {
        return $this->render(
            'workflow::dashboard',
            [
                'active' => 'workflow',
                'contracts' => $this->search(QueueFolderEnum::ECM_LEGAL),
            ],
        );
    }
    
    public function riskAction(): ResponseInterface
    {
        return $this->render(
            'workflow::dashboard',
            [
                'active' => 'workflow',
                'contracts' => $this->search(QueueFolderEnum::ECM_RISK),
            ],
        );
    }
    
    public function purchasingAction(): ResponseInterface
    {
        return $this->render(
            'workflow::dashboard',
            [
                'active' => 'workflow',
                'contracts' => $this->search(QueueFolderEnum::ECM_PURCHASING),
            ],
            );
    }
    
    public function vendorAction(): ResponseInterface
    {
        return $this->render(
            'workflow::dashboard',
            [
                'active' => 'workflow',
                'contracts' => $this->search(QueueFolderEnum::ECM_VENDOR),
            ],
            );
    }
    
    public function mayorAction(): ResponseInterface
    {
        return $this->render(
            'workflow::dashboard',
            [
                'active' => 'workflow',
                'contracts' => $this->search(QueueFolderEnum::ECM_MAYOR),
            ],
            );
    }
    
    private function search(QueueFolderEnum $case)
    {
        $contracts = $this->contractService->search([
            'ancestor_folder_id' => '336171280120',
            'template_key' => "ecm-application",
            'scope' => "enterprise_1328932288",
            'query' => "queue = :val",
            'query_params' => [
                'val' => $case->value,
            ],
        ]);
        
        return $contracts[0];
    }
    
    private function render(string $template, array $params): ResponseInterface
    {
        /**
         * Propagate all queue folder and permissions.
         * @var array $queues
         */
        $queues = [];
        
        foreach (QueueFolderEnum::cases() as $queue) {
            if ($this->authorizationService->isGranted($queue->name)) {
                $queues[] = $queue->name;
            }
        }
        
        $params['queues'] = $queues;
        
        
        
        return new HtmlResponse($this->template->render($template, $params));
    }
}