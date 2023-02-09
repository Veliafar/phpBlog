<?php

namespace Veliafar\PhpBlog\Http\Actions;

use Veliafar\PhpBlog\Http\Request;
use Veliafar\PhpBlog\Http\Response;

interface ActionInterface
{
  public function handle(Request $request): Response;
}