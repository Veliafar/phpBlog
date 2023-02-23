<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Veliafar\PhpBlog\Blog\Commands\CreateUserConsole;
use Veliafar\PhpBlog\Blog\Commands\DeletePostConsole;
use Veliafar\PhpBlog\Blog\Commands\FakeData\PopulateDB;
use Veliafar\PhpBlog\Blog\Commands\UpdateUserConsole;

$container = require __DIR__ . '/bootstrap.php';
//$faker = Faker\Factory::create('ru_RU');


// Создаём объект приложения
$application = new Application();
$commandsClasses = [
  CreateUserConsole::class,
  DeletePostConsole::class,
  UpdateUserConsole::class,
  PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
  // Посредством контейнера
  // создаём объект команды
  $command = $container->get($commandClass);
  // Добавляем команду к приложению
  $application->add($command);
}

$logger = $container->get(LoggerInterface::class);
try {
  $application->run();
} catch (Exception $exception) {
  $logger->error($exception->getMessage(), ['exception' => $exception]);
}



