<?php

use Veliafar\PhpBlog\Blog\Commands\Arguments;
use Veliafar\PhpBlog\Blog\Commands\CreateUserCommand;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
$command = new CreateUserCommand(new SqliteUsersRepository($connection));

try {
  $command->handle(Arguments::fromArgv($argv));
} catch (Exception $exception) {
  echo $exception->getMessage();
}