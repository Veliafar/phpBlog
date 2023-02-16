<?php

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Http\Actions\Comment\CreateComment;
use Veliafar\PhpBlog\Http\Actions\Comment\FindCommentByUuid;
use Veliafar\PhpBlog\Http\Actions\Like\CreateCommentLike;
use Veliafar\PhpBlog\Http\Actions\Like\CreatePostLike;
use Veliafar\PhpBlog\Http\Actions\Post\CreatePost;
use Veliafar\PhpBlog\Http\Actions\Post\DeletePost;
use Veliafar\PhpBlog\Http\Actions\Post\FindPostByUuid;
use Veliafar\PhpBlog\Http\Actions\User\CreateUser;
use Veliafar\PhpBlog\Http\Actions\User\FindByUsername;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$request = new Request(
  $_GET,
  $_SERVER,
  file_get_contents('php://input'),
);

try {
  $path = $request->path();
} catch (HttpException $except) {
  $logger->warning($except->getMessage());
  (new ErrorResponse)->send();
  return;
}
try {
  $method = $request->method();
} catch (HttpException $except) {
  $logger->warning($except->getMessage());
  (new ErrorResponse)->send();
  return;
}


$routes = [
  'GET' => [
    '/users/show' => FindByUsername::class,
    '/posts/show' => FindPostByUuid::class,
    '/comments/show' => FindCommentByUuid::class,
  ],
  'POST' => [
    '/users/create' => CreateUser::class,
    '/posts/create' => CreatePost::class,
    '/posts/like/create' => CreatePostLike::class,
    '/comments/create' => CreateComment::class,
    '/comments/like/create' => CreateCommentLike::class,
  ],
  'DELETE' => [
    '/posts' => DeletePost::class,
  ]
];

if (
  !array_key_exists($method, $routes)
  || !array_key_exists($path, $routes[$method])
) {
  $message = "Route not found: $method $path";
  $logger->notice($message);
  (new ErrorResponse($message))->send();
  return;
}

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
  $response = $action->handle($request);
  $response->send();
} catch (Exception $exception) {
  $logger->error($exception->getMessage(), ['exception' => $exception]);
  try {
    (new ErrorResponse($exception->getMessage()))->send();
  } catch (JsonException $except) {
    $logger->error($except->getMessage(), ['except' => $except]);
  }
}

