<?php

use Veliafar\PhpBlog\Blog\Commands\Arguments;
use Veliafar\PhpBlog\Blog\Commands\CreateUserCommand;


$container = require __DIR__ . '/bootstrap.php';


//$faker = Faker\Factory::create('ru_RU');
try {

  $command = $container->get(CreateUserCommand::class);
  $command->handle(Arguments::fromArgv($argv));

} catch (Exception $exception) {
  echo $exception->getMessage();
}



