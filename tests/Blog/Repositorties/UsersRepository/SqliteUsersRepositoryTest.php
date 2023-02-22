<?php

namespace Veliafar\PhpBlog\Blog\Repositorties\UsersRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;


class SqliteUsersRepositoryTest extends TestCase
{

  /**
   * @throws InvalidArgumentException
   */
  public function testItThrowAnExceptionWhenUserNotFound(): void
  {

    $connectionMock = $this->createStub(PDO::class);
    $statementStub = $this->createStub(PDOStatement::class);
    $statementStub->method('fetch')->willReturn(false);
    $connectionMock->method('prepare')->willReturn($statementStub);

    $repository = new SqliteUsersRepository($connectionMock);
    $this->expectException(UserNotFoundException::class);
    $this->expectExceptionMessage('Cannot get user: Ivan');

    $repository->getByUsername('Ivan');
  }

  public function testItSaveUserToDatabase(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $statementMock = $this->createMock(PDOStatement::class);
    $statementMock
      ->expects($this->once())
      ->method('execute')
      ->with([ // с единственным аргументом - массивом
        ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':username' => 'ivan123',
        ':first_name' => 'Ivan',
        ':last_name' => 'Nikitin',
        ':password' => 'password',
      ]);
    // 3. При вызове метода prepare стаб подключения
    // возвращает мок запроса
    $connectionStub->method('prepare')->willReturn($statementMock);
    // 1. Передаём в репозиторий стаб подключения
    $repository = new SqliteUsersRepository($connectionStub);
    // Вызываем метод сохранения пользователя
    $repository->save(
      new User( // Свойства пользователя точно такие,
      // как и в описании мока
        new UUID('123e4567-e89b-12d3-a456-426614174000'),
        new Name('Ivan', 'Nikitin'),
        'ivan123',
        'password'
      ));
  }
}