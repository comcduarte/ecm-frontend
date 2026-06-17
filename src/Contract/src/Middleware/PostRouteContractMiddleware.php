<?php
declare(strict_types = 1);
namespace Frontend\Contract\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\Contract\Service\ContractServiceInterface;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserRole;
use Laminas\Authentication\AuthenticationServiceInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PostRouteContractMiddleware implements MiddlewareInterface
{
    #[Inject(
        TemplateRendererInterface::class,
        ContractServiceInterface::class,
        RouterInterface::class,
        EntityManagerInterface::class,
        AuthenticationServiceInterface::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected EntityManagerInterface $entityManager,
        protected AuthenticationServiceInterface $authenticationService,
        protected FlashMessengerInterface $messenger,
    ){}
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * Global Parameters
         */
        $routeResult = $this->router->match($request);
        $route_name = $routeResult->getMatchedRouteName();
        $rolename = strtoupper(preg_replace('/^.*::(.*)$/', 'ECM_$1', $route_name));
        
        $matchedParams = $routeResult->getMatchedParams();
        $folder_id = $matchedParams['id'];
        
        /**
         * Retrieve Contract
         * @var \Core\Contract\Entity\Contract $contract
         */
        $contract = $this->contractService->findContract($folder_id);
        
        /**
         * Retrieve Department
         */
        if ($rolename == 'ECM_DEPT') {
            /**
             * Set rolename to the dept the contract is in.
             */
            $rolename = strtoupper(preg_replace('/^.*([A-Za-z]{2})$/', 'ECM_$1', $contract->getContract_folder()->parent->name));
        }
        
        /**
         * NotificationMiddleware
         */
        $notification = [];
        $notification['body'] = $this->template->render('notifications::contract-routed',[
            'contract_number' => $contract->getContract_folder()->getId(),
            'contract_name' => $contract->getContract_folder()->name,
        ]);
        $notification['subject'] = sprintf('[ECM] Contract: %s', $contract->getContract_folder()->name);
        
        
        /**
         * Find all active users in responsible department
         * @var UserRole $role
         */
        $role = $this->entityManager->getRepository(UserRole::class)->findOneBy(['name' => $rolename]);
        
        /**
         * 
         * @var \Frontend\User\Repository\UserRepository $repo
         */
        $repo = $this->entityManager->getRepository(User::class);
        $users = $repo->findUsersByRole($role);
        $notification['users'] = array_column($users, 'identity');
        
        /**
         * Find logged in user
         * @var \Frontend\User\Entity\UserIdentity $identity
         */
        $identity = $this->authenticationService->getIdentity();
        $notification['logged_on_user'] = $identity->getIdentity();
        
        
        return $handler->handle($request->withAttribute('notification', $notification));
    }

}