<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Repository\UserRepository;
use Armasquads\Service\Passwords;
use Armasquads\Validation\Validator;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthController extends Controller
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        if ($context->user !== null) {
            return self::redirectToRoute($request, $response, 'dashboard');
        }

        $fallbackUrl = self::localPath($request->getQueryParams()['fallback_url'] ?? null);
        $post = self::post($request);
        $email = is_string($post['email'] ?? null) ? $post['email'] : '';

        if ($request->getMethod() === 'POST') {
            $error = $this->authenticate($request, $email, is_string($post['password'] ?? null) ? $post['password'] : '');
            if ($error === null) {
                return $fallbackUrl !== null
                    ? self::redirect($response, $fallbackUrl)
                    : self::redirectToRoute($request, $response, 'dashboard');
            }
            $context->session->flash('error', $error);
        }

        return self::render($request, $response, 'auth/login', [
            'fallbackUrl' => $fallbackUrl,
            'loginEmail' => $email,
        ]);
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        $values = [];
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $post = self::post($request);
            $values = [
                'username' => trim(strip_tags(self::field($post, 'username'))),
                'email' => trim(self::field($post, 'email')),
                'password' => trim(self::field($post, 'password')),
                'password2' => trim(self::field($post, 'password2')),
            ];

            if (!self::isValidCsrf($request)) {
                $errors = ['ERROR_FORM_CSRF'];
            } else {
                $validator = $this->validateRegistration($values);
                if ($validator->isValid()) {
                    $userId = $this->users->create(
                        $values['username'],
                        $values['email'],
                        Passwords::hash($values['password']),
                        new DateTimeImmutable(),
                    );
                    $context->session->login($userId);

                    return self::redirectToRoute($request, $response, 'dashboard');
                }
                $errors = $validator->messages();
            }
        }

        return self::render($request, $response, 'auth/login', [
            'fallbackUrl' => null,
            'loginEmail' => '',
            'registerValues' => $values,
            'registerErrors' => $errors,
        ]);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        if ($context->user !== null) {
            $context->session->logout();
            $context->session->flash('success', 'FRONTEND_LOGIN_AUTH_LOGOUT');
        }

        return self::redirectToRoute($request, $response, 'home');
    }

    /**
     * Returns null on success or the error message.
     */
    private function authenticate(ServerRequestInterface $request, string $email, string $password): ?string
    {
        if (!self::isValidCsrf($request)) {
            return 'ERROR_FORM_CSRF';
        }

        $user = $email !== '' ? $this->users->findByEmail($email) : null;
        if ($user === null || $password === '' || !Passwords::verify($password, $user->passwordHash)) {
            return 'FRONTEND_LOGIN_AUTH_WRONG';
        }
        if ($user->disabled) {
            return 'FRONTEND_LOGIN_AUTH_BANNED';
        }

        if (Passwords::isLegacyHash($user->passwordHash)) {
            $this->users->updatePassword($user->id, Passwords::hash($password));
        }
        $this->users->updateLastLogin($user->id, new DateTimeImmutable());
        Context::of($request)->session->login($user->id);

        return null;
    }

    /**
     * @param array{username: string, email: string, password: string, password2: string} $values
     */
    private function validateRegistration(array $values): Validator
    {
        $validator = new Validator();

        if (Validator::isBlank($values['username'])) {
            $validator->add('username', Validator::IS_EMPTY, 'Choose a Username');
        } elseif ($this->users->usernameExists($values['username'])) {
            $validator->add('username', Validator::ALREADY_EXISTS, 'Username already exists!');
        }

        if (Validator::isBlank($values['email'])) {
            $validator->add('email', Validator::IS_EMPTY, 'Enter a valid Email');
        } elseif (!Validator::isEmail($values['email'])) {
            $validator->add('email', Validator::INVALID_EMAIL, 'Please enter a valid Email!');
        } elseif ($this->users->emailExists($values['email'])) {
            $validator->add('email', Validator::ALREADY_EXISTS, 'Account with this Email already exists!');
        }

        if (Validator::isBlank($values['password'])) {
            $validator->add('password', Validator::IS_EMPTY, 'Enter a Password');
        }

        if (Validator::isBlank($values['password2'])) {
            $validator->add('password2', Validator::IS_EMPTY, 'Please repeat your Password!');
        } elseif ($values['password2'] !== $values['password']) {
            $validator->add('password2', Validator::NOT_SAME, 'Both entered passwords are different!');
        }

        return $validator;
    }
}
