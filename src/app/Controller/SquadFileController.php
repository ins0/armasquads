<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Repository\SquadRepository;
use Armasquads\Service\LogoService;
use Armasquads\Service\SquadFiles;
use Armasquads\Service\SquadXml;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;

/**
 * Public files the game loads from /user/squads/xml/<private id>/. These URLs are stored in player profiles.
 */
final readonly class SquadFileController
{
    public function __construct(
        private SquadRepository $squads,
        private LogoService $logos,
        private SquadFiles $files,
    ) {
    }

    /**
     * @param array{id: string} $args
     */
    public function xml(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $squad = $this->squads->findByPrivateId($args['id']);
        if ($squad === null) {
            return $response->withStatus(404);
        }

        $xml = $this->files->xml($squad);
        $response->getBody()->write($xml);

        return $response->withHeader('Content-Type', 'application/xml')->withHeader('Content-Length', (string) strlen($xml));
    }

    /**
     * Serves the squad logo for any <md5>.paa file name, like the old route did.
     *
     * @param array{id: string} $args
     */
    public function logo(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $squad = $this->squads->findByPrivateId($args['id']);
        $paaPath = $squad !== null && $this->logos->serverLogo($squad) !== null ? $this->logos->paaPath($squad) : null;
        if ($paaPath === null) {
            return $response->withStatus(404);
        }

        $paa = $this->logos->absolutePath($paaPath);

        return $response
            ->withBody(new Stream(fopen($paa, 'rb')))
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment; filename=' . basename($paa))
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', (string) filesize($paa));
    }

    public function dtd(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(SquadXml::dtd());

        return $response->withHeader('Content-Type', 'application/xml-dtd');
    }
}
