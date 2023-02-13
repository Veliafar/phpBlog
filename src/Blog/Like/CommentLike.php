<?php

namespace Veliafar\PhpBlog\Blog\Like;

use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class CommentLike extends Like
{
  private Comment $comment;
  public function __construct(private UUID $uuid, Comment $comment,  private User $user)
  {
    parent::__construct($uuid, $user);
    $this->comment = $comment;
  }


  public function getComment(): Comment
  {
    return $this->comment;
  }

  public function __toString(): string
  {
    return "like с ID $this->uuid" . "пользователя: $this->user" . PHP_EOL . "для комментария: $this->comment";
  }
}