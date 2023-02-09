<?php

use Veliafar\PhpBlog\Blog\Exceptions\AppException;
use Veliafar\PhpBlog\Blog\Exceptions\HttpException;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Http\Actions\Post\CreatePost;
use Veliafar\PhpBlog\Http\Actions\Post\FindPostByUuid;
use Veliafar\PhpBlog\Http\Actions\User\CreateUser;
use Veliafar\PhpBlog\Http\Actions\User\FindByUsername;
use Veliafar\PhpBlog\Http\ErrorResponse;
use Veliafar\PhpBlog\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

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
// Пытаемся получить HTTP-метод запроса
  $method = $request->method();
} catch (HttpException) {
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
  (new ErrorResponse)->send();
  return;
}

$usersRepository = new SqliteUsersRepository(
  new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
$postsRepository = new SqlitePostsRepository(
  new PDO('sqlite:' . __DIR__ . '/blog.sqlite'),
  $usersRepository
);


$routes = [
  // Добавили ещё один уровень вложенности
  // для отделения маршрутов,
  // применяемых к запросам с разными методами
  'GET' => [
    '/users/show' => new FindByUsername(
      $usersRepository
    ),
    '/posts/show' => new FindPostByUuid(
      $postsRepository
    ),
  ],
  'POST' => [
    '/users/create' => new CreateUser(
      $usersRepository
    ),
    '/posts/create' => new CreatePost(
      $postsRepository,
      $usersRepository
    ),
  ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
  (new ErrorResponse('Not found'))->send();
  return;
}
// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
  (new ErrorResponse('Not found'))->send();
  return;
}
// Выбираем действие по методу и пути
$action = $routes[$method][$path];
try {
  $response = $action->handle($request);
} catch (AppException $e) {
  (new ErrorResponse($e->getMessage()))->send();
}
$response->send();