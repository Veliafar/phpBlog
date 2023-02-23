<?php

namespace Veliafar\PhpBlog\Blog\Commands\FakeData;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class PopulateDB extends Command
{
  public function __construct(
    private readonly \Faker\Generator           $faker,
    private readonly UserRepositoryInterface    $usersRepository,
    private readonly PostRepositoryInterface    $postsRepository,
    private readonly CommentRepositoryInterface $commentRepository,
  )
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->setName('fake-data:populate-db')
      ->setDescription('Populates DB with fake data')
      ->addArgument('users', InputArgument::OPTIONAL, 'number of created users')
      ->addArgument('posts', InputArgument::OPTIONAL, 'number of created posts')
      ->addArgument('comments', InputArgument::OPTIONAL, 'number of created comments');
  }

  /**
   * @throws InvalidArgumentException
   */
  protected function execute(
    InputInterface  $input,
    OutputInterface $output,
  ): int
  {
    $usersNum = $usersNum = $input->getArgument('users') ? $usersNum = $input->getArgument('users') : 1;
    $postsNum = $input->getArgument('posts') ? $input->getArgument('posts') :  1;
    $commentsNum = $input->getArgument('comments') ? $input->getArgument('comments') : 1;

    // Создаём десять пользователей
    $users = [];
    for ($i = 0; $i < $usersNum; $i++) {

      $user = $this->createFakeUser();
      $users[] = $user;
      $output->writeln('User created: '  . $user->username());
    }
    // От имени каждого пользователя
    // создаём по двадцать статей

    foreach ($users as $user) {
      for ($i = 0; $i < $postsNum; $i++) {
        $post = $this->createFakePost($user);
        $output->writeln('Post created: ' . $post->getTitle());

        for ($i = 0; $i < $commentsNum; $i++) {
          $comment = $this->createFakeComment($post, $user);
          $output->writeln('Comment created: ' . $comment->getText());
        }

      }
    }

    return Command::SUCCESS;
  }

  /**
   * @throws InvalidArgumentException
   */
  private function createFakeUser(): User
  {
    $user = User::createFrom(
      new Name(
        $this->faker->firstName,
        $this->faker->lastName
      ),
      $this->faker->userName,
      $this->faker->password,
    );
    $this->usersRepository->save($user);
    return $user;
  }

  /**
   * @throws InvalidArgumentException
   */
  private function createFakePost(User $user): Post
  {
    $post = new Post(
      UUID::random(),
      $user,
      $this->faker->sentence(6, true),
      $this->faker->realText
    );
    // Сохраняем статью в репозиторий
    $this->postsRepository->save($post);
    return $post;
  }

  /**
   * @throws InvalidArgumentException
   */
  private function createFakeComment(Post $post, User $user): Comment
  {
    $comment = new Comment(
      UUID::random(),
      $post,
      $user,
      $this->faker->realText
    );
    // Сохраняем статью в репозиторий
    $this->commentRepository->save($comment);
    return $comment;
  }

}