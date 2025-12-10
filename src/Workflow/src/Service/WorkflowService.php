<?php

declare(strict_types=1);

namespace Frontend\Workflow\Service;

use Core\App\Helper\Paginator;
use Core\App\Message;
use Core\Workflow\Entity\Workflow;
use Core\Workflow\Repository\WorkflowRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\App\Exception\NotFoundException;

use function in_array;

class WorkflowService implements WorkflowServiceInterface
{
    #[Inject(
        WorkflowRepository::class,
    )]
    public function __construct(
        protected WorkflowRepository $workflowRepository,
    ) {
    }

    public function getWorkflowRepository(): WorkflowRepository
    {
        return $this->workflowRepository;
    }

    public function deleteWorkflow(
        Workflow $workflow,
    ): void {
        $this->workflowRepository->deleteResource($workflow);
    }

    /**
     * @param array<non-empty-string, mixed> $params
     */
    public function getWorkflows(
        array $params,
    ): array {
        $filters = $params['filters'] ?? [];
        $params  = Paginator::getParams($params, 'workflow.created');

        $sortableColumns = [
            'workflow.created',
            'workflow.updated',
        ];
        if (! in_array($params['sort'], $sortableColumns, true)) {
            $params['sort'] = 'workflow.created';
        }

        $paginator = new DoctrinePaginator($this->workflowRepository->getWorkflows($params, $filters)->getQuery());

        return Paginator::wrapper($paginator, $params, $filters);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    public function saveWorkflow(
        array $data,
        ?Workflow $workflow = null,
    ): Workflow {
        if (! $workflow instanceof Workflow) {
            $workflow = new Workflow();
        }

        $this->workflowRepository->saveResource($workflow);

        return $workflow;
    }

    /**
     * @throws NotFoundException
     */
    public function findWorkflow(
        string $uuid,
    ): Workflow {
        $workflow = $this->workflowRepository->find($uuid);
        if (! $workflow instanceof Workflow) {
            throw new NotFoundException(Message::resourceNotFound('Workflow'));
        }

        return $workflow;
    }
}
