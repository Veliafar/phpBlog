<?php

namespace Veliafar\PhpBlog\Blog\Repositorties\PostRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class SqlitePostsRepositoryTest extends TestCase
{
  public function testItThrowAnExceptionWhenPostNotFound(): void
  {
    $connectionMock = $this->createStub(PDO::class);
    $statementStub = $this->createStub(PDOStatement::class);
    $statementStub->method('fetch')->willReturn(false);
    $connectionMock->method('prepare')->willReturn($statementStub);

    $usersRepository = new SqliteUsersRepository($connectionMock);
    $repository = new SqlitePostsRepository($connectionMock, $usersRepository);
    $this->expectException(PostNotFoundException::class);
    $this->expectExceptionMessage('Cannot get post: 123e4567-e89b-12d3-a456-426614174000');

    $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
  }

  public function testItSavePostToDatabase(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $usersRepositoryStub = $this->createStub(SqliteUsersRepository::class);
    $statementMock = $this->createMock(PDOStatement::class);
    $statementMock
      ->expects($this->once())
      ->method('execute')
      ->with([ // с единственным аргументом - массивом
        ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':title' => 'Ivan',
        ':text' => 'Nikitin',
      ]);

    $connectionStub->method('prepare')->willReturn($statementMock);

    $repository = new SqlitePostsRepository($connectionStub, $usersRepositoryStub);

    $user = new User(
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      new Name('First', 'Second'),
      'ivan123',
      'password'
    );
    $post = new Post(
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      $user,
      'Ivan',
      'Nikitin'
    );
    $repository->save($post);
  }

  public function testItGetPostByUuid(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $statementMock = $this->createMock(PDOStatement::class);
    $statementMock
      ->expects($this->exactly(2))
      ->method('execute')
      ->with([ // с единственным аргументом - массивом
        'uuid' => '123e4567-e89b-12d3-a456-426614174000',
      ]);
    $connectionStub->method('prepare')->willReturn($statementMock);
    $statementMock->method('fetch')->willReturn([ // с единственным аргументом - массивом
      'uuid' => '123e4567-e89b-12d3-a456-426614174000',
      'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
      'title' => 'ivan123',
      'text' => 'ivan123',
      'username' => 'ivan123',
      'first_name' => 'Ivan',
      'last_name' => 'Nikitin',
      'password' => 'password'

    ]);

    $usersRepository = new SqliteUsersRepository($connectionStub);
    $repository = new SqlitePostsRepository($connectionStub, $usersRepository);
    $post = $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string)$post->uuid());
  }
}