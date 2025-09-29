<?php

declare(strict_types=1);

namespace Frontend\Contract\Service;

use Core\Contract\Entity\Contract;
use Core\Contract\Repository\ContractRepository;
use Frontend\App\Exception\NotFoundException;

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

    /**
     * @throws NotFoundException
     */
    public function findContract(
        string $uuid,
    ): Contract;
}
