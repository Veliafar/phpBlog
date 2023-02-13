<?php

namespace Veliafar\PhpBlog\Blog\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AppException implements NotFoundExceptionInterface
{

}