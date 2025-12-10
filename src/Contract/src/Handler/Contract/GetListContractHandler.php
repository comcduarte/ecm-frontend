<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetListContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        TemplateRendererInterface::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected TemplateRendererInterface $template,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        return new HtmlResponse(
            $this->template->render('contract::list-contract', [
                'cabinet' => $this->contractService->getContracts($request->getQueryParams()),
            ])
        );
    }
}
