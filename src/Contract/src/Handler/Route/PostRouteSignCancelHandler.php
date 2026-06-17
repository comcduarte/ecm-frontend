<?php
declare(strict_types = 1);
namespace Frontend\Contract\Handler\Route;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Form\SignContractModalForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\File;
use comcduarte\Box\API\Resource\MetadataInstance;
use comcduarte\Box\API\Resource\BoxSign\BoxSignRequest;
class PostRouteSignCancelHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        AccessTokenService::class,
        SignContractModalForm::class,
        FlashMessengerInterface::class,
        ContractServiceInterface::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected AccessTokenService $accessTokenService,
        protected SignContractModalForm $form,
        protected FlashMessengerInterface $messenger,
        protected ContractServiceInterface $contractService,
    ){}
        
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $file_id = $request->getAttribute('id');
        
        /**
         * Retrieve Metadata from the file
         * @var \comcduarte\Box\API\Resource\MetadataInstance $metadata
         */
        $metadata = new MetadataInstance($access_token);
        $instances = $metadata->list_metadata_instances_on_file($file_id);
        $sign_request_id = null;
        
        foreach ($instances->entries as $x) {
            switch ($x['$template']) {
                case 'boxSign':
                    $boxsign = new BoxSignRequest($access_token);
                    $result = $boxsign->get_box_sign_request_by_id($x['signId']);
                    $sign_request_id = $boxsign->getId();
                    break;
                default:
                    break;
            }
        }
        
        $result = $boxsign->cancel_box_sign_request($sign_request_id);
        if ($result instanceof ClientError) {
            $this->messenger->addError($result->message);
            throw new ClientErrorException($result->message);
        }
        
        $file = new File($access_token);
        $sign_files = $boxsign->getSignFiles();
        foreach ($sign_files['files'] as $sign_file) {
            $result = $file->delete_file($sign_file['id']);
            
            if ($result instanceof ClientError) {
                throw new ClientErrorException($result->message);
            }
        }
        
        if ($boxsign->getSigningLog() instanceof File) {
            $result = $file->delete_file($boxsign->getSigningLog()->id);
            
            if ($result instanceof ClientError) {
                throw new ClientErrorException($result->message);
            }
        }
        unset($file);
        
        $uri = $this->router->generateUri('workflow::dashboard', ['action' => 'index']);
        return new RedirectResponse($uri);
    }
}