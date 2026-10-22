<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Repository\SquadRepository;
use Armasquads\Repository\UserRepository;
use Armasquads\Service\LogoService;
use Armasquads\Validation\EditorInput;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Other accounts that may edit a squad. Only the owner adds or removes them, an editor can leave on their own.
 */
final class EditorController extends Controller
{
    public function __construct(
        private readonly SquadRepository $squads,
        private readonly UserRepository $users,
        private readonly LogoService $logos,
    ) {
    }

    /**
     * Lists the editors of a squad and adds or removes one on POST. Changes are saved right away.
     *
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

        $squadId = (int) $squad->id;
        if ($request->getMethod() !== 'POST') {
            return self::render($request, $response, 'squads/editors', [
                'squad' => $squad,
                'logoPng' => $this->logos->pngPath($squad),
                'editors' => $this->squads->editors($squadId),
            ]);
        }

        $post = self::post($request);
        if (!self::isValidCsrf($request)) {
            $context->session->flash('error', 'ERROR_FORM_CSRF');
        } elseif (self::field($post, 'remove') !== '') {
            $this->squads->removeEditor($squadId, (int) self::field($post, 'remove'));
            $context->session->flash('success', 'Editor removed');
        } else {
            $username = trim(self::field($post, 'username'));
            $matches = $username === '' ? [] : $this->users->idsByUsername($username);
            $validator = EditorInput::validate($username, $matches, $squad->userId, array_keys($this->squads->editors($squadId)));
            if ($validator->isValid()) {
                $this->squads->addEditor($squadId, $matches[0]);
                $context->session->flash('success', 'Editor added');
            } else {
                foreach ($validator->messages() as $message) {
                    $context->session->flash('error', $message);
                }
            }
        }

        return self::redirectToRoute($request, $response, 'squad-editors', ['id' => $squadId]);
    }

    /**
     * @param array{id: string} $args
     */
    public function leave(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $context = Context::of($request);
        $userId = $context->user()->id;
        $squad = $this->squads->findEditable($userId, (int) $args['id']);

        if (!$context->session->isValidCsrfToken($request->getQueryParams()['csrf'] ?? null)) {
            $context->session->flash('error', 'ERROR_FORM_CSRF');
        } elseif ($squad === null || $squad->userId === $userId) {
            $context->session->flash('error', 'Squad not found');
        } else {
            $this->squads->removeEditor((int) $squad->id, $userId);
            $context->session->flash('success', 'You no longer edit this squad');
        }

        return self::redirectToRoute($request, $response, 'squads');
    }
}
