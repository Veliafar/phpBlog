<?php

namespace Veliafar\PhpBlog\Http\Auth;

use DateTimeImmutable;
use Veliafar\PhpBlog\Blog\AuthToken;
use Veliafar\PhpBlog\Blog\Exceptions\AuthException;
use Veliafar\PhpBlog\Blog\Exceptions\AuthTokenNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

  private const HEADER_PREFIX = 'Bearer ';

  public function __construct(
// Репозиторий токенов
    private AuthTokensRepositoryInterface $authTokensRepository,
// Репозиторий пользователей
    private UserRepositoryInterface       $usersRepository,
  )
  {
  }


  /**
   * @throws AuthException
   */
  public function user(Request $request): User
  {
    try {
      $header = $request->header('Authorization');
    } catch (HttpException $e) {
      throw new AuthException($e->getMessage());
    }

    // Проверяем, что заголовок имеет правильный формат
    if (!str_starts_with($header, self::HEADER_PREFIX)) {
      throw new AuthException("Malformed token: [$header]");
    }

    // Отрезаем префикс Bearer
    $token = mb_substr($header, strlen(self::HEADER_PREFIX));

    // Ищем токен в репозитории
    try {
      $authToken = $this->authTokensRepository->get($token);
    } catch (AuthTokenNotFoundException) {
      throw new AuthException("Bad token: [$token]");
    }

    // Проверяем срок годности токена
    if ($authToken->expiresOn() <= new DateTimeImmutable()) {
      throw new AuthException("Token expired: [$token]");
    }
    // Получаем UUID пользователя из токена
    $userUuid = $authToken->userUuid();
    // Ищем и возвращаем пользователя
    return $this->usersRepository->get($userUuid);
  }

  /**
   * @throws AuthException
   */
  public function token(Request $request): AuthToken {
    try {
      $header = $request->header('Authorization');
    } catch (HttpException $e) {
      throw new AuthException($e->getMessage());
    }

    // Проверяем, что заголовок имеет правильный формат
    if (!str_starts_with($header, self::HEADER_PREFIX)) {
      throw new AuthException("Malformed token: [$header]");
    }

    // Отрезаем префикс Bearer
    $token = mb_substr($header, strlen(self::HEADER_PREFIX));

    try {
      $authToken = $this->authTokensRepository->get($token);
    } catch (AuthTokenNotFoundException) {
      throw new AuthException("Bad token: [$token]");
    }

    // Проверяем срок годности токена
    if ($authToken->expiresOn() <= new DateTimeImmutable()) {
      throw new AuthException("Token expired: [$token]");
    }

    return $authToken;
  }
}