<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\UCLTS;

use Core\Contract\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\ConflictException;
use Frontend\Contract\Form\CreateUCLTSForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostCreateUCLTSHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateUCLTSForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateUCLTSForm $createUCLTSForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createUCLTSForm
            ->setAttribute('action', $this->router->generateUri('contract::post-uclts-form'));
            
        try {
            $data = (array) $request->getParsedBody();
            $this->createUCLTSForm->setData($data);
            
            if ($this->createUCLTSForm->isValid()) {
                $data = $this->createUCLTSForm->getData();
                
                /**
                 * Custom Logic
                 */
                $data['DOCTYPE'] = 'Contract';
                
                /**
                 * Checkboxes
                 */
                for ($i = 0; $i < 5; $i++) {
                    $data['INFO']['OPTIONS']['CHECK_' . $i] = '[ ]';
                }
                
                for ($i = 0; $i < 5; $i++) {
                    if (isset($data['INFO']['OPTIONS'][$i])) {
                        $z = $data['INFO']['OPTIONS'][$i];
                        $data['INFO']['OPTIONS']['CHECK_' . $z] = '[X]';
                        
                        //-- CHRO --//
                        if ($data['INFO']['OPTIONS'][$i] == 4) {
                            $data['INFO']['OPTIONS']['CHECK_CHRO'] = 'TRUE';
                        }
                    }
                }
                
                $contract = $this->contractService->createContract($data);
                $this->contractService->generateContract($data, $contract);
                
                $this->messenger->addSuccess(Message::CONTRACT_CREATED);

                return new RedirectResponse($this->router->generateUri('workflow::dashboard', ['action' => 'index']));
            }

            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form' => $this->createUCLTSForm->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (ConflictException $exception) {
            return new HtmlResponse(
                $this->template->render('contract::create-uclts-form', [
                    'form'     => $this->createUCLTSForm->prepare(),
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->logger->err('Create UCLTS', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
//                 'trace' => $exception->getTraceAsString(),
            ]);

            return new HtmlResponse(
                $this->template->render('contract::create-contract-form', [
                    'form'     => $this->createUCLTSForm->prepare(),
                    'messages' => [
                        'error' => Message::AN_ERROR_OCCURRED,
                    ],
                ]),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
