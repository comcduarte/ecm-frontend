<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Document;

use Core\Metadata\Instance\EcmApplication;
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
use comcduarte\Box\API\Resource\File;
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
                    'project-name' => $instance->getProjectName(),
                ];
                
                /**
                 * Find contract object folder via file parents
                 */
                $file = new File($access_token);
                $result = $file->get_file_information(preg_replace('/[a-z]*_([0-9]*)/','$1',$instance->parent));
                
                $data['file'] = $file->getArrayCopy();
                
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
                
                for ($x = $file->path_collection->total_count; --$x; $x >= 0)
                {
                    /**
                     * If no entry exists, move on.
                     */
                    if (!isset($file->path_collection->entries[$x]['id'])) {
                        continue;
                    }
                    
                    $parent = $file->path_collection->entries[$x]['id'];
                    
                    /**
                     * If file is located within a SUPPORTING DOCUMENTATION folder
                     * look one level higher.
                     */
                    if ($file->path_collection->entries[$x]['name'] == 'SUPPORTING DOCUMENTATION') {
                        continue;
                    }
                    
                    /**
                     * If file was located within an amendment structure
                     * look one level higher.
                     */
                    if (preg_match('/^AM/',$file->path_collection->entries[$x]['name'])) {
                        continue;
                    }
                    
                    /**
                     * Contract Object Found
                     */
                    break;
                }
                
                $parent_folder = new Folder($access_token);
                $result = $parent_folder->get_folder_information($parent);
                
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
                
                
                $attributes = [
                    'name' => $data['FILE']->getClientFilename(),
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
                
                $new_file_id = $result->entries[0]['id'];
            }
            return new RedirectResponse($this->router->generateUri('document::upload-file', ['id' => $new_file_id]));
        } catch (Throwable $e) {
            $this->messenger->addError($e->getMessage());
            return new RedirectResponse($this->router->generateUri('document::upload-file', $request->getAttributes()));
        }
    }
}