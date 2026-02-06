<?php
declare(strict_types=1);

namespace Frontend\Page\Controller;

use Core\Contract\Entity\Contract;
use Core\Contract\Enum\QueueFolderEnum;
use Dot\Authorization\AuthorizationInterface;
use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

class DashboardController extends AbstractActionController
{
    #[Inject(
        TemplateRendererInterface::class,
        ContractServiceInterface::class,
        AuthorizationInterface::class,
        'config',
        )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected ContractServiceInterface $contractService,
        protected AuthorizationInterface $authorizationService,
        protected array $config,
        ){}
    
    public function deptAction(): ResponseInterface
    {
        $contracts = [];
        
        foreach (QueueFolderEnum::cases() as $queue) {
            if ($this->authorizationService->isGranted($queue->name)) {
                $x = $this->contractService->search([
                    'ancestor_folder_id' => QueueFolderEnum::ECM_ONBASE->value,
                    'template_key' => "permission",
                    'scope' => "enterprise_" . $this->config['box-config']->enterpriseID,
                    'query' => "(department = :val AND item.type = :type) AND item.name <> :suppdoc",
                    'query_params' => [
                        'val' => $queue->value,
                        'type' => 'folder',
                        'suppdoc' => 'SUPPORTING DOCUMENTATION',
                    ],
                ]);
                array_push($contracts, $x[0]);
            }
        }
        
        /**
         * @var Contract $contract
         */
        foreach ($contracts[0] as $contract) {
            switch ($contract->getContract_folder()->getParent()->name) {
                case 'RISK':
                    $x = 20;
                    break;
                case 'LEGAL':
                    $x = 40;
                    break;
                case 'PURCHASING':
                    $x = 60;
                    break;
                case 'MAYOR':
                    $x = 80;
                    break;
                default:
                    $x = 0;
            }
            
            $contract->setProgress($x);
        }
        

        return new HtmlResponse($this->template->render(
            'dashboards::dept',
            ['contracts' => $contracts]
            ));
    }
}