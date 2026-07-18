<?php

declare(strict_types=1);

namespace Frontend\Contract\Service;

use Core\Contract\Entity\Contract;
use Core\Contract\Repository\ContractRepository;
use Frontend\App\Exception\NotFoundException;
use comcduarte\Box\API\Resource\Comments;
use comcduarte\Box\API\Resource\Items;
use comcduarte\Box\API\Resource\MetadataInstances;
use comcduarte\Box\API\Enum\ResourceType;

interface ContractServiceInterface
{
    public function getContractRepository(): ContractRepository;

    public function deleteContract(
        Contract $contract,
    ): void;

    /**
     * @param array<non-empty-string, mixed> $params
     */
    public function getContracts(
        array $params,
    ): array;

    /**
     * @param array<non-empty-string, mixed> $data
     */
    public function saveContract(
        array $data,
        ?Contract $contract = null,
    ): Contract;

    public function createContract(array $data);
    
    public function generateContract(array $data, Contract $contract);
    
    /**
     * @throws NotFoundException
     */
    public function findContract(
        string $folder_id,
    ): Contract;
    
    public function uploadContract(array $data, string $tmp_filename);
    
    public function search(array $params): array;
    
    public function move(string $source, string $destination): bool;
    
    public function getMetadata(string $contract, string $template_key, string $scope = 'enterprise', ResourceType $type = ResourceType::File): MetadataInstances;
    
    /**
     * 
     * @see ContractService
     */
    public function getComments(string $file_id): Comments;

    public function getSupportingDocumentation(string $folder_id): Items;
    
    public function getAmendments(string $folder_id): Items;
}
