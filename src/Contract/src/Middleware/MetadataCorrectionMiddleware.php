<?php
declare(strict_types = 1);
namespace Frontend\Contract\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Service\ContractServiceInterface;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\Items;
use Core\Metadata\Instance\EcmApplication;
use comcduarte\Box\API\Resource\ClientError;
use Laminas\Validator\Identical;
use Dot\Log\Logger;
use comcduarte\Box\API\Resource\MetadataCascadePolicy;
use comcduarte\Box\API\Enum\ConflictResolutionType;

class MetadataCorrectionMiddleware implements MiddlewareInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        EntityManagerInterface::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected EntityManagerInterface $entityManager,
        protected Logger $logger,
    ){}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeResult = $this->router->match($request);
        $folder_id = $routeResult->getMatchedParams()['id'];
        $scope = 'enterprise';
        $accessToken = $this->contractService->accessTokenService->getAccessToken();

        try {
            $supporting_documentation = $this->contractService->getSupportingDocumentation($folder_id);
        } catch (ClientErrorException $e) {
            $supporting_documentation = new Items();
        }
        
        /**
         * Required instances on contract folder
         */
        $instance = new EcmApplication($accessToken);
        $template_key = 'ecm-application';
        $result = $instance->get_metadata_instance_on_folder($folder_id, $scope, $template_key);
        if ($result instanceof ClientError) {
            $this->logger->err($result->message);
            $data = [
                'contract-number' => $folder_id,
                'project-name' => '',
                
            ];
            $result = $instance->create_metadata_instance_on_folder($folder_id, $scope, $template_key, $data);
            if ($result instanceof ClientError) {
                $this->logger->err($result->message);
                throw new ClientErrorException($result->message);
            }
        }
        
        /**
         * Format of Contract Number
         */
        $identical = new Identical($folder_id);
        
        if(!$identical->isValid($instance->getContractnumber())) {
            $instance->setContractnumber($folder_id);
        }
        
        /**
         * Metadata Cascade Policy
         */
        $metadata_cascade_policy = new MetadataCascadePolicy($accessToken);
        $policies = $metadata_cascade_policy->list_metadata_cascade_policies($folder_id);
        
        $create_policy = true;
        $identical = new Identical($template_key);
        /**
         * @var MetadataCascadePolicy $policy
         */
        foreach ($policies->entries as $policy) {
            if ($policy && $identical->isValid($policy['templateKey'])) {
                $create_policy = false;
            }
        }
        
        if ($create_policy) {
            $result = $metadata_cascade_policy->create_metadata_cascade_policy($folder_id, $scope, $template_key);
            if ($result instanceof ClientError) {
                throw new ClientErrorException($result->message);
            }
            
            $conflict_resolution = ConflictResolutionType::Overwrite;
            $result = $metadata_cascade_policy->force_apply_metadata_cascade_policy_to_folder($metadata_cascade_policy->getId(), $conflict_resolution);
            if ($result instanceof ClientError) {
                throw new ClientErrorException($result->message);
            }
        }
        
        /**
         * Process all supporting documentation
         */
        foreach ($supporting_documentation->entries as $file) {
            $result = $instance->get_metadata_instance_on_file($file['id'], 'enterprise', 'ecm-application');
            
            if ($result instanceof ClientError) {
                throw new ClientErrorException($result->message);
            }
            
            $identical = new Identical($folder_id);
            
            
            if (!$identical->isValid($instance->getContractnumber())) {
                $instance->setContractnumber($folder_id);
                
                $data = [
                    [
                        'op' => 'replace',
                        'path' => '/contract-number',
                        'value' => $folder_id,
                    ],
                ];
                
                $result = $instance->update_metadata_instance_on_file($file['id'], 'enterprise', 'ecm-application', $data);
                
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
            }
        }

        return $handler->handle($request);
    }
}