<?php 

function webmentions($options = array()) {

  $webmentions = new Kirby\Webmentions\Mentions($options); 
  $webmentions->ping();

  return $webmentions;

}

// like tag
kirbytext::$tags['like'] = array(

  'attr' => array(
    'text',
    'title',
    'rel',
    'target',
    'popup'
  ),
  'html' => function($tag) {

    return kirbytag(array(
      'link'   => $tag->attr('like'),
      'text'   => $tag->attr('text'),
      'title'  => $tag->attr('title'),
      'rel'    => $tag->attr('rel'),
      'target' => $tag->attr('target'),
      'popup'  => $tag->attr('popup'),
      'class'  => 'u-like-of',
    ));

  }
);