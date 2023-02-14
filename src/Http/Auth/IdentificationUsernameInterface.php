<?php

namespace Veliafar\PhpBlog\Http\Auth;

use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Http\Request;

interface IdentificationUsernameInterface
{
 public function user(Request $request, bool $isQuery): User;
}