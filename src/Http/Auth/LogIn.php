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

class LogIn implements ActionInterface
{
  public function __construct(
// Авторизация по паролю
    private PasswordAuthenticationInterface $passwordAuthentication,
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
      $user = $this->passwordAuthentication->user($request);
    } catch (AuthException $e) {
      return new ErrorResponse($e->getMessage());
    }

    $authToken = new AuthToken(

      bin2hex(random_bytes(40)),
      $user->uuid(),

      (new DateTimeImmutable())->modify('+1 day')
    );

    $this->authTokensRepository->save($authToken);

    return new SuccessfulResponse([
      'token' => $authToken->token(),
    ]);
  }
}