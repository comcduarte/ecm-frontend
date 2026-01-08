<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\CC;

use Core\App\Message;
use Frontend\App\Common\Department;
use Core\Contract\Enum\QueueFolderEnum;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\ConflictException;
use Frontend\Contract\Form\CreateCCForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostCreateCCHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateCCForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateCCForm $createCCForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createCCForm
            ->setAttribute('action', $this->router->generateUri('cc::post-create'));

        try {
            $data = (array) $request->getParsedBody();
            $this->createCCForm->setData($data);
            if ($this->createCCForm->isValid()) {
                $data = $this->createCCForm->getData();
                
                $data['DOCTYPE'] = 'Contract';
                $data['CONTRACT_AMOUNT'] = '';
                $data['CONTRACT_END_DATE'] = '';
                
                //-- ExCESS_100K --//
                if ($data['EXCESS_100K']) {
                    $data['EXCESS_100K_Y'] = mb_chr(0x2611, 'UTF-8');
                    $data['EXCESS_100K_N'] = mb_chr(0x2610, 'UTF-8');
                } else {
                    $data['EXCESS_100K_Y'] = mb_chr(0x2610, 'UTF-8');
                    $data['EXCESS_100K_N'] = mb_chr(0x2611, 'UTF-8');
                }
                
                //-- GOV_USE --//
                if ($data['GOV_USE']) {
                    $data['GOV_USE_Y'] = mb_chr(0x2611, 'UTF-8');
                    $data['GOV_USE_N'] = mb_chr(0x2610, 'UTF-8');
                } else {
                    $data['GOV_USE_Y'] = mb_chr(0x2610, 'UTF-8');
                    $data['GOV_USE_N'] = mb_chr(0x2611, 'UTF-8');
                }
                
                $a = QueueFolderEnum::from($data['DEPARTMENT'])->name;
                $ref = new \ReflectionClass(Department::class);
                $data['DEPARTMENT_NAME'] = $ref->getConstant($a);
                
                /**
                 * Create Contract
                 */
                $contract = $this->contractService->createContract($data);
                $this->contractService->generateContract($data, $contract);
                
                $this->messenger->addSuccess('Contract Created');
                
                return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index']));
            }

            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form' => $this->createCCForm->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (ConflictException $exception) {
            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form'     => $this->createCCForm->prepare(),
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->logger->err('Create CC', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form'     => $this->createCCForm->prepare(),
                    'messages' => [
                        'error' => Message::AN_ERROR_OCCURRED,
                    ],
                ]),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
