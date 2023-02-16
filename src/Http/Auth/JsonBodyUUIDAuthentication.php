<?php

namespace Veliafar\PhpBlog\Http\Auth;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\AuthException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;
use Veliafar\PhpBlog\Http\Request;

class JsonBodyUUIDAuthentication implements AuthenticationUUIDInterface
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
  public function user(Request $request): User
  {
    try {
      $userUUID = new UUID($request->jsonBodyField('user_uuid'));
    } catch (HttpException|InvalidArgumentException $e) {
      $this->logger->warning($e->getMessage());
      throw new  AuthException($e->getMessage());
    }

    try {
      return $this->userRepository->get($userUUID);
    } catch (UserNotFoundException $e) {
      $this->logger->error($e->getMessage());
      throw new AuthException($e->getMessage());
    }
  }
}