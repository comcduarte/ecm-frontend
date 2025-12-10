<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
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

class GetEditContractFormHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        EditContractForm::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected EditContractForm $editContractForm,
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
            )
            ->bind($contract);

        return new HtmlResponse(
            $this->template->render('contract::edit-contract-form', [
                'form' => $this->editContractForm->prepare(),
                'contract' => $contract,
            ])
        );
    }
}
