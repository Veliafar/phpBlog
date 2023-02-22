<?php

namespace Veliafar\PhpBlog\Blog\Repositories\AuthTokensRepository;

use Veliafar\PhpBlog\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
  public function save(AuthToken $authToken): void;
// Метод получения токена
  public function get(string $token): AuthToken;

}