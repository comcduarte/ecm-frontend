<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Document;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Form\CreateCommentForm;
use Frontend\Contract\Form\UploadFileForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\Comment;
use comcduarte\Box\API\Resource\File;
use comcduarte\Box\API\Resource\MetadataInstance;

class GetViewDocumentHandler implements RequestHandlerInterface
{
    #[Inject(
        AccessTokenService::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateCommentForm::class,
        UploadFileForm::class,
        RouterInterface::class,
        )]
    public function __construct(
        protected AccessTokenService $accessTokenService,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateCommentForm $addCommentForm,
        protected UploadFileForm $uploadFileForm,
        protected RouterInterface $router,
        ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $file_id = $request->getAttribute('id');
        /**
         * Get Document
         */
        $file = new File($access_token);
        $file->list_all_representations();
        $file->get_file_information($file_id);
        $file->request_desired_representation('pdf');
        $result = $file->download_file_representation();
        
        $pdf_data = '';
        if ($result instanceof ClientError) {
        } else {
            $pdf_data = $result->getBody();
        }
        
        /**
         * Get Comments
         */
        $comment = new Comment($access_token);
        $comments = $comment->list_file_comments($file_id);
        
        if ($comments instanceof ClientError)
        {
            throw new ClientErrorException($comments->message);
        }
        
        /**
         * Get Metadata Instances
         */
        $instance = new MetadataInstance($access_token);
        $instances = $instance->list_metadata_instances_on_file($file_id);
        
        /**
         * Upload File Form
         */
        $this->uploadFileForm->setAttribute('action', $this->router->generateUri('document::upload-file', $request->getAttributes()));
        $this->uploadFileForm->prepare();
        
        return new HtmlResponse(
            $this->template->render('document::view-document', [
                'active' => 'document',
                'image' => base64_encode($pdf_data),
                'comments' => $comments,
                'metadata_instances' => $instances,
                'form' => $this->addCommentForm->prepare(),
                'uploadform' => $this->uploadFileForm,
            ])
        );
    }
}