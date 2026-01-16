<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Amendment;

use Core\Contract\Enum\QueueFolderEnum;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Common\Department;
use Frontend\Contract\Form\CreateAmendmentForm;
use Frontend\Contract\Service\AmendmentService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostCreateAmendmentFormHandler implements RequestHandlerInterface
{
    #[Inject(
        AmendmentService::class,
        RouterInterface::class,
        CreateAmendmentForm::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected AmendmentService $contractService,
        protected RouterInterface $router,
        protected CreateAmendmentForm $form,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected Logger $logger,
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = array_merge_recursive($request->getParsedBody(), $request->getUploadedFiles());
            $this->form->setData($data);
            
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                
                /**
                 * Data Manipulation
                 */
                
                //-- DEPARTMENT NAME --//
                $a = QueueFolderEnum::from($data['DEPARTMENT'])->name;
                $ref = new \ReflectionClass(Department::class);
                $data['DEPARTMENT_NAME'] = $ref->getConstant($a);
                
                //-- AMENDMENT NUMBER --//
//                 $data['AMENDMENT_NUM'] = 1;
                
                //-- COMPLETION_DATE --//
                $data['COMPLETION_DATE'] = $data['DATE']['END_DATE'];
                
                //-- REQUIRED FIELDS --//
                $data['DOCTYPE'] = 'Contract';
                $data['CONTRACT_AMOUNT'] = '';
                $data['CONTRACT_END_DATE'] = '';
                
                $contract = $this->contractService->createContract($data);
                
                /**
                 * Document Generation
                 */
                $this->contractService->generateContract($data, $contract);
                
                /**
                 * File Upload
                 */
//                 $data = [
//                     'name' => $filename,
//                     'parent' => [
//                         'id' => $contract->getFolder_id(),
//                     ],
//                 ];
//                 $tmp_filename = $data['FILE']->getStream()->getMetadata('uri');
//                 $filename = sprintf('%s.%s', $contract->getProject_name(), pathinfo($data['FILE']->getClientFilename(), PATHINFO_EXTENSION));
//                 $this->contractService->uploadContract($data, $tmp_filename);
                
                $this->messenger->addSuccess('Success');
                
                return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index']));
            } else {
                return new HtmlResponse(
                    $this->template->render('amendment::create-form', [
                        'form' => $this->form->prepare(),
                    ]),
                    StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
                    );
            }
            
        } catch (Throwable $e) {
            $this->logger->err('Import Amendment', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->messenger->addError($e->getMessage());
            
            return new HtmlResponse(
                $this->template->render('amendment::create-form', [
                    'form' => $this->form->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
                );
        }
    }
}