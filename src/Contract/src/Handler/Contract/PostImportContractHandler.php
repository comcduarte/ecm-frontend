<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Core\Contract\Entity\Contract;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Frontend\Contract\Form\UploadFileForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostImportContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        UploadFileForm::class,
        'dot-log.default_logger',
        )]
        public function __construct(
            protected ContractServiceInterface $contractService,
            protected RouterInterface $router,
            protected TemplateRendererInterface $template,
            protected FlashMessengerInterface $messenger,
            protected UploadFileForm $uploadFileForm,
            protected Logger $logger,
            ) {
        }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = array_merge_recursive(
                $request->getParsedBody(),
                $request->getUploadedFiles(),
                );
            
            $this->uploadFileForm->setData($data);
            
            if ($this->uploadFileForm->isValid()) {
                $data = $this->uploadFileForm->getData();
                
                /**
                 * Create Contract Folder Object
                 * @var Contract $contract
                 * contract-name,parent
                 */
                $contract = $this->contractService->createContract($data);
//                 $this->contractService->generateContract($data, $contract);
                
                
                /**
                 * Upload File to Primary Document Folder
                 */
                $filename = sprintf('%s.%s', $contract->getProject_name(), pathinfo($data['FILE']->getClientFilename(), PATHINFO_EXTENSION));
                $tmp_filename = $data['FILE']->getStream()->getMetadata('uri');
                
                $data = [
                    'name' => $filename,
                    'parent' => [
                        'id' => $contract->getFolder_id(),
                    ],
                ];
                
                $this->contractService->uploadContract($data, $tmp_filename);
                
                $this->messenger->addSuccess('Success');
            } else {
                $this->messenger->addInfo('Invalid');
            }
        } catch (Throwable $exception) {
            $this->logger->err('Import Contract', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
            
            $this->messenger->addError('Exception');
        }
        
        return new RedirectResponse($this->router->generateUri('contract::import-contract-form', ['action' => 'import']));
    }
}