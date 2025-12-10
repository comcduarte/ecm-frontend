<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\CreateUCGSForm;
use Frontend\Contract\Form\CreateUCLaborForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateContractFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateUCGSForm::class,
        CreateUCLaborForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateUCGSForm $createUCGSForm,
        protected CreateUCLaborForm $createUCLaborForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        
        switch ($request->getAttribute('form')) {
            case 'ucgs':
                //-- Uniform Contract for Goods and Services --//
                $form = $this->createUCGSForm;
                break;
            case 'uclts':
                //-- Uniform Contract for Labor and Trade over $100K --//
                $form = $this->createUCLaborForm;
                break;
            case 'cc':
                //-- Cooperative Contract --//
            default:
                break;
                
        }
        
       $form
            ->setAttribute('action', $this->router->generateUri('contract::create-contract'));

        return new HtmlResponse(
            $this->template->render('contract::create-contract-form', [
                'form' => $form->prepare(),
            ])
        );
    }
}
