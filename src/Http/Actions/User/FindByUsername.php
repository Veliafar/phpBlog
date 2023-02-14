<?php

namespace Veliafar\PhpBlog\Http\Actions\User;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
  public function __construct(
    private UserRepositoryInterface $usersRepository,
    private LoggerInterface $logger,
  )
  {
  }

  // Функция, описанная в контракте
  public function handle(Request $request): Response
  {
    try {
      $username = $request->query('username');
    } catch (HttpException $e) {
      $this->logger->error($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    try {
      $user = $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
      $this->logger->notice($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }
    $this->logger->info("User found: $username");
    return new SuccessfulResponse([
      'username' => $user->username(),
      'name' => $user->name()->first() . ' ' . $user->name()->last(),
    ]);
  }

}