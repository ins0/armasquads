<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Repository\UserRepository;
use Armasquads\Service\Passwords;
use Armasquads\Validation\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AccountController extends Controller
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $post = self::post($request);
            $validator = $this->validatePasswordChange(
                $context->user()->passwordHash,
                self::field($post, 'oldPass'),
                self::field($post, 'newPass'),
                self::field($post, 'newPassConfirm'),
            );

            if (!self::isValidCsrf($request)) {
                $errors = ['ERROR_FORM_CSRF'];
            } elseif (!$validator->isValid()) {
                $errors = $validator->messages();
            } else {
                $this->users->updatePassword($context->user()->id, Passwords::hash(self::field($post, 'newPass')));
                $context->session->flash('success', 'Password changed');

                return self::redirectToRoute($request, $response, 'account');
            }
        }

        return self::render($request, $response, 'account', ['errors' => $errors]);
    }

    private function validatePasswordChange(string $currentHash, string $oldPass, string $newPass, string $newPassConfirm): Validator
    {
        $validator = new Validator();

        if (Validator::isBlank($oldPass)) {
            $validator->add('oldPass', Validator::IS_EMPTY, 'ERROR_CHANGE_PASSWORD_ENTER_CURRENT');
        } elseif (!Passwords::verify($oldPass, $currentHash)) {
            $validator->add('oldPass', Validator::INVALID_VALUE, 'ERROR_CHANGE_PASSWORD_WRONG');
        }

        if (Validator::isBlank($newPass)) {
            $validator->add('newPass', Validator::IS_EMPTY, 'ERROR_CHANGE_PASSWORD_ENTER_NEW');
        } elseif ($newPass !== $newPassConfirm) {
            $validator->add('newPass', Validator::NOT_SAME, 'ERROR_CHANGE_PASSWORD_NOT_SAME');
        }

        return $validator;
    }
}
