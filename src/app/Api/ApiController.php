<?php

declare(strict_types=1);

namespace Armasquads\Api;

use Armasquads\Config;
use Armasquads\Model\ApiKey;
use Armasquads\Model\Member;
use Armasquads\Model\Squad;
use Armasquads\Repository\ApiKeyRepository;
use Armasquads\Repository\SquadRepository;
use Armasquads\Service\LogoService;
use Armasquads\Translator;
use Armasquads\Validation\MemberInput;
use Armasquads\Validation\SquadInput;
use Armasquads\Validation\Validator;
use DateTimeImmutable;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * REST API v1, see templates/api/docs.phtml. Every request needs a key from the X-API-Key header or the key parameter.
 */
final readonly class ApiController
{
    private const array JSON_ERRORS = [
        JSON_ERROR_NONE => 'no errors',
        JSON_ERROR_DEPTH => 'json data maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'json data underflow or the modes mismatch',
        JSON_ERROR_CTRL_CHAR => 'json data unexpected control character found',
        JSON_ERROR_SYNTAX => 'json data syntax error, malformed JSON',
        JSON_ERROR_UTF8 => 'json data malformed UTF-8 characters, possibly incorrectly encoded',
    ];

    public function __construct(
        private Config $config,
        private ApiKeyRepository $keys,
        private SquadRepository $squads,
        private LogoService $logos,
    ) {
    }

    public function selectVersion(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->handle($request, $response, 'please specific api version /api/v[versionNumber]', []);
    }

    public function selectResource(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->handle($request, $response, 'no url resource path found', []);
    }

    public function squads(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->handle($request, $response, ['GET' => 'fetchSquads', 'POST' => 'createSquad'], []);
    }

    /**
     * @param array<string, string> $args
     */
    public function squad(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->handle($request, $response, ['DELETE' => 'deleteSquad', 'PUT' => 'updateSquad', 'GET' => 'fetchSquad'], $args);
    }

    /**
     * @param array<string, string> $args
     */
    public function members(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->handle($request, $response, ['GET' => 'fetchMembers', 'POST' => 'createMember'], $args);
    }

    /**
     * @param array<string, string> $args
     */
    public function member(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->handle($request, $response, ['DELETE' => 'deleteMember', 'PUT' => 'updateMember', 'GET' => 'fetchMember'], $args);
    }

    /**
     * @param string|array<string, string> $actions error message for incomplete paths, otherwise method => action
     * @param array<string, string> $args
     */
    private function handle(ServerRequestInterface $request, ResponseInterface $response, string|array $actions, array $args): ResponseInterface
    {
        $now = new DateTimeImmutable();
        $response = $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('X-Cache-Buster', (string) microtime(true))
            ->withHeader('Access-Control-Allow-Origin', '*');

        $keyValue = $request->getHeaderLine('X-API-Key') ?: ($request->getQueryParams()['key'] ?? '');
        if (!is_string($keyValue) || $keyValue === '') {
            return self::respond($response, ApiResponse::error('api key not found', 401));
        }

        $key = $this->keys->findByKey($keyValue);
        if ($key === null) {
            return self::respond($response, ApiResponse::error('api key invalid', 403));
        }

        $key->resetRateIfWindowPassed($now);
        $response = $response
            ->withHeader('X-RateLimit-Limit', (string) $key->limit)
            ->withHeader('X-RateLimit-Remaining', (string) $key->remainingRate())
            ->withHeader('X-RateLimit-Reset', (string) ApiKey::nextRateReset($now)->getTimestamp());

        if (!$key->active) {
            return self::respond($response, ApiResponse::error('API key banned', 403));
        }
        if ($key->isLimitExceeded()) {
            return self::respond($response, ApiResponse::error('API limit exceeded', 429));
        }
        if (is_string($actions)) {
            return self::respond($response, ApiResponse::error($actions, 400));
        }

        $response = $response->withHeader('Allow', implode(', ', array_keys($actions)));
        $method = $request->getMethod();

        $data = [];
        if ($method === 'POST' || $method === 'PUT') {
            $data = self::decodeBody((string) $request->getBody());
            if (is_string($data)) {
                return self::respond($response, ApiResponse::error($data, 400));
            }
        }

        $action = $actions[$method] ?? null;
        if ($action === null) {
            return self::respond($response, ApiResponse::error('method not supported', 501));
        }

        $translator = Translator::forHost(strtolower($request->getHeaderLine('Host')), $this->config->languagePath);
        $result = $this->$action($key->userId, $args, $data, $translator);

        if (!$result->hasError()) {
            $key->registerRequest($now);
            $this->keys->saveUsage($key);
        }

        return self::respond($response, $result);
    }

    /**
     * @return array<mixed>|string decoded data or the error message
     */
    private static function decodeBody(string $body): array|string
    {
        if ($body === '') {
            return 'post data empty';
        }

        $data = json_decode($body);
        if (!$data) {
            return self::JSON_ERRORS[json_last_error()] ?? 'json data unknown error';
        }

        $decoded = json_decode((string) json_encode($data), true);

        return is_array($decoded) ? $decoded : [$decoded];
    }

    private static function respond(ResponseInterface $response, ApiResponse $result): ResponseInterface
    {
        $response->getBody()->write($result->body());

        return $response->withStatus($result->status);
    }

    private function fetchSquads(int $userId): ApiResponse
    {
        return new ApiResponse(array_map($this->squadToArray(...), $this->squads->findByUser($userId)));
    }

    /**
     * @param array<string, string> $args
     */
    private function fetchSquad(int $userId, array $args): ApiResponse
    {
        if ((int) $args['id'] <= 0) {
            return ApiResponse::error('missing squad parameter id', 404);
        }

        $squad = $this->squads->findForUser($userId, (int) $args['id']);

        return $squad !== null ? new ApiResponse($this->squadToArray($squad)) : ApiResponse::error('squad not found', 404);
    }

    /**
     * @param array<string, string> $args
     * @param array<string, mixed> $data
     */
    private function createSquad(int $userId, array $args, array $data, Translator $translator): ApiResponse
    {
        $logo = self::logoFromBase64($data);
        if ($logo === false) {
            return ApiResponse::error(['logo' => 'need logo data in base64'], 200);
        }

        try {
            $input = SquadInput::fromArray($data);
            $validator = $input->validate($logo);
            if (!$validator->isValid()) {
                return ApiResponse::error(self::translatedErrors($validator, $translator), 422);
            }

            $squad = new Squad(
                id: null,
                userId: $userId,
                privateId: $this->squads->createUniquePrivateId(),
                logo: $logo !== null ? $this->logos->save($logo['file']) : null,
                tag: (string) $input->tag,
                name: (string) $input->name,
                title: $input->title,
                homepage: $input->homepage,
                email: $input->email,
            );
            $this->squads->insert($squad);

            $result = $this->squadToArray($squad);
            $paa = $this->logos->paaPath($squad);
            if ($paa !== null) {
                $result['logo'] = base64_encode((string) file_get_contents($this->logos->absolutePath($paa)));
            }

            return new ApiResponse($result, null, 201);
        } finally {
            self::removeLogo($logo);
        }
    }

    /**
     * @param array<string, string> $args
     * @param array<string, mixed> $data
     */
    private function updateSquad(int $userId, array $args, array $data, Translator $translator): ApiResponse
    {
        if ((int) $args['id'] <= 0) {
            return ApiResponse::error('squad [id] missing', 400);
        }

        $squad = $this->squads->findForUser($userId, (int) $args['id']);
        if ($squad === null) {
            return ApiResponse::error('squad not found', 404);
        }

        $logo = self::logoFromBase64($data);
        if ($logo === false) {
            return ApiResponse::error(['logo' => 'need logo data in base64'], 200);
        }

        try {
            $current = ['tag' => $squad->tag, 'name' => $squad->name, 'title' => $squad->title, 'homepage' => $squad->homepage, 'email' => $squad->email];
            $input = SquadInput::fromArray(array_merge($current, $data));
            $validator = $input->validate($logo);
            if (!$validator->isValid()) {
                return ApiResponse::error(self::translatedErrors($validator, $translator), 422);
            }

            if ($logo !== null) {
                $newLogo = $this->logos->save($logo['file']);
                if ($newLogo !== null) {
                    $this->logos->delete($squad->logo);
                    $squad->logo = $newLogo;
                }
            }
            $squad->tag = (string) $input->tag;
            $squad->name = (string) $input->name;
            $squad->title = $input->title;
            $squad->homepage = $input->homepage;
            $squad->email = $input->email;
            $this->squads->update($squad);

            return new ApiResponse($this->squadToArray($squad));
        } finally {
            self::removeLogo($logo);
        }
    }

    /**
     * @param array<string, string> $args
     */
    private function deleteSquad(int $userId, array $args): ApiResponse
    {
        $squad = $this->squads->findForUser($userId, (int) $args['id']);
        if ($squad === null) {
            return (int) $args['id'] <= 0
                ? ApiResponse::error('missing parameter id', 400)
                : ApiResponse::error('squad not found', 404);
        }

        $this->logos->delete($squad->logo);
        $this->squads->delete((int) $squad->id);

        return new ApiResponse();
    }

    /**
     * @param array<string, string> $args
     */
    private function fetchMembers(int $userId, array $args): ApiResponse
    {
        $squad = $this->findSquadForMembers($userId, $args);
        if ($squad instanceof ApiResponse) {
            return $squad;
        }

        return new ApiResponse(array_map(static fn (Member $member): array => $member->toArray(), $this->squads->members((int) $squad->id)));
    }

    /**
     * @param array<string, string> $args
     */
    private function fetchMember(int $userId, array $args): ApiResponse
    {
        $found = $this->findMember($userId, $args);

        return $found instanceof ApiResponse ? $found : new ApiResponse($found[1]->toArray());
    }

    /**
     * @param array<string, string> $args
     * @param array<string, mixed> $data
     */
    private function createMember(int $userId, array $args, array $data, Translator $translator): ApiResponse
    {
        $squad = $this->findSquadForMembers($userId, $args);
        if ($squad instanceof ApiResponse) {
            return $squad;
        }

        $validator = MemberInput::validate($data);
        if (!$validator->isValid()) {
            return ApiResponse::error(self::translatedErrors($validator, $translator), 422);
        }

        $member = MemberInput::toMember($data);
        try {
            $this->squads->insertMember((int) $squad->id, $member);
        } catch (PDOException) {
            return ApiResponse::error(['uuid' => 'allready exists', 'username' => 'allready exists'], 422);
        }

        return new ApiResponse($member->toArray(), null, 201);
    }

    /**
     * @param array<string, string> $args
     * @param array<string, mixed> $data
     */
    private function updateMember(int $userId, array $args, array $data, Translator $translator): ApiResponse
    {
        $found = $this->findMember($userId, $args);
        if ($found instanceof ApiResponse) {
            return $found;
        }
        [$squad, $member] = $found;

        $merged = array_merge($member->toArray(), $data);
        $validator = MemberInput::validate($merged);
        if (!$validator->isValid()) {
            return ApiResponse::error(self::translatedErrors($validator, $translator), 422);
        }

        $updated = MemberInput::toMember($merged);
        try {
            $this->squads->updateMember((int) $squad->id, $member->uuid, $updated);
        } catch (PDOException) {
            return ApiResponse::error(['uuid' => 'allready exists'], 422);
        }

        return new ApiResponse($updated->toArray());
    }

    /**
     * @param array<string, string> $args
     */
    private function deleteMember(int $userId, array $args): ApiResponse
    {
        $found = $this->findMember($userId, $args);
        if ($found instanceof ApiResponse) {
            return $found;
        }

        $this->squads->deleteMember((int) $found[0]->id, $found[1]->uuid);

        return new ApiResponse();
    }

    /**
     * @param array<string, string> $args
     */
    private function findSquadForMembers(int $userId, array $args): Squad|ApiResponse
    {
        if ((int) $args['id'] <= 0) {
            return ApiResponse::error('missing squad parameter id', 404);
        }

        return $this->squads->findForUser($userId, (int) $args['id']) ?? ApiResponse::error('squad not found', 404);
    }

    /**
     * @param array<string, string> $args
     * @return array{Squad, Member}|ApiResponse
     */
    private function findMember(int $userId, array $args): array|ApiResponse
    {
        if ((int) $args['id'] <= 0) {
            return ApiResponse::error('missing squad parameter id', 404);
        }
        if (($args['mid'] ?? '') === '' || $args['mid'] === '0') {
            return ApiResponse::error('missing member parameter id', 404);
        }

        $squad = $this->squads->findForUser($userId, (int) $args['id']);
        if ($squad === null) {
            return ApiResponse::error('squad not found', 404);
        }

        $member = $this->squads->findMember((int) $squad->id, $args['mid']);

        return $member !== null ? [$squad, $member] : ApiResponse::error('member not found', 404);
    }

    /**
     * @return array{id: ?int, privateID: string, tag: string, name: string, email: ?string, logo: ?string, homepage: ?string, title: ?string}
     */
    private function squadToArray(Squad $squad): array
    {
        return [
            'id' => $squad->id,
            'privateID' => $squad->privateId,
            'tag' => $squad->tag,
            'name' => $squad->name,
            'email' => $squad->email,
            'logo' => $this->logos->paaPath($squad),
            'homepage' => $squad->homepage,
            'title' => $squad->title,
        ];
    }

    /**
     * Writes a base64 encoded logo into a temporary file.
     *
     * @param array<string, mixed> $data
     * @return array{file: string, name: string}|false|null null without logo, false for invalid base64
     */
    private static function logoFromBase64(array $data): array|false|null
    {
        $encoded = $data['logo'] ?? null;
        if (empty($encoded)) {
            return null;
        }

        $decoded = is_string($encoded) ? base64_decode($encoded, true) : false;
        if ($decoded === false || base64_encode($decoded) !== $encoded) {
            return false;
        }

        $file = (string) tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($file, $decoded);

        return ['file' => $file, 'name' => basename($file) . '.png'];
    }

    /**
     * @param ?array{file: string, name: string} $logo
     */
    private static function removeLogo(?array $logo): void
    {
        if ($logo !== null && is_file($logo['file'])) {
            unlink($logo['file']);
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function translatedErrors(Validator $validator, Translator $translator): array
    {
        return array_map(
            static fn (array $messages): array => array_map($translator->translate(...), $messages),
            $validator->errors(),
        );
    }
}
