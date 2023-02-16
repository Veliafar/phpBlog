<?php

namespace Veliafar\PhpBlog\Http\Auth;

use DateTimeImmutable;
use Exception;
use Veliafar\PhpBlog\Blog\AuthToken;
use Veliafar\PhpBlog\Blog\Exceptions\AuthException;
use Veliafar\PhpBlog\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
  public function __construct(
// Авторизация по паролю
    private TokenAuthenticationInterface $identification,
// Репозиторий токенов
    private AuthTokensRepositoryInterface   $authTokensRepository
  )
  {
  }


  /**
   * @throws Exception
   */
  public function handle(Request $request): Response
  {
    try {
      $authToken = $this->identification->token($request);
    } catch (AuthException $e) {
      return new ErrorResponse($e->getMessage());
    }

    $expiredToken = new AuthToken(
      $authToken->token(),
      $authToken->userUuid(),
      new DateTimeImmutable()
    );

    $this->authTokensRepository->save($expiredToken);

    return new SuccessfulResponse([
      'token' => $expiredToken->token(),
    ]);
  }
}