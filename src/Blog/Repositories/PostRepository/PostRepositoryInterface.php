<?php

namespace Veliafar\PhpBlog\Blog\Repositories\PostRepository;

use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\UUID;

interface PostRepositoryInterface
{
  public function save(Post $post): void;

  public function get(UUID $uuid): Post;
}