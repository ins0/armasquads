<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Repository\SquadRepository;
use Armasquads\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PageController extends Controller
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly SquadRepository $squads,
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
}
