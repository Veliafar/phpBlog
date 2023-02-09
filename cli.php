<?php

use Veliafar\PhpBlog\Blog\Commands\Arguments;
use Veliafar\PhpBlog\Blog\Commands\CreateUserCommand;
use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\UUID;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//$command = new CreateUserCommand(new SqliteUsersRepository($connection));



$userRepository = new SqliteUsersRepository($connection);
$postRepository = new SqlitePostsRepository($connection, $userRepository);
$commentRepository = new SqliteCommentsRepository($connection, $postRepository, $userRepository);
$faker = Faker\Factory::create('ru_RU');
try {
  // $command->handle(Arguments::fromArgv($argv));
  //$user = $userRepository->getByUsername('user1');
  //$postRepository->save(new Post(UUID::random(), $user, $faker->word(), $faker->realText(50)));
  //$post = $postRepository->get(new UUID('4b33b881-023c-4dd1-af74-4e19806b7db5'));
  //$commentRepository->save(new Comment(UUID::random(), $post, $user, $faker->realText(50)));
  echo $commentRepository->get(new UUID('4bd4d00f-3756-4a70-9ffb-dfaffcbcf95a'));

} catch (Exception $exception) {
  echo $exception->getMessage();
}



