<?php

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Blog\Commands\Arguments;
use Veliafar\PhpBlog\Blog\Commands\CreateUserCommand;


$container = require __DIR__ . '/bootstrap.php';


//$faker = Faker\Factory::create('ru_RU');


$command = $container->get(CreateUserCommand::class);
$logger = $container->get(LoggerInterface::class);
try {
  $command->handle(Arguments::fromArgv($argv));
} catch (Exception $exception) {
  $logger->error($exception->getMessage(), ['exception' => $exception]);
}



