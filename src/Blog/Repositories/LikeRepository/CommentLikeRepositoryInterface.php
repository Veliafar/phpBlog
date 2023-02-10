<?php

namespace Veliafar\PhpBlog\Blog\Repositories\LikeRepository;

use Veliafar\PhpBlog\Blog\CommentLike;
use Veliafar\PhpBlog\Blog\UUID;

interface CommentLikeRepositoryInterface
{
  public function save(CommentLike $like): void;

  public function get(UUID $uuid): CommentLike;

  public function getByCommentUUID(UUID $uuid): array;

  public function checkUserLikeForCommentExist(UUID $commentUUID, UUID $userUUID): void;

}