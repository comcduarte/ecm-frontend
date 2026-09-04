<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\Contract\Form\SearchContractForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ContractSearchHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        SearchContractForm::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected SearchContractForm $form,
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $results = [];
        $params = $request->getQueryParams();
        
        $this->form->setAttribute('action', $this->router->generateUri('contract::contract-search'));
        
        if (isset($params['query'])) {
            $results = $this->contractService->getContracts($params);
        }
        
        
        return new HtmlResponse(
            $this->template->render('contract::contract-search',[
                'cabinet' => $results,
                'form' => $this->form->prepare(),
            ])
        );
    }
}