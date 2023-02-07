<?php

use Veliafar\PhpBlog\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_REQUEST);

try {
  $path = $request->path();
} catch (exception) {

}