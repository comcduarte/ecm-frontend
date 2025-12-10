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

class MetadataCorrectionMiddleware implements MiddlewareInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        EntityManagerInterface::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected EntityManagerInterface $entityManager,
    ){}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeResult = $this->router->match($request);
        $folder_id = $routeResult->getMatchedParams()['id'];

        try {
            $supporting_documentation = $this->contractService->getSupportingDocumentation($folder_id);
            $instance = new EcmApplication($this->contractService->accessTokenService->getAccessToken());
        } catch (ClientErrorException $e) {
            $supporting_documentation = new Items();
        }
        
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