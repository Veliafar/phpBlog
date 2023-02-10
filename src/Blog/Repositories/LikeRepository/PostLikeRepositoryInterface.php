<?php

namespace Veliafar\PhpBlog\Blog\Repositories\LikeRepository;

use Veliafar\PhpBlog\Blog\PostLike;
use Veliafar\PhpBlog\Blog\UUID;

interface PostLikeRepositoryInterface
{
  public function save(PostLike $like): void;

  public function get(UUID $uuid): PostLike;

  public function getByPostUUID(UUID $uuid): array;

  public function checkUserLikeForPostExist(UUID $postUUID, UUID $userUUID): void;

}