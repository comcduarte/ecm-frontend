<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

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
        
        return new RedirectResponse($this->router->generateUri('create', ['action' => 'import']));
    }
}