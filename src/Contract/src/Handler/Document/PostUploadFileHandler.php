<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Document;

use Core\Metadata\Instance\EcmApplication;
use Core\Metadata\Instance\SupportingDocumentation;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Form\UploadFileForm;
use Laminas\Diactoros\UploadedFile;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Folder;
use comcduarte\Box\API\Resource\Upload;
use Throwable;

class PostUploadFileHandler implements RequestHandlerInterface
{
    
    #[Inject(
        AccessTokenService::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        UploadFileForm::class,
        RouterInterface::class,
        )]
        public function __construct(
            protected AccessTokenService $accessTokenService,
            protected TemplateRendererInterface $template,
            protected FlashMessengerInterface $messenger,
            protected UploadFileForm $form,
            protected RouterInterface $router,
            ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = array_merge_recursive(
                $request->getParsedBody(),
                $request->getUploadedFiles(),
            );
            
            $this->form->setData($data);
            
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                
                $file_id = $request->getAttribute('id');
                $access_token = $this->accessTokenService->getAccessToken();
                $scope = 'enterprise';
                $template_key = 'ecm-application';
                
                /**
                 * Retrieve Metadata Instance on the file
                 */
                $instance = new EcmApplication($access_token);
                $result = $instance->get_metadata_instance_on_file($file_id, $scope, $template_key);
                
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
                
                $data['instance'] = [
                    'id' => $instance->getId(),
                    'queue' => $instance->getQueue(),
                    'contract-number' => $instance->getContractnumber(),
                    'PROJECT_NAME' => $instance->getProjectName(),
                ];
                
                
                $parent_folder = new Folder($access_token);
                $result = $parent_folder->get_folder_information($data['instance']['contract-number']);
                
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
                
                foreach ($parent_folder->item_collection->entries as $item)
                {
                    if ($item['name'] == 'SUPPORTING DOCUMENTATION') {
                        $supporting_documentation_folder_id = $item['id'];
                        break;
                    }
                }
                
                $data['contract-object'] = $parent_folder;
                $data['supporting-documentation'] = $supporting_documentation_folder_id;
                
                $upload = new Upload($access_token);
                
                
                $filename = sprintf('%s-%s', $data['DOCTYPE'], $data['FILE']->getClientFilename());
                $attributes = [
                    'name' => $filename,
                    'parent' => [
                        'id' => $supporting_documentation_folder_id,
                    ],
                ];
                
                $tmp_filename = $data['FILE']->getStream()->getMetadata('uri');
                
                /**
                 * @var $data UploadedFile
                 */
                $result = $upload->upload_file($attributes, $tmp_filename);
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
                
                $new_file_id = $result->entries[0]->id;
                
                /**
                 * SUPPORTING DOCUMENTATION
                 */
                $scope = 'enterprise';
                $template_key = 'supporting-documentation';
                $template_data = [
                    'document-type' => $data['DOCTYPE'],
                ];
                
                $instance = new SupportingDocumentation($access_token);
                $result = $instance->create_metadata_instance_on_file($new_file_id, $scope, $template_key, $template_data);
                
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
                
                return new RedirectResponse($this->router->generateUri('document::view-document', ['id' => $new_file_id]));
            } else {
                throw new ClientErrorException('Form is invalid.');
            }
            
        } catch (Throwable $e) {
            $this->messenger->addError($e->getMessage());
            return new RedirectResponse($this->router->generateUri('document::view-document', $request->getAttributes()));
        }
    }
}