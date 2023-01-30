<?php

namespace Veliafar\PhpBlog\Blog\Repositories\UsersRepository;

use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

interface UserRepositoryInterface
{
  public function save(User $user): void;

  public function get(UUID $uuid): User;

  public function getByUsername(string $username): User;
}