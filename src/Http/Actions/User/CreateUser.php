<?php

namespace Veliafar\PhpBlog\Http\Actions\User;

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
    private UserRepositoryInterface $usersRepository
  )
  {
  }

  /**
   * @throws InvalidArgumentException
   */
  public function handle(Request $request): Response
  {
    try {
      $newUserUuid = UUID::random();

      $user = new User(
        $newUserUuid,
        new Name(
          $request->jsonBodyField('first_name'),
          $request->jsonBodyField('last_name'),
        ),
        $request->jsonBodyField('username'),
      );

    } catch (HttpException $e) {
      return new ErrorResponse($e->getMessage());
    }
    $this->usersRepository->save($user);

    return new SuccessfulResponse([
      'uuid' => (string)$newUserUuid,
    ]);
  }
}