<?php

namespace Veliafar\PhpBlog\Blog\Repositories\CommentRepository;

use PDO;
use Veliafar\PhpBlog\Blog\Comment;
use Veliafar\PhpBlog\Blog\Exceptions\CommentNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\InvalidArgumentException;
use Veliafar\PhpBlog\Blog\Exceptions\PostNotFoundException;
use Veliafar\PhpBlog\Blog\Exceptions\UserNotFoundException;
use Veliafar\PhpBlog\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Veliafar\PhpBlog\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Veliafar\PhpBlog\Blog\UUID;

class SqliteCommentsRepository implements CommentRepositoryInterface
{
  public function __construct(
    private PDO                   $connection,
    private SqlitePostsRepository $postRepository,
    private SqliteUsersRepository $userRepository
  )
  {
  }

  public function save(Comment $comment): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)'
    );

    $statement->execute([
      ':uuid' => (string)$comment->uuid(),
      ':post_uuid' => (string)$comment->getPostUUID(),
      ':author_uuid' => (string)$comment->getUserUUID(),
      ':text' => $comment->getText(),
    ]);
  }


  /**
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   * @throws UserNotFoundException
   * @throws CommentNotFoundException
   */
  public function get(UUID $uuid): Comment
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM comments WHERE uuid = :uuid'
    );
    $statement->execute([
      'uuid' => (string)$uuid,
    ]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new CommentNotFoundException(
        "Cannot get comment: $uuid"
      );
    }


    $post = $this->postRepository->get(new UUID($result['post_uuid']));
    $user = $this->userRepository->get(new UUID($result['author_uuid']));

    return new Comment(
      new UUID($result['uuid']),
      $post,
      $user,
      $result['text'],
    );
  }

  /**
   * @throws UserNotFoundException
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   * @throws CommentNotFoundException
   */
  public function getAllByPostUUID(UUID $post_uuid): array
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM comments WHERE post_uuid = :post_uuid GROUP BY uuid'
    );
    $statement->execute([
      'post_uuid' => (string)$post_uuid,
    ]);


    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new CommentNotFoundException(
        "Cannot get comments: $post_uuid"
      );
    }

    $comments = [];
    foreach ($result as $rowComment) {
      $post = $this->postRepository->get(new UUID($rowComment['post_uuid']));
      $user = $this->userRepository->get(new UUID($rowComment['author_uuid']));
      $comments[] = new Comment(
        new UUID($rowComment['uuid']),
        $post,
        $user,
        $rowComment['text'],
      );
    }
    return $comments;
  }

  /**
   * @throws CommentNotFoundException
   */
  public function deleteAllCommentsOfPost(UUID $post_uuid): bool
  {
    $statement = $this->connection->prepare(
      'DELETE FROM comments WHERE post_uuid = :post_uuid'
    );
    $statement->execute([
      'post_uuid' => (string)$post_uuid,
    ]);
    $result = $statement->rowCount();
    if ($result === 0) {
      throw new CommentNotFoundException(
        "Cannot delete comment: $post_uuid"
      );
    }
    return true;
  }

  /**
   * @throws CommentNotFoundException
   */
  public function delete(UUID $uuid): void
  {
    $statement = $this->connection->prepare(
      'DELETE FROM comments WHERE uuid = :uuid'
    );
    $statement->execute([
      'uuid' => (string)$uuid,
    ]);

    $result = $statement->rowCount();
    if ($result === 0) {
      throw new CommentNotFoundException(
        "Cannot delete comment: $uuid"
      );
    }
  }

}