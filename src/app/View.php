<?php

declare(strict_types=1);

namespace Armasquads;

use Armasquads\Model\User;
use DateTimeInterface;
use IntlDateFormatter;
use Locale;
use Slim\Interfaces\RouteParserInterface;
use Throwable;

/**
 * Renders the PHP templates in templates/. Inside a template $this is the View, so all helpers below are available.
 */
final class View
{
    private const array FLASH_TYPES = [
        'default' => ['alert-warning', 'Warning'],
        'error' => ['alert-danger', 'Error'],
        'info' => ['alert-info', 'Information'],
        'success' => ['alert-success', 'Success'],
    ];

    /** @var list<string> */
    private array $titles = [];

    public function __construct(
        private readonly string $templatePath,
        private readonly RouteParserInterface $routeParser,
        private readonly Translator $translator,
        private readonly Session $session,
        private readonly ?User $identity,
        private readonly string $host,
        private readonly string $scheme,
    ) {
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function render(string $template, array $variables = []): string
    {
        $content = $this->partial($template, $variables);

        return $this->partial('layout', ['content' => $content]);
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function partial(string $template, array $variables = []): string
    {
        $file = $this->templatePath . '/' . $template . '.phtml';

        ob_start();
        try {
            (function () use ($file, $variables): void {
                extract($variables);
                require $file;
            })();
        } catch (Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        return (string) ob_get_clean();
    }

    public function translate(string $message): string
    {
        return $this->translator->translate($message);
    }

    /**
     * Translated and HTML escaped.
     */
    public function t(string $message): string
    {
        return $this->escapeHtml($this->translate($message));
    }

    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return $this->translator->translatePlural($singular, $plural, $count);
    }

    public function escapeHtml(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param array<string, string|int> $parameters
     * @param array<string, string|int> $query
     */
    public function url(string $route, array $parameters = [], array $query = []): string
    {
        return $this->routeParser->urlFor($route, array_map(strval(...), $parameters), $query);
    }

    public function basePath(?string $path = ''): string
    {
        return $path ? '/' . ltrim($path, '/') : '';
    }

    /**
     * Same host with another top level domain, used to switch between armasquads.de and armasquads.com.
     */
    public function serverUrl(?string $topLevelDomain = null): string
    {
        $host = $this->host;
        if ($topLevelDomain !== null) {
            $host = (string) preg_replace('#\.[a-z]+$#', '.' . $topLevelDomain, $host, 1);
        }

        return $this->scheme . '://' . $host;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function headTitle(string $title): void
    {
        $this->titles[] = $title;
    }

    public function renderTitle(): string
    {
        $titles = array_map($this->translate(...), [...$this->titles, $this->host]);

        return '<title>' . $this->escapeHtml(implode(' » ', $titles)) . '</title>';
    }

    public function locale(): string
    {
        return $this->translator->locale();
    }

    public function languageName(string $locale): string
    {
        return (string) Locale::getDisplayName($locale, $this->locale());
    }

    public function dateFormat(?DateTimeInterface $date): string
    {
        if ($date === null) {
            return '';
        }

        $formatter = new IntlDateFormatter($this->locale(), IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);

        return (string) $formatter->format($date);
    }

    public function identity(): ?User
    {
        return $this->identity;
    }

    public function csrfToken(): string
    {
        return $this->session->csrfToken();
    }

    public function csrfField(): string
    {
        return '<input type="hidden" name="csrf" value="' . $this->escapeHtml($this->csrfToken()) . '" />';
    }

    public function flashMessages(): string
    {
        $flashes = $this->session->takeFlashes();
        $html = '';

        foreach (self::FLASH_TYPES as $type => [$class, $heading]) {
            if (!empty($flashes[$type])) {
                $html .= $this->alert($class, $heading, $flashes[$type]);
            }
        }

        return $html;
    }

    /**
     * @param list<string> $errors
     */
    public function formErrors(array $errors): string
    {
        return $errors === [] ? '' : $this->alert('alert-danger', 'Error', $errors);
    }

    /**
     * @param list<string> $messages
     */
    private function alert(string $class, string $heading, array $messages): string
    {
        $items = array_map(fn (string $message): string => $this->escapeHtml($this->translate($message)), $messages);

        return '<div class="alert alert-dismissable ' . $class . '"><h4>' . $this->escapeHtml($this->translate($heading)) . '</h4>'
            . '<ul><li>' . implode('</li><li>', $items) . '</li></ul></div>';
    }
}
