<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Repository\SquadRepository;
use Armasquads\Repository\UserRepository;
use FilesystemIterator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class PageController extends Controller
{
    private const int LOGO_SIZE_CACHE_SECONDS = 3600;

    public function __construct(
        private readonly UserRepository $users,
        private readonly SquadRepository $squads,
        private readonly string $logoPath,
        private readonly string $newsPath,
    ) {
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $statistics = $this->squads->statistics();

        return self::render($request, $response, 'home', [
            'lastUser' => $this->users->findLatest(),
            'totalUsers' => $this->users->count(),
            'totalSquads' => $statistics['squads'],
            'totalSquadsMembers' => $statistics['members'],
        ]);
    }

    public function impressum(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return self::render($request, $response, 'impressum');
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return self::render($request, $response, 'dashboard', [
            'news' => $this->news(Context::of($request)->translator->language()),
        ]);
    }

    /**
     * News are files named <date>.<language>.phtml in templates/news; a post without translation is shown in English.
     *
     * @return list<string>
     */
    private function news(string $language): array
    {
        $posts = [];
        foreach (glob($this->newsPath . '/*.en.phtml') ?: [] as $file) {
            $post = basename($file, '.en.phtml');
            $posts[$post] = is_file("$this->newsPath/$post.$language.phtml") ? "news/$post.$language" : "news/$post.en";
        }
        krsort($posts);

        return array_values($posts);
    }

    public function support(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $statistics = $this->squads->statistics();

        return self::render($request, $response, 'support', [
            'data' => [
                'registered_users' => $this->users->count(),
                'total_squads' => $statistics['squads'],
                'total_squad_members' => $statistics['members'],
                'total_squad_logos' => $statistics['logos'],
                'total_squad_logos_size' => self::humanFileSize($this->logoDirectorySize()),
            ],
        ]);
    }

    /**
     * Walking all logo files takes a while, so the result is cached for an hour.
     */
    private function logoDirectorySize(): int
    {
        $cacheFile = sys_get_temp_dir() . '/armasquads-logo-size';
        if (is_file($cacheFile) && filemtime($cacheFile) > time() - self::LOGO_SIZE_CACHE_SECONDS) {
            return (int) file_get_contents($cacheFile);
        }

        $bytes = 0;
        if (is_dir($this->logoPath)) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->logoPath, FilesystemIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $bytes += $file->getSize();
            }
        }
        file_put_contents($cacheFile, (string) $bytes);

        return $bytes;
    }

    private static function humanFileSize(int $bytes): string
    {
        $factor = (int) floor((strlen((string) $bytes) - 1) / 3);

        return sprintf('%.2f', $bytes / 1024 ** $factor) . ' ' . ('BKMGTP'[$factor] ?? '');
    }
}
