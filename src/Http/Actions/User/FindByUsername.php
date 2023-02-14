<?php

namespace Veliafar\PhpBlog\Http\Actions\User;

use Psr\Log\LoggerInterface;
use Veliafar\PhpBlog\Http\Actions\ActionInterface;
use Veliafar\PhpBlog\Http\Auth\IdentificationUsernameInterface;
use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;
use Veliafar\PhpBlog\Http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
  public function __construct(
    private IdentificationUsernameInterface $identification,
    private LoggerInterface                 $logger,
  )
  {
  }

  // Функция, описанная в контракте
  public function handle(Request $request): Response
  {
    $user = $this->identification->user($request, true);
    $username = $user->username();
    $this->logger->info("User found: $username");
    return new SuccessfulResponse([
      'username' => $username,
      'name' => $user->name()->first() . ' ' . $user->name()->last(),
    ]);
  }

}