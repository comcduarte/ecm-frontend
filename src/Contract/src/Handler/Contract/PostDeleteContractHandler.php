<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Contract\Form\DeleteContractForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Laminas\Diactoros\Response\RedirectResponse;

class PostDeleteContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        DeleteContractForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected DeleteContractForm $deleteContractForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        try {
            $contract = $this->contractService->findContract($request->getAttribute('id'));
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->deleteContractForm->setAttribute(
            'action',
            $this->router->generateUri('contract::delete-contract', ['id' => $request->getAttribute('id')])
        );

        try {
            $data = (array) $request->getParsedBody();
            $this->deleteContractForm->setData($data);
            if ($this->deleteContractForm->isValid()) {
                $this->contractService->deleteContract($contract);
                $this->messenger->addSuccess('Contract deleted successfully.');

//                 return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
                return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index']));
            }

            return new HtmlResponse(
                $this->template->render('contract::delete-contract-form', [
                    'form' => $this->deleteContractForm->prepare(),
                    'contract' => $contract,
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->messenger->addError(Message::AN_ERROR_OCCURRED);
            $this->logger->err('Delete Contract', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index']));
        }
    }
}
