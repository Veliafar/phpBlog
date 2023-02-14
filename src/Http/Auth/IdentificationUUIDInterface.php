<?php

namespace Veliafar\PhpBlog\Http\Auth;

use Veliafar\PhpBlog\Blog\User;
use Veliafar\PhpBlog\Http\Request;

interface IdentificationUUIDInterface
{
 public function user(Request $request): User;
}