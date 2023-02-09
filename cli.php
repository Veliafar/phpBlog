<?php

use Veliafar\PhpBlog\Blog\Commands\Arguments;
use Veliafar\PhpBlog\Blog\Commands\CreateUserCommand;


$container = require __DIR__ . '/bootstrap.php';


//$faker = Faker\Factory::create('ru_RU');
try {

  $command = $container->get(CreateUserCommand::class);
  $command->handle(Arguments::fromArgv($argv));

  //$userRepository->save(new User(UUID::random(), new Name('Ivan', 'Ivan'), 'admin'));
  // $command->handle(Arguments::fromArgv($argv));
  //$user = $userRepository->getByUsername('user1');
  //$postRepository->save(new Post(UUID::random(), $user, $faker->word(), $faker->realText(50)));
  //$post = $postRepository->get(new UUID('4b33b881-023c-4dd1-af74-4e19806b7db5'));
  //$commentRepository->save(new Comment(UUID::random(), $post, $user, $faker->realText(50)));
  //echo $commentRepository->get(new UUID('4bd4d00f-3756-4a70-9ffb-dfaffcbcf95a'));

  //  $postRepository->save(
  //    new Post(
  //      UUID::random(),
  //      $userRepository->getByUsername('user1'),
  //      'title',
  //      'text'
  //    )
  //  );

  //  $commentRepository->save(new Comment(
  //    UUID::random(),
  //    $postRepository->get(new UUID('4767b685-ee61-492c-9608-b7e2af985cf1')),
  //    $userRepository->getByUsername('user1'),
  //    'number 3'
  //  ));

  //  $allCommentsByPostUUID = $postRepository->delete(
  //    new UUID('4b33b881-023c-4dd1-af74-4e19806b7db5'),
  //    $commentRepository
  //  );
  //$commentRepository->delete(new UUID('4b9d375a-56fb-438f-b204-0aa374b5e80f'));

  //$postRepository->delete(new UUID('ad042b70-8031-4a72-bd11-e8f6b983afe6'), $commentRepository);

} catch (Exception $exception) {
  echo $exception->getMessage();
}



