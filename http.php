<?php

use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Http\Actions\Comment\CreateComment;
use Veliafar\PhpBlog\Http\Actions\Comment\FindCommentByUuid;
use Veliafar\PhpBlog\Http\Actions\Post\CreatePost;
use Veliafar\PhpBlog\Http\Actions\Post\DeletePost;
use Veliafar\PhpBlog\Http\Actions\Post\FindPostByUuid;
use Veliafar\PhpBlog\Http\Actions\User\CreateUser;
use Veliafar\PhpBlog\Http\Actions\User\FindByUsername;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
  $_GET,
  $_SERVER,
  file_get_contents('php://input'),
);

try {
  $path = $request->path();
} catch (HttpException) {
  (new ErrorResponse)->send();
  return;
}
try {
  $method = $request->method();
} catch (HttpException) {
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
    '/comments/create' => CreateComment::class,
  ],
  'DELETE' => [
    '/posts' => DeletePost::class,
  ]
];

if (!array_key_exists($method, $routes)) {
  (new ErrorResponse('Not found'))->send();
  return;
}
if (!array_key_exists($path, $routes[$method])) {
  (new ErrorResponse('Not found'))->send();
  return;
}

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
  $response = $action->handle($request);
  $response->send();
} catch (Exception $e) {
  (new ErrorResponse($e->getMessage()))->send();
}

