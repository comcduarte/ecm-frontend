<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetViewContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
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

        $contract_file = $contract->getContract_file();
        $data = $contract_file->download_file($contract_file->id);
        
        /**
         * Populate Metadata
         */
        $metadata_instances = $this->contractService->getMetadata($contract->getContract_folder()->id);
        //-- parse instances and display with PDF
        
//         $metadata = [
//             'contract_file_id' => $contract->getContract_file()->id,
//             'contract_folder_id' => $contract->getContract_folder()->id,
//             'queue' => $contract->getContract_folder()->parent['id'],
//         ];
        
        /**
         * Populate Comments
         */
        $comments = $this->contractService->getComments($contract_file->id);
        
        /**
         * Supporting Documentation
         */
        $supporting_documentation = $this->contractService->getSupportingDocumentation($contract->getContract_folder()->id);
        
        return new HtmlResponse(
            $this->template->render('contract::view-contract', [
                'active' => 'document',
                'contract' => $contract,
                'image' => base64_encode($data->getBody()),
                'id' => $contract->getContract_folder()->id,
                'metadata_instances' => $metadata_instances,
                'comments' => $comments,
                'supporting_documentation' => $supporting_documentation,
            ])
        );
    }
}
