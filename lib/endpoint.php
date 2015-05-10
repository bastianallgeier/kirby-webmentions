<?php

namespace Kirby\Webmentions;

use V;
use Str;
use Response;
use F;

class Endpoint {

  public function __construct() {

    $endpoint = $this;

    kirby()->routes(array(
      array(
        'pattern' => 'webmention', 
        'method'  => 'GET|POST', 
        'action'  => function() use($endpoint) {        
          echo $endpoint->start();
        }
      )
    ));

  }

  public function start() {

    $src    = get('source');
    $target = get('target');

    if(!v::url($src)) {
      return response::error('Invalid source');
    }

    if(!v::url($target)) {
      return response::error('Invalid target');
    }

    if(!str::contains($target, site()->url())) {
      return response::error('Invalid target');
    }

    require_once(dirname(__DIR__) . DS . 'vendor' . DS . 'mf2.php');
    require_once(dirname(__DIR__) . DS . 'vendor' . DS . 'comments.php');

    $data   = \Mf2\fetch($src);
    $result = \IndieWeb\comments\parse($data['items'][0], $src);

    if(empty($result)) {
      return response::error('Probably spam');
    }

    $path = ltrim(str_replace(site()->url(), '', $target), '/');

    if(!empty($path) and $page = page($path)) {

      if(!empty($result['published'])) {
        $time = strtotime($result['published']);
      } else {
        $time = time();
        $result['published'] = date('c');
      }

      $json = json_encode($result);
      $hash = sha1($json);
      $file = $page->root() . DS . '.webmentions' . DS . $time . '-' . $hash . '.json';

      f::write($file, $json);

      return response::success('yay', $result, 202);

    } else {
      return response::error('Invalid page');
    }

  }

}