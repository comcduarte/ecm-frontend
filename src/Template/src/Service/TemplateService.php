<?php
declare(strict_types = 1);
namespace Frontend\Template\Service;

use Core\App\Message;
use Core\Template\Entity\Template;
use Core\Template\Repository\TemplateRepository;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Template\Exception\NotFoundException;
use Frontend\App\Service\AccessTokenService;

class TemplateService implements TemplateServiceInterface
{

    #[Inject(
        TemplateRepository::class,
        AccessTokenService::class,
        )]
    public function __construct(protected TemplateRepository $templateRepository, protected AccessTokenService $accessTokenService)
    {}

    public function getTemplateRepository(): TemplateRepository
    {
        return $this->templateRepository;
    }

    public function deleteTemplate(Template $template): void
    {
        $this->templateRepository->deleteResource($template);
    }

    /**
     *
     * @param
     *            array<non-empty-string, mixed> $params
     */
    public function getTemplates(): array
    {
        $access_token = $this->accessTokenService->getAccessToken();
        $templates = $this->templateRepository->getTemplates($access_token);
        return $templates;
    }

    /**
     *
     * @param
     *            array<non-empty-string, mixed> $data
     */
    public function saveTemplate(array $data, ?Template $template = null): Template
    {
        if (! $template instanceof Template) {
            $template = new Template();
        }

        $this->templateRepository->saveResource($template);

        return $template;
    }

    /**
     *
     * @throws NotFoundException
     */
    public function findTemplate(string $uuid): Template
    {
        $template = $this->templateRepository->find($uuid);
        if (! $template instanceof Template) {
            throw new NotFoundException(Message::resourceNotFound('Template'));
        }

        return $template;
    }
}
