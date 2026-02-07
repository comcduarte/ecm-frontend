<?php 
declare(strict_types=1);
namespace Frontend\Contract\Handler\Route;

use Core\Contract\Enum\QueueFolderEnum;
use Core\Metadata\Instance\Approval;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Service\ContractServiceInterface;
use Frontend\User\Entity\UserIdentity;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Folder;

class PostRouteContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        AccessTokenService::class,
        AuthenticationService::class,
        RouterInterface::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected AccessTokenService $accessTokenService,
        protected AuthenticationService $authenticationService,
        protected RouterInterface $router,
        protected FlashMessengerInterface $messenger,
    ){}
    
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $access_token = $this->accessTokenService->getAccessToken();
        
        $destination = [];
        preg_match("/.*\/(\D*)\/.*/", $request->getRequestTarget(), $destination);
        
        /**
         * Determine Source Queue
         */
        $folder = new Folder($access_token);
        $folder->get_folder_information($id);
        $queue = $folder->getParent();
        
        switch ($queue->name) {
            case 'RISK':
            case 'LEGAL':
            case 'PURCHASING':
            case 'VENDOR':
            case 'MAYOR':
                $queue_name = strtolower($queue->name);
                break;
            default:
                $this->messenger->addSuccess('Department has been notified.');
                return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'dept', 'dept' => $queue->id]));
                break;
        }
        
        $queue = null;
        foreach (QueueFolderEnum::cases() as $case) {
            if ($case->name === 'ECM_' . strtoupper($destination[1])) {
                $queue = $case;
                break;
            }
        }  
        
        if (!$queue) {
            throw new \Exception('Queue does not exist');
        }
        
        $this->contractService->move($id,(string) $queue->value);
        
        /**
         * Determine Contract File ID
         * @var string $contract_file_id
         */
        $contract_file_id = '';
        
        $items = $folder->list_items_in_folder($id);
        foreach ($items->entries as $item) {
            if ($item['type'] == 'file') {
                $contract_file_id = $item['id'];
            }
        }
        
        
        
        if ($this->authenticationService->hasIdentity()) {
            /**
             * 
             * @var UserIdentity $identity
             */
            $identity = $this->authenticationService->getIdentity();
        }
        
        /**
         * Sign Metadata
         */
        $scope = 'enterprise';
        $template_key = 'approval';
        $data = [
            [
                'op' => 'add',
                'path' => sprintf('/%s-approver-date', $queue_name),
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'op' => 'add',
                'path' => sprintf('/%s-approver', $queue_name),
                'value' => $identity->getIdentity(),
            ],
        ];
        
        $metadata_instance = new Approval($access_token);
        
        $result = $metadata_instance->update_metadata_instance_on_file($contract_file_id, $scope, $template_key, $data);
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index']));
    }
}