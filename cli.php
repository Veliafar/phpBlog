<?php

use Veliafar\PhpBlog\Article\Article;
use Veliafar\PhpBlog\Comment\Comment;
use Veliafar\PhpBlog\User\User;

require_once __DIR__ . '/vendor/autoload.php';

if ($argv[1] === 'user') {
  $faker = Faker\Factory::create();
  echo PHP_EOL . new User($faker->uuid(), $faker->firstName(), $faker->lastName());
}

if ($argv[1] === 'post') {
  $faker = Faker\Factory::create();
  $user =  new User($faker->uuid(), $faker->firstName(), $faker->lastName());
  echo new Article($faker->uuid(), $user->getId(), $faker->text(10), $faker->text(100));
}

if ($argv[1] === 'comment') {
  $faker = Faker\Factory::create();
  $user =  new User($faker->uuid(), $faker->firstName(), $faker->lastName());
  $article =  new Article($faker->uuid(), $user->getId(), $faker->text(10), $faker->text(100));
  echo new Comment($faker->uuid(), $user->getId(), $article->getId(), $faker->text(50));
}