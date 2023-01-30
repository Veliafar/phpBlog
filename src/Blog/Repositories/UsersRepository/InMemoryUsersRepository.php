<?php

namespace Veliafar\PhpBlog\Blog\Repositories\UsersRepository;

use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class InMemoryUsersRepository implements UserRepositoryInterface
{
  private array $users = [];

  public function save(User $user): void
  {
    $this->users[] = $user;
  }

  /**
   * @throws UserNotFoundException
   */
  public function get(UUID $uuid): User
  {
    foreach ($this->users as $user) {
        if ((string)$user->uuid() === (string)$uuid) {
          return $user;
        }
    }
    throw new UserNotFoundException("User not found: $uuid");
  }

  /**
   * @throws UserNotFoundException
   */
  public function getByUsername(string $username): User
  {
    foreach ($this->users as $user) {
      if ((string)$user->username() === $username) {
        return $user;
      }
    }
    throw new UserNotFoundException("User not found: $username");
  }
}