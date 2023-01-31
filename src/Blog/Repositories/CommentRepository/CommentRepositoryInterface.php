<?php

namespace Veliafar\PhpBlog\Blog\Repositories\CommentRepository;

use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\UUID;

interface CommentRepositoryInterface
{
  public function save(Comment $comment): void;

  public function get(UUID $uuid): Comment;
}