<?php
declare(strict_types = 1);
namespace Frontend\Contract\Middleware;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Dot\Mail\Service\MailServiceInterface;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Exception;

class NotificationMiddleware implements MiddlewareInterface
{

    #[Inject(
        RouterInterface::class,
        MailServiceInterface::class,
        FlashMessengerInterface::class,
        'dot-log.default_logger',
        'config',
    )]
    public function __construct(
        protected RouterInterface $router,
        protected MailServiceInterface $mailService,
        protected FlashMessengerInterface $messenger,
        protected Logger $logger,
        protected array $config = [],
    ){}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $notification = $request->getAttribute('notification');
        
        try {
            $this->mailService->setBody($notification['body']);
            $this->mailService->setSubject($notification['subject']);
            
            
            $this->mailService->getMessage()
            ->addTo($notification['users'])
            ->addCc($notification['logged_on_user']);
        } catch (Exception $e) {
            $this->messenger->addError($e->getMessage());
            $this->logger->err($e->getMessage());
        }
        
        try {
            if (!$this->mailService->send()->isValid()) {
                throw new Exception ('Email unable to send.');
            }
        } catch (Exception $e) {
            $this->messenger->addError($e->getMessage());
            throw new Exception ('Email unable to send.');
            $this->logger->err($e->message);
        }
        
        return $handler->handle($request);
    }
}
