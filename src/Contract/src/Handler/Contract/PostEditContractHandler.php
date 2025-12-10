<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\BadRequestException;
use Frontend\App\Exception\ConflictException;
use Frontend\App\Exception\NotFoundException;
use Frontend\Contract\Form\EditContractForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostEditContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        EditContractForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected EditContractForm $editContractForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        try {
            $contract = $this->contractService->findContract($request->getAttribute('uuid'));
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->editContractForm
            ->setAttribute(
                'action',
                $this->router->generateUri('contract::edit-contract', ['uuid' => $contract->getUuid()->toString()])
            );

        try {
            $data = (array) $request->getParsedBody();
            $this->editContractForm->setData($data);
            if ($this->editContractForm->isValid()) {
                $data = (array) $this->editContractForm->getData();
                $this->contractService->saveContract($data, $contract);
                $this->messenger->addSuccess(Message::CONTRACT_UPDATED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('contract::edit-contract-form', [
                    'form' => $this->editContractForm->prepare(),
                    'contract' => $contract,
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (BadRequestException | ConflictException | NotFoundException $exception) {
            return new HtmlResponse(
                $this->template->render('contract::edit-contract-form', [
                    'form' => $this->editContractForm->prepare(),
                    'contract' => $contract,
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->messenger->addError(Message::AN_ERROR_OCCURRED);
            $this->logger->err('Update Contract', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new EmptyResponse(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
