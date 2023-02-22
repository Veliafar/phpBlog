<?php

namespace Veliafar\PhpBlog\Http\Actions\User;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class CreateUser implements ActionInterface
{
  public function __construct(
    private UserRepositoryInterface $usersRepository,
    private LoggerInterface         $logger,
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {
    try {
      $user = User::createFrom(
        new Name(
          $request->jsonBodyField('first_name'),
          $request->jsonBodyField('last_name'),
        ),
        $request->jsonBodyField('username'),
        $request->jsonBodyField('password')
      );

    } catch (HttpException $e) {
      $this->logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    $this->usersRepository->save($user);
    $this->logger->info("User created: $user");
    return new SuccessfulResponse([
      'uuid' => (string)$user->uuid(),
    ]);
  }
}