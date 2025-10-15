<?php 
declare(strict_types=1);
namespace Frontend\Contract\Handler\Route;

use Core\Contract\Enum\QueueFolderEnum;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PostRouteContractHandler implements RequestHandlerInterface
{
    #[Inject(
        ContractServiceInterface::class,
    )]
    public function __construct(
        protected ContractServiceInterface $contractService,
    ){}
    
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        
        $destination = [];
        preg_match("/.*\/(\D*)\/.*/", $request->getRequestTarget(), $destination);
        
        $queue = null;
        foreach (QueueFolderEnum::cases() as $case) {
            if ($case->name === 'ECM_' . strtoupper($destination[1])) {
                $queue = $case;
                break;
            }
        }  
        
        if (!$queue) {
            throw new \Exception('Queue does not exist');
        }
        
        $this->contractService->move($id,(string) $queue->value);
        
        return new RedirectResponse($request->getServerParams()['HTTP_REFERER']);
    }
}