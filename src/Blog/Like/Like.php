<?php

namespace Veliafar\PhpBlog\Blog\Like;



use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class Like
{
  public function __construct(private UUID $uuid,  private User $user)
  {
  }

  public function uuid(): UUID
  {
    return $this->uuid;
  }

  public function getUser(): User
  {
    return $this->user;
  }

}