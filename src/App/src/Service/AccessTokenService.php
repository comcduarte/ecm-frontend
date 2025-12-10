<?php
declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\DependencyInjection\Attribute\Inject;
use comcduarte\Box\API\AccessToken;
use comcduarte\Box\API\AccessTokenAwareTrait;

class AccessTokenService implements AccessTokenServiceInterface
{
    use AccessTokenAwareTrait;
    
    #[Inject('config')]
    public function __construct(
        protected array $config
        )
    {
        $this->access_token = new AccessToken([
            'client_id' => $config['box-config']->boxAppSettings->clientID,
            'client_secret' => $config['box-config']->boxAppSettings->clientSecret,
            'public_key_id' => $config['box-config']->boxAppSettings->appAuth->publicKeyID,
            'private_key' => $config['box-config']->boxAppSettings->appAuth->privateKey,
            'passphrase' => $config['box-config']->boxAppSettings->appAuth->passphrase,
            'grant_type' => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            'box_subject_type' => "enterprise",
            'box_subject_id' => $config['box-config']->enterpriseID,
        ]);
    }
}
