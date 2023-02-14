<?php

namespace Veliafar\PhpBlog\Blog\Repositories\UsersRepository;

use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class DummyUsersRepository implements UserRepositoryInterface
{

  public function save(User $user): void
  {
    // TODO: Implement save() method.
  }

  public function get(UUID $uuid): User
  {
    throw new UserNotFoundException("Not found");
  }

  /**
   * @throws UserNotFoundException
   * @throws InvalidArgumentException
   */
  public function getByUsername(string $username): User
  {
    if ($username !== "user123") {
      throw new UserNotFoundException(
        "Cannot get user: $username"
      );
    }

    return new User(UUID::random(), new Name("first", "second"), "user123");
  }

}