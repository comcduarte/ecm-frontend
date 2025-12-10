<?php
declare(strict_types = 1);
namespace Frontend\Template\Service;

use Core\Template\Entity\Template;
use Core\Template\Repository\TemplateRepository;
use Frontend\Template\Exception\NotFoundException;

interface TemplateServiceInterface
{

    public function getTemplateRepository(): TemplateRepository;

    public function deleteTemplate(Template $template): void;

    public function getTemplates(): array;

    /**
     *
     * @param
     *            array<non-empty-string, mixed> $data
     */
    public function saveTemplate(array $data, ?Template $template = null): Template;

    /**
     *
     * @throws NotFoundException
     */
    public function findTemplate(string $uuid): Template;
}
