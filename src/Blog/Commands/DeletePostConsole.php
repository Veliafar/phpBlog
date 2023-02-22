<?php


namespace Veliafar\PhpBlog\Blog\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Veliafar\PhpBlog\Blog\UUID;

class DeletePostConsole extends Command
{
  public function __construct(
    private PostRepositoryInterface    $postsRepository,
    private CommentRepositoryInterface $commentRepository,
  )
  {
    parent::__construct();
  }

  // Конфигурируем команду
  protected function configure(): void
  {
    $this
      ->setName('posts:delete')
      ->setDescription('Deletes a post')
      ->addArgument(
        'uuid',
        InputArgument::REQUIRED,
        'UUID of a post to delete'
      )
      // Добавили опцию
      ->addOption(
        // Имя опции
        'check-existence',
        // Сокращённое имя
        'c',
        // Опция не имеет значения
        InputOption::VALUE_NONE,
        // Описание
        'Check if post actually exists',
      );
  }

  /**
   * @throws InvalidArgumentException
   */
  protected function execute(
    InputInterface  $input,
    OutputInterface $output,
  ): int
  {
    $question = new ConfirmationQuestion(
      'Delete post [Y/n]? ',
      false
    );
    if (
      !$this->getHelper('question')
        ->ask($input, $output, $question)
    ) {
      return Command::SUCCESS;
    }
    $uuid = new UUID($input->getArgument('uuid'));

    // Если опция проверки существования статьи установлена
    if ($input->getOption('check-existence')) {
      try {
        // Пытаемся получить статью
        $this->postsRepository->get($uuid);
      } catch (PostNotFoundException $e) {
        // Выходим, если статья не найдена
        $output->writeln($e->getMessage());
        return Command::FAILURE;
      }
    }

    $this->postsRepository->delete($uuid, $this->commentRepository);
    $output->writeln("Post $uuid deleted");
    return Command::SUCCESS;
  }
}