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
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\BaseResource;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Comment;
use comcduarte\Box\API\Resource\Comments;
use comcduarte\Box\API\Resource\Items;
use comcduarte\Box\API\Resource\MetadataInstances;
use comcduarte\Box\API\Resource\DocGen\BoxDocGenJob;

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
        $access_token = $this->accessTokenService->getAccessToken();
        $contract = $this->contractRepository->createContract($data, $access_token);

        return $contract;
    }
    
    public function createContract(array $data)
    {
        /**
         * Create Contract Object Folder Structure
         */
        $access_token = $this->accessTokenService->getAccessToken();
        $contract = $this->contractRepository->createContract($data, $access_token);
        
        if ($contract instanceof ClientError) {
            throw new ClientErrorException("Unable to create contract object structure.");
        }
        
        /**
         * Assign Metadata Tags to Structure
         */
        $folder_id = $contract->getFolder_id();
        $scope = 'enterprise';
        $template_key = 'ecm-application';
        
        $metadata_instance = new EcmApplication($access_token);
        $instance = [
            'project-name' => $contract->getProject_name(),
            'queue' => $data['parent'],
        ];
        $result = $metadata_instance->create_metadata_instance_on_folder($folder_id, $scope, $template_key, $instance);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException("Unable to assign metadata template to folder.");
        }
        
        $destination_folder = new BaseResource();
        $destination_folder->setId($contract->getFolder_id());
        $destination_folder->setType('folder');
        
        $template = new BaseResource();
        $template->setId($data['TYPE']);
        $template->setType('file');
        
        $generated_file_name = sprintf('%d-%04d %s', date('Y'), 2, strtoupper($data['project-name']));
        $user_input = $data;
        $document_generation_data = [
            [
                'generated_file_name' => $generated_file_name,
                'user_input' => $user_input,
            ],
        ];
        
        $job = new BoxDocGenJob($access_token);
        $result = $job->generate_document(
            $destination_folder,
            $document_generation_data,
            $template,
            'api',
            'docx'
            );
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
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
    
    /**
     * 
     * {@inheritDoc}
     * @see \Frontend\Contract\Service\ContractServiceInterface::getComments()
     */
    public function getComments(string $file_id): Comments
    {
        $access_token = $this->accessTokenService->getAccessToken();
        
        $comment = new Comment($access_token);
        $comments = $comment->list_file_comments($file_id);
        
        if ($comments instanceof ClientError)
        {
            throw new ClientErrorException($comments->message);
        }
    
        return $comments; 
    }

    public function getSupportingDocumentation(string $folder_id): Items
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $params = [
            'contract-folder' => $folder_id,
        ];
        
        return $this->contractRepository->getSupportingDocumentation($params, $access_token);
    }
}
