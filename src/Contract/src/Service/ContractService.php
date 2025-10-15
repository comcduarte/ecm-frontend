<?php

declare(strict_types=1);

namespace Frontend\Contract\Service;

use Core\App\Message;
use Core\Contract\Entity\Contract;
use Core\Contract\Repository\ContractRepository;
use Core\Metadata\Instance\EcmApplication;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\App\Exception\NotFoundException;
use Frontend\App\Service\AccessTokenService;
use comcduarte\Box\API\Resource\MetadataInstances;

class ContractService implements ContractServiceInterface
{
    #[Inject(
        ContractRepository::class,
        AccessTokenService::class,
        'config',
    )]
    public function __construct(
        protected ContractRepository $contractRepository,
        protected AccessTokenService $accessTokenService,
        protected array $config = [],
    ) {
    }

    public function getContractRepository(): ContractRepository
    {
        return $this->contractRepository;
    }

    public function deleteContract(
        Contract $contract,
    ): void {
        $this->contractRepository->deleteResource($contract);
    }

    /**
     * @param array<non-empty-string, mixed> $params
     */
    public function getContracts(
        array $params,
    ): array {
//         $filters = $params['filters'] ?? [];
//         $params  = Paginator::getParams($params, 'contract.created');

//         $sortableColumns = [
//             'contract.created',
//             'contract.updated',
//         ];
//         if (! in_array($params['sort'], $sortableColumns, true)) {
//             $params['sort'] = 'contract.created';
//         }

//         $paginator = new DoctrinePaginator($this->contractRepository->getContracts($params, $filters)->getQuery());

//         return Paginator::wrapper($paginator, $params, $filters);
        $params = [
            'application-folder' => $this->config['box-config']['application-folder'],
        ];
        
        return $this->contractRepository->getContracts($params, $this->accessTokenService->getAccessToken());
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    public function saveContract(
        array $data,
        ?Contract $contract = null,
    ): Contract {
        if (! $contract instanceof Contract) {
            $contract = new Contract();
        }

        $this->contractRepository->saveResource($contract);

        return $contract;
    }

    /**
     * @throws NotFoundException
     */
    public function findContract(
        string $uuid,
    ): Contract {
//         return $this->contractRepository->search($this->accessTokenService->getAccessToken());
        $contract = $this->contractRepository->find($uuid, $this->accessTokenService->getAccessToken());
        if (! $contract instanceof Contract) {
            throw new NotFoundException(Message::resourceNotFound('Contract'));
        }

        return $contract;
    }
    
    public function search(array $params): array
    {
        /**
         * Check Parameters
         */
        
        $access_token = $this->accessTokenService->getAccessToken();
        return $this->contractRepository->search($params, $access_token);
    }
    
    public function move(string $source, string $destination): bool
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $this->contractRepository->move($source, $destination, $access_token);
        return true;
    }
    
    public function getMetadata(string $contract): MetadataInstances
    {
        $instances = new MetadataInstances();
        $access_token = $this->accessTokenService->getAccessToken();
        
        $source = $contract;
        $scope = 'enterprise';
        $template_key = 'ecm-application';
        
        $metadata_instance = new EcmApplication($access_token);
        $metadata_instance->get_metadata_instance_on_folder($source, $scope, $template_key);
        
        $instances->entries[] = $metadata_instance;
        
        return $instances;
    }
}
