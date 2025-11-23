<?php

declare(strict_types=1);

namespace Frontend\Contract\Service;

use Core\App\Message;
use Core\Contract\Entity\Contract;
use Core\Contract\Repository\ContractRepository;
use Core\Metadata\Instance\Contract as ContractInstance;
use Core\Metadata\Instance\EcmApplication;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\App\Exception\NotFoundException;
use Frontend\App\Service\AccessTokenService;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\BaseResource;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Comment;
use comcduarte\Box\API\Resource\Comments;
use comcduarte\Box\API\Resource\File;
use comcduarte\Box\API\Resource\Folder;
use comcduarte\Box\API\Resource\Items;
use comcduarte\Box\API\Resource\MetadataInstances;
use comcduarte\Box\API\Resource\Upload;
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
    
    public function getNewContractName(array $params): string
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $instances = $this->getMetadata($this->config['box-config']['application-folder']);
        
        if ($instances instanceof ClientError) {
            //-- Do Something --//
        }
        
        $integer = (string) $instances->entries[0]->getContractNumber();
        
        /**
         * Update Metadata Tag to increment Number
         */
        $folder_id = $this->config['box-config']['application-folder'];
        $scope = 'enterprise_' . $this->config['access-token-config']->enterpriseID;
        $template_key = 'ecm-application';
        $integer++;
        $data = [
            [
                'op' => 'replace',
                'path' => '/contract-number',
                'value' => "$integer",
            ],
        ];
        
        $metadata_instance = new EcmApplication($access_token);
        
        $result = $metadata_instance->get_metadata_instance_on_folder($folder_id, $scope, $template_key);
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        $result = $metadata_instance->update_metadata_instance_on_folder($folder_id, $scope, $template_key, $data);
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        
        return sprintf('%d-%04d %s', date('Y'), $integer, strtoupper($params['PROJECT_NAME']));
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
        $data['contract-name'] = $this->getNewContractName($data);
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
            'contract-number' => $folder_id,
        ];
        $result = $metadata_instance->create_metadata_instance_on_folder($folder_id, $scope, $template_key, $instance);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException("Unable to assign metadata template to folder.");
        }
        
        return $contract;
    }

    public function generateContract(array $data, Contract $contract)
    {
        $access_token = $this->accessTokenService->getAccessToken();
        
        $destination_folder = new BaseResource();
        $destination_folder->setId($contract->getFolder_id());
        $destination_folder->setType('folder');
        
        $template = new BaseResource();
        $template->setId($data['TYPE']);
        $template->setType('file');
        
        /**
         * Populate Entity Information
         */
        $generated_file_name = $contract->getProject_name();
        
        switch ($data['ENTITY']) {
            case 'City of Middletown':
                break;
            case 'Russell Library Company':
                $data['ENTITY_NAME'] = 'Russell Library Company';
                $data['ENTITY_ALIAS'] = 'the Library';
                break;
            default:
                $data['ENTITY_NAME'] = 'City of Middletown and Russell Library Company';
                $data['ENTITY_ALIAS'] = 'the City';
                break;
        }
        
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
        
        /**
         * This is being used until a check-batch method can be created.
         * It takes practically no time for the document to be generated.
         */
        sleep(10);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        /**
         * Find File
         */
        $folder = new Folder($access_token);
        $items = $folder->list_items_in_folder($contract->getFolder_id());
        
        if ($items instanceof ClientError) {
            throw new ClientErrorException($items->message);
        }
        
        $found_file = false;
        foreach ($items->entries as $item) {
            if ($item['type'] == 'file') {
                $contract_file = new File($access_token);
                $contract_file->setId($item['id'])->setType('file');
                $contract->setContract_file($contract_file);
                $found_file = true;
                break;
            };
        }
        
        if (!$found_file) {
            throw new ClientErrorException('Unable to find file.');
        }
        
        $metadata = [
            'file_id' => $contract->getContract_file()->getId(),
            'folder_id' => $contract->getFolder_id(),
            'approval' => [],
            'contract' => [
                'coi-expiration' => '',
                'contract-amount' => $data['CONTRACT_AMOUNT'],
                'contract-end-date' => $data['CONTRACT_END_DATE'],
                'contract-status' => '',
                'document-type' => $data['DOCTYPE'],
            ],
            'ecm-application' => [
                'project-name' => $data['PROJECT_NAME'],
                'queue' => '',
                'contract-number' => $contract->getFolder_id(),
            ],
            
        ];
        
        $this->setMetadata($metadata);
        
        return;
    }
    
    /**
     * $contract->getContract_file()->getId()
     * {@inheritDoc}
     * @see \Frontend\Contract\Service\ContractServiceInterface::findContract()
     */
    public function findContract(string $folder_id): Contract 
    {
        $contract = $this->contractRepository->find($folder_id, $this->accessTokenService->getAccessToken());
        if (! $contract instanceof Contract) {
            throw new NotFoundException(Message::resourceNotFound('Contract'));
        }
        return $contract;
    }
    
    public function uploadContract(array $data, string $tmp_filename)
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $upload = new Upload($access_token);
        
        $result = $upload->upload_file($data, $tmp_filename);
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        /**
         * 
         * @var File $file
         */
        $file = $result->entries[0];
        
        $metadata = [
            'file_id' => $file->getId(),
            'folder_id' => $file->parent->getId(),
            'approval' => [],
            'contract' => [
                'coi-expiration' => '',
                'contract-amount' => '',
                'contract-end-date' => '',
                'contract-status' => '',
                'document-type' => '',
            ],
            'ecm-application' => [
                'project-name' => $data['name'],
                'queue' => '',
                'contract-number' => $file->parent->getId(),
            ],
            
        ];
        
        $this->setMetadata($metadata);
        return;
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
    
    public function setMetadata(array $metadata)
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $file_id = $metadata['file_id'];
        $folder_id = $metadata['folder_id'];
        
        /**
         * ECM APPLICATION
         */
        $data = $metadata['ecm-application'];
        $scope = 'enterprise';
        $template_key = 'ecm-application';
        $template_data = [
            'project-name' => $data['project-name'],
            'queue' => '',
            'contract-number' => $folder_id,
        ];
        
        $instance = new EcmApplication($access_token);
        $result = $instance->create_metadata_instance_on_file($file_id, $scope, $template_key, $template_data);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        /**
         * CONTRACT
         */
        $data = $metadata['contract'];
        $scope = 'enterprise';
        $template_key = 'contract';
        $template_data = [
            'coi-expiration' => '',
            'contract-amount' => $data['contract-amount'],
            'contract-end-date' => $data['contract-end-date'],
            'contract-status' => '',
            'document-type' => $data['document-type'],
        ];
        
        $instance = new ContractInstance($access_token);
        $result = $instance->create_metadata_instance_on_file($file_id, $scope, $template_key, $template_data);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        /**
         * APPROVAL
         */
        $data = $metadata['approval'];
        $scope = 'enterprise';
        $template_key = 'approval';
        $template_data = [
            'department-approver' =>  '',
            'department-approver-date' =>  '',
            'legal-approver' =>  '',
            'legal-approver-date' =>  '',
            'risk-approver' =>  '',
            'risk-approver-date' =>  '',
            'purchasing-approver' =>  '',
            'purchasing-approver-date' =>  '',
            'mayor-approver' =>  '',
            'mayor-approver-date' =>  '',
            'vendor-approver' =>  '',
            'vendor-approver-date' =>  '',
        ];
        
        $instance = new ContractInstance($access_token);
        $result = $instance->create_metadata_instance_on_file($file_id, $scope, $template_key, $template_data);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        return;
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
