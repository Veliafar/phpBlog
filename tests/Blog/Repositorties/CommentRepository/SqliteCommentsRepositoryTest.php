<?php

namespace Veliafar\PhpBlog\Blog\Repositorties\CommentRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\Exceptions\CommentNotFoundException;
use Veliafar\PhpBlog\Blog\Name;
use Veliafar\PhpBlog\Blog\Post;
use Veliafar\PhpBlog\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Blog\UUID;

class SqliteCommentsRepositoryTest extends TestCase
{
  public function testItThrowAnExceptionWhenCommentNotFound(): void
  {
    $connectionMock = $this->createStub(PDO::class);
    $statementStub = $this->createStub(PDOStatement::class);
    $statementStub->method('fetch')->willReturn(false);
    $connectionMock->method('prepare')->willReturn($statementStub);

    $usersRepository = new SqliteUsersRepository($connectionMock);
    $postRepository = new SqlitePostsRepository($connectionMock, $usersRepository);
    $repository = new SqliteCommentsRepository($connectionMock, $postRepository, $usersRepository);
    $this->expectException(CommentNotFoundException::class);
    $this->expectExceptionMessage('Cannot get comment: 123e4567-e89b-12d3-a456-426614174000');

    $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
  }

  public function testItSaveCommentToDatabase(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $usersRepositoryStub = $this->createStub(SqliteUsersRepository::class);
    $postsRepositoryStub = $this->createStub(SqlitePostsRepository::class);
    $statementMock = $this->createMock(PDOStatement::class);
    $statementMock
      ->expects($this->once())
      ->method('execute')
      ->with([ // с единственным аргументом - массивом
        ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':post_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':text' => 'Nikitin',
      ]);

    $connectionStub->method('prepare')->willReturn($statementMock);

    $repository = new SqliteCommentsRepository($connectionStub, $postsRepositoryStub, $usersRepositoryStub);
    $user = new User(
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      new Name('First', 'Second'),
      'ivan123',
    );
    $post = new Post(
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      $user,
      'Ivan',
      'Nikitin'
    );
    $comment = new Comment(
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      $post,
      $user,
      'Nikitin'
    );
    $repository->save($comment);
  }

  public function testItGetCommentByUuid(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $statementMock = $this->createMock(PDOStatement::class);
    $statementMock
      ->expects($this->exactly(4))
      ->method('execute')
      ->with([ // с единственным аргументом - массивом
        'uuid' => '123e4567-e89b-12d3-a456-426614174000',
      ]);
    $connectionStub->method('prepare')->willReturn($statementMock);
    $statementMock->method('fetch')->willReturn([ // с единственным аргументом - массивом
      'uuid' => '123e4567-e89b-12d3-a456-426614174000',
      'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
      'post_uuid' => '123e4567-e89b-12d3-a456-426614174000',
      'title' => 'ivan123',
      'text' => 'ivan123',
      'username' => 'ivan123',
      'first_name' => 'Ivan',
      'last_name' => 'Nikitin',
    ]);

    $usersRepository = new SqliteUsersRepository($connectionStub);
    $postsRepository = new SqlitePostsRepository($connectionStub, $usersRepository);
    $repository = new SqliteCommentsRepository($connectionStub, $postsRepository, $usersRepository);
    $post = $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string)$post->uuid());
  }
}