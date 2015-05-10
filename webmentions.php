<?php 

load(array(
  'kirby\\webmentions\\endpoint' => __DIR__ . DS . 'lib' . DS . 'endpoint.php',
  'kirby\\webmentions\\mentions' => __DIR__ . DS . 'lib' . DS . 'mentions.php',
  'kirby\\webmentions\\mention'  => __DIR__ . DS . 'lib' . DS . 'mention.php',
  'kirby\\webmentions\\author'   => __DIR__ . DS . 'lib' . DS . 'author.php',
));

require(__DIR__ . DS . 'helpers.php');

new Kirby\Webmentions\Endpoint;