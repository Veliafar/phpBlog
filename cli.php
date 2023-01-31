<?php

use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\User;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');
$user = new User($faker->uuid(), new Name($faker->firstName(), $faker->lastName()), $faker->word());

//$userRepository = new InMemoryUsersRepository();

if ($argv[1] === 'user') {
  echo PHP_EOL . $user;
}

if ($argv[1] === 'post') {
  echo new Post($faker->uuid(), $user, $faker->realText(10), $faker->realText(100));
}

if ($argv[1] === 'comment') {
  $post = new Post($faker->uuid(), $user, $faker->realText(10), $faker->realText(100));
  echo new Comment($faker->uuid(), $post, $user, $faker->realText(50));
}