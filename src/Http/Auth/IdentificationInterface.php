<?php

namespace Veliafar\PhpBlog\Http\Auth;

use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Http\Request;

interface IdentificationInterface
{
 public function user(Request $request): User;
}