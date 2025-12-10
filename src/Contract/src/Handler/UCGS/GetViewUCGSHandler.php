<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\UCGS;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Contract\Service\UCGSServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetViewUCGSHandler implements RequestHandlerInterface
{
    #[Inject(
        UCGSServiceInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected UCGSServiceInterface $uCGSService,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        try {
            $uCGS = $this->uCGSService->findUCGS($request->getAttribute('uuid'));
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return new HtmlResponse(
            $this->template->render('u-cgs::view-u-cgs', [
                'uCGS' => $uCGS,
            ])
        );
    }
}
