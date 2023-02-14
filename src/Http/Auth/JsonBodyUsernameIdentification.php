<?php

namespace Veliafar\PhpBlog\Http\Auth;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\AuthException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Http\Request;

class JsonBodyUsernameIdentification implements IdentificationUsernameInterface
{

  public function __construct(
    private UserRepositoryInterface $userRepository,
    private LoggerInterface         $logger,
  )
  {
  }

  /**
   * @throws AuthException
   */
  public function user(Request $request, bool $isQuery): User
  {
    try {
      $username = !$isQuery
        ? $request->jsonBodyField('username')
        : $request->query('username');

    } catch (HttpException $e) {
      $this->logger->warning($e->getMessage());
      throw new  AuthException($e->getMessage());
    }

    try {
      return $this->userRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
      $this->logger->error($e->getMessage());
      throw new AuthException($e->getMessage());
    }
  }
}