<?php
declare(strict_types=1);

namespace Frontend\Contract\Service;

use Core\Contract\Repository\ContractRepository;
use Core\Metadata\Instance\Amendment;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\App\Service\AccessTokenService;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Folder;
use comcduarte\Box\API\Resource\MetadataCascadePolicy;

class AmendmentService extends ContractService
{
    #[Inject(
        ContractRepository::class,
        AccessTokenService::class,
        'config',
    )]
    public function __construct(
        protected ContractRepository $contractRepository,
        public AccessTokenService $accessTokenService,
        protected array $config = [],
        ) {
    }
    
    public function getNewContractName(array $params): string
    {
        $contract_name = parent::getNewContractName($params);
        
        $matches = [];
        $pattern = '/^(\d{4})-(\d{4}) (.*)$/';
        preg_match($pattern, $contract_name, $matches);
        $assigned_number = $matches[2];
        
        
        $folder = new Folder($this->accessTokenService->getAccessToken());
        $folder->get_folder_information($params['CONTRACT_ID']);
        
        preg_match($pattern, $folder->name, $matches);
        $orig_year = $matches[1];
        $orig_num = $matches[2];
        
        return sprintf('%04d-%04d-AM%04d %s', $orig_year, $orig_num, $assigned_number, $params['PROJECT_NAME']);        
        
    }
    
    public function createContract(array $data)
    {
        $contract = parent::createContract($data);
        
        $access_token = $this->accessTokenService->getAccessToken();
        
        //-- Globals --//
        $folder_id = $contract->getFolder_id();
        $scope = 'enterprise';
        $template_key = 'amendment';
        
        //-- Create Metadata Cascade Policy --//
        $policy = new MetadataCascadePolicy($access_token);
        $result = $policy->create_metadata_cascade_policy($folder_id, $scope, $template_key);
        if ($result instanceof ClientError) {
            throw new ClientErrorException("Unable to create metadata cascade policy.");
        }
        
        //-- Create Metadata Instance --//
        $metadata_instance = new Amendment($access_token);
        $instance = [
            'amendment-number' => $data['CONTRACT_ID'],
        ];
        $result = $metadata_instance->create_metadata_instance_on_folder($folder_id, $scope, $template_key, $instance);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException("Unable to assign metadata template to folder.");
        }
        
        return $contract;
    }
}