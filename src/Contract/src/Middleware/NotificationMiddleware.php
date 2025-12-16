<?php
declare(strict_types = 1);
namespace Frontend\Contract\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\Mail\Service\MailServiceInterface;
use Frontend\Contract\Service\ContractServiceInterface;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserRole;
use Frontend\User\Enum\UserStatusEnum;
use Laminas\Authentication\AuthenticationServiceInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Exception;
use Frontend\User\Entity\UserIdentity;

class NotificationMiddleware implements MiddlewareInterface
{

    #[Inject(
        TemplateRendererInterface::class,
        ContractServiceInterface::class,
        RouterInterface::class,
        MailServiceInterface::class,
        EntityManagerInterface::class,
        AuthenticationServiceInterface::class,
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected MailServiceInterface $mailService,
        protected EntityManagerInterface $entityManager,
        protected AuthenticationServiceInterface $authenticationService,
        
    ){}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * 
         * @var \Mezzio\Router\RouteResult $routeResult
         */
        $routeResult = $this->router->match($request);
        $route_name = $routeResult->getMatchedRouteName();
        $rolename = strtoupper(preg_replace('/^.*::(.*)$/', 'ECM_$1', $route_name));
        
        $matchedParams = $routeResult->getMatchedParams();
        $folder_id = $matchedParams['id'];
        
        /**
         * 
         * @var \Core\Contract\Entity\Contract $contract
         */
        $contract = $this->contractService->findContract($folder_id);
        
        $html = $this->template->render('notifications::contract-routed',[
            'contract_number' => $contract->getContract_folder()->getId(),
            'contract_name' => $contract->getContract_folder()->name,
        ]);
        
//         $html = '
//             <p>Please log into the ECM Application to review the document and to execute the appropriate actions.</p>
//         ';
        
        $this->mailService->setBody($html);
        $this->mailService->setSubject(sprintf('[ECM] Contract: %s', $contract->getContract_folder()->name));
        
        $active = UserStatusEnum::Active;
        $users = $this->entityManager->getRepository(User::class)->findAll();
        
        /**
         * @var User $user
         */
        foreach ($users as $user) {
            if ($user->getStatus() != $active) {
                continue;
            }
            
            $add = false;
            $roles = $user->getRoles();
            /**
             * @var UserRole $role
             */
            foreach ($roles as $role) {
                if ($role->getName() == 'ECM_NO_NOTIFICATIONS') {
                    $add = false;
                    continue 2;
                }
                
                if ($role->getName() == $rolename) {
                    $add = true;
                }
            }
            
            if ($add) {
                $this->mailService->getMessage()->addTo($user->getIdentity(), $user->getName());
            }
        }
        /**
         * 
         * @var UserIdentity $identity
         */
        $identity = $this->authenticationService->getIdentity();
        $this->mailService->getMessage()->addCc($identity->getIdentity());
        
        try {
            if (!$this->mailService->send()->isValid()) {
                throw new Exception ('Email unable to send.');
            }
            
        } catch (Exception $e) {
            throw new Exception ('Email unable to send.');
        }
       
        return $handler->handle($request);
    }
}
