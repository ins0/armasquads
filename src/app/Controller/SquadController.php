<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Model\Squad;
use Armasquads\Repository\SquadRepository;
use Armasquads\Service\LogoService;
use Armasquads\Service\SquadFiles;
use Armasquads\Validation\SquadInput;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\Stream;

final class SquadController extends Controller
{
    public function __construct(
        private readonly SquadRepository $squads,
        private readonly LogoService $logos,
        private readonly SquadFiles $files,
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $squads = $this->squads->findByUser(Context::of($request)->user()->id);

        return self::render($request, $response, 'squads/index', [
            'squads' => $squads,
            'memberCounts' => $this->squads->memberCounts(array_map(static fn (Squad $squad): int => (int) $squad->id, $squads)),
            'logos' => $this->logos,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        $values = [];
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $values = self::post($request);
            $input = SquadInput::fromArray($values);
            $logo = self::uploadedLogo($request);
            $validator = $input->validate($logo);

            if (!self::isValidCsrf($request)) {
                $errors = ['ERROR_FORM_CSRF'];
            } elseif (!$validator->isValid()) {
                $errors = $validator->messages();
            } else {
                $this->squads->insert(new Squad(
                    id: null,
                    userId: $context->user()->id,
                    privateId: $this->squads->createUniquePrivateId(),
                    logo: $logo !== null ? $this->logos->save($logo['file']) : null,
                    tag: (string) $input->tag,
                    name: (string) $input->name,
                    title: $input->title,
                    homepage: $input->homepage,
                    email: $input->email,
                ));
                $context->session->flash('success', 'Squad successfully created!');

                return self::redirectToRoute($request, $response, 'squads');
            }
        }

        return self::render($request, $response, 'squads/form', ['squad' => null, 'values' => $values, 'errors' => $errors]);
    }

    /**
     * @param array{id: string} $args
     */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $context = Context::of($request);
        $squad = $this->squads->findForUser($context->user()->id, (int) $args['id']);
        if ($squad === null) {
            $context->session->flash('error', 'Squad not found');

            return self::redirectToRoute($request, $response, 'squads');
        }

        $values = ['tag' => $squad->tag, 'name' => $squad->name, 'title' => $squad->title, 'homepage' => $squad->homepage, 'email' => $squad->email];
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $values = self::post($request);
            $input = SquadInput::fromArray($values);
            $logo = self::uploadedLogo($request);
            $validator = $input->validate($logo);

            if (!self::isValidCsrf($request)) {
                $errors = ['ERROR_FORM_CSRF'];
            } elseif (!$validator->isValid()) {
                $errors = $validator->messages();
            } else {
                if (self::field($values, 'deleteLogo') === '1' || $logo !== null) {
                    $this->logos->delete($squad->logo);
                    $squad->logo = null;
                }
                if ($logo !== null) {
                    $squad->logo = $this->logos->save($logo['file']);
                }

                $squad->tag = (string) $input->tag;
                $squad->name = (string) $input->name;
                $squad->title = $input->title;
                $squad->homepage = $input->homepage;
                $squad->email = $input->email;
                $this->squads->update($squad);
                $context->session->flash('success', 'Squad successfully edited!');

                return self::redirectToRoute($request, $response, 'squad-edit', ['id' => (int) $squad->id]);
            }
        }

        return self::render($request, $response, 'squads/form', [
            'squad' => $squad,
            'logoPng' => $this->logos->pngPath($squad),
            'values' => $values,
            'errors' => $errors,
        ]);
    }

    /**
     * @param array{id: string} $args
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $context = Context::of($request);
        $squad = $this->squads->findForUser($context->user()->id, (int) $args['id']);

        if (!$context->session->isValidCsrfToken($request->getQueryParams()['token'] ?? null)) {
            $context->session->flash('error', 'ERROR_FORM_CSRF');
        } elseif ($squad === null) {
            $context->session->flash('error', 'Squad not found');
        } else {
            $this->logos->delete($squad->logo);
            $this->squads->delete((int) $squad->id);
            $context->session->flash('success', 'Squad successfully deleted!');
        }

        return self::redirectToRoute($request, $response, 'squads');
    }

    /**
     * @param array{id: string} $args
     */
    public function download(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $context = Context::of($request);
        $squad = $this->squads->findForUser($context->user()->id, (int) $args['id']);
        if ($squad === null) {
            $context->session->flash('error', 'Squad not found');

            return self::redirectToRoute($request, $response, 'squads');
        }

        $zipFile = $this->files->zip($squad);
        $handle = fopen($zipFile, 'rb');
        unlink($zipFile);

        return $response
            ->withBody(new Stream($handle))
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Transfer-Encoding', 'Binary')
            ->withHeader('Content-Disposition', 'attachment; filename="squad_file_pack_armasquads_' . $squad->id . '.zip"');
    }

    /**
     * @return ?array{file: string, name: string}
     */
    private static function uploadedLogo(ServerRequestInterface $request): ?array
    {
        $file = $request->getUploadedFiles()['logo'] ?? null;
        if (!$file instanceof UploadedFileInterface || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return [
            'file' => $file->getError() === UPLOAD_ERR_OK ? (string) $file->getStream()->getMetadata('uri') : '',
            'name' => (string) $file->getClientFilename(),
        ];
    }
}
