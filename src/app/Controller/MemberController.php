<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Repository\SquadRepository;
use Armasquads\Service\LogoService;
use Armasquads\Validation\MemberInput;
use Armasquads\Validation\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MemberController extends Controller
{
    public function __construct(
        private readonly SquadRepository $squads,
        private readonly LogoService $logos,
    ) {
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

        $rows = array_map(static fn ($member): array => $member->toArray(), $this->squads->members((int) $squad->id));
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $posted = self::post($request)['members'] ?? [];
            $rows = array_values(array_filter(is_array($posted) ? $posted : [], is_array(...)));
            $errors = self::validate($rows);

            if (!self::isValidCsrf($request)) {
                $errors = ['ERROR_FORM_CSRF'];
            } elseif ($errors === []) {
                $this->squads->replaceMembers((int) $squad->id, array_map(MemberInput::toMember(...), $rows));
                $context->session->flash('success', 'Members successfully edited!');

                return self::redirectToRoute($request, $response, 'squad-members', ['id' => (int) $squad->id]);
            }
        }

        return self::render($request, $response, 'squads/members', [
            'squad' => $squad,
            'logoPng' => $this->logos->pngPath($squad),
            'rows' => $rows,
            'errors' => $errors,
        ]);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<string>
     */
    private static function validate(array $rows): array
    {
        $errors = [];
        $uuids = [];

        foreach ($rows as $row) {
            $errors = [...$errors, ...MemberInput::validate($row)->messages()];

            // The primary key column compares case insensitive and ignores trailing spaces.
            $uuid = mb_strtolower(rtrim(MemberInput::toMember($row)->uuid));
            if (!Validator::isBlank($uuid) && isset($uuids[$uuid])) {
                $errors[] = 'PlayerID already exists in this squad!';
            }
            $uuids[$uuid] = true;
        }

        return array_values(array_unique($errors));
    }
}
