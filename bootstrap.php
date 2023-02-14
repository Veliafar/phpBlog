<?php

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
use Veliafar\PhpBlog\Http\Auth\IdentificationUsernameInterface;
use Veliafar\PhpBlog\Http\Auth\IdentificationUUIDInterface;
use Veliafar\PhpBlog\Http\Auth\JsonBodyUsernameIdentification;
use Veliafar\PhpBlog\Http\Auth\JsonBodyUUIDIdentification;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer(new ContainerStorage());
Dotenv::createImmutable(__DIR__)->safeLoad();
$container->bind(
  PDO::class,
  new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH'])
);


$logger = (new Logger('blog'));
if ($_ENV['LOG_TO_FILES'] === 'yes') {
  $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/blog.log'))
    ->pushHandler(
      new StreamHandler(
        __DIR__ . '/logs/blog.warning.log',
        level: Level::Warning,
        bubble: false
      ),
    )
    ->pushHandler(
      new StreamHandler(
        __DIR__ . '/logs/blog.error.log',
        level: Level::Error,
        bubble: false
      ),
    )
    ->pushHandler(
      new StreamHandler(
        __DIR__ . '/logs/blog.critical.log',
        level: Level::Critical,
        bubble: false
      ),
    );
}
if ($_ENV['LOG_TO_CONSOLE'] === 'yes') {
  $logger->pushHandler(
    new StreamHandler("php://stdout")
  );
}

$container->bind(
  LoggerInterface::class,
  $logger
);

$container->bind(
  UserRepositoryInterface::class,
  SqliteUsersRepository::class
);

$container->bind(
  IdentificationUsernameInterface::class,
  JsonBodyUsernameIdentification::class
);

$container->bind(
  IdentificationUUIDInterface::class,
  JsonBodyUUIDIdentification::class
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