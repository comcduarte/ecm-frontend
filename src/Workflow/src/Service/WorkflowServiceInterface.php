<?php

declare(strict_types=1);

namespace Frontend\Workflow\Service;

use Core\Workflow\Entity\Workflow;
use Core\Workflow\Repository\WorkflowRepository;
use Frontend\App\Exception\NotFoundException;

interface WorkflowServiceInterface
{
    public function getWorkflowRepository(): WorkflowRepository;

    public function deleteWorkflow(
        Workflow $workflow,
    ): void;

    /**
     * @param array<non-empty-string, mixed> $params
     */
    public function getWorkflows(
        array $params,
    ): array;

    /**
     * @param array<non-empty-string, mixed> $data
     */
    public function saveWorkflow(
        array $data,
        ?Workflow $workflow = null,
    ): Workflow;

    /**
     * @throws NotFoundException
     */
    public function findWorkflow(
        string $uuid,
    ): Workflow;
}
