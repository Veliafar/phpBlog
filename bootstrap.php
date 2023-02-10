<?php

use Veliafar\PhpBlog\Blog\Container\ContainerStorage;
use Veliafar\PhpBlog\Blog\Container\DIContainer;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\CommentLikeRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\PostLikeRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\SqliteCommentLikesRepository;
use Veliafar\PhpBlog\Blog\Repositories\LikeRepository\SqlitePostLikesRepository;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer(new ContainerStorage());

$container->bind(
  PDO::class,
  new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
  UserRepositoryInterface::class,
  SqliteUsersRepository::class
);

$container->bind(
  PostRepositoryInterface::class,
  SqlitePostsRepository::class
);

$container->bind(
  CommentRepositoryInterface::class,
  SqliteCommentsRepository::class
);

$container->bind(
  PostLikeRepositoryInterface::class,
  SqlitePostLikesRepository::class
);

$container->bind(
  CommentLikeRepositoryInterface::class,
  SqliteCommentLikesRepository::class
);

return $container;