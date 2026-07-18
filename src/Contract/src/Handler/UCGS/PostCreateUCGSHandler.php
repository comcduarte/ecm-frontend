<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\UCGS;

use Core\Contract\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\ConflictException;
use Frontend\Contract\Form\CreateUCGSForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Core\Contract\Entity\Contract;

class PostCreateUCGSHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateUCGSForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateUCGSForm $createUCGSForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createUCGSForm
            ->setAttribute('action', $this->router->generateUri('contract::post-ucgs-form'));

        try {
            $data = (array) $request->getParsedBody();
            $this->createUCGSForm->setData($data);
            if ($this->createUCGSForm->isValid()) {
                $data = $this->createUCGSForm->getData();
                
                $data['DOCTYPE'] = 'Contract';
                
                /**
                 * Create Contract
                 * @var Contract $contract
                 */
                $contract = $this->contractService->createContract($data);
                $this->contractService->generateContract($data, $contract);
                
                $this->messenger->addSuccess(Message::CONTRACT_CREATED);

                return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index', 'dept' => $contract->getContract_folder()->parent->id]));
            }

            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form' => $this->createUCGSForm->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (ConflictException $exception) {
            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form'     => $this->createUCGSForm->prepare(),
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->logger->err('Create UCGS', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form'     => $this->createUCGSForm->prepare(),
                    'messages' => [
                        'error' => Message::AN_ERROR_OCCURRED,
                    ],
                ]),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
