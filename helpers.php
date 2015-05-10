<?php 

function webmentions($options = array()) {

  $webmentions = new Kirby\Webmentions\Mentions($options); 
  $webmentions->ping();

  return $webmentions;

}