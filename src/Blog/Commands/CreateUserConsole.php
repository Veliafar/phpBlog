<?php

namespace Veliafar\PhpBlog\Blog\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Veliafar\PhpBlog\Blog\User;

class CreateUserConsole extends Command
{
  public function __construct(
    private UserRepositoryInterface $usersRepository,
  )
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      // Указываем имя команды;
      // мы будем запускать команду,
      // используя это имя
      ->setName('users:create')
      // Описание команды
      ->setDescription('Creates new user')
      ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
      ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
      ->addArgument('username', InputArgument::REQUIRED, 'Username')
      ->addArgument('password', InputArgument::REQUIRED, 'Password');
  }


  /**
   * @throws InvalidArgumentException
   */
  protected function execute(
    InputInterface  $input,
    OutputInterface $output,
  ): int
  {
    // Для вывода сообщения вместо логгера
    // используем объект типа OutputInterface
    $output->writeln('Create user command started');
    // Вместо использования нашего класса Arguments
    // получаем аргументы из объекта типа InputInterface
    $username = $input->getArgument('username');
    if ($this->userExists($username)) {
      // Используем OutputInterface вместо логгера
      $output->writeln("User already exists: $username");
      // Завершаем команду с ошибкой
      return Command::FAILURE;
    }

    $user = User::createFrom(
      new Name(
        $input->getArgument('first_name'),
        $input->getArgument('last_name')
      ),
      $username,
      $input->getArgument('password'),
    );
    //
    $this->usersRepository->save($user);
    // Используем OutputInterface вместо логгера
    $output->writeln('User created: ' . $user->uuid());
    // Возвращаем код успешного завершения
    return Command::SUCCESS;
  }

  // Полностью перенесли из класса CreateUserCommand
  private function userExists(string $username): bool
  {
    try {
      $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException) {
      return false;
    }
    return true;
  }


}