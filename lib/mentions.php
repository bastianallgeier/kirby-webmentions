<?php

namespace Kirby\Webmentions;

use Field;
use Collection;
use Dir;
use Page;
use Data;
use Str;
use V;
use Remote;
use Tpl;
use Exception;

class Mentions extends Collection {

  public $options  = array();
  public $page     = null;
  public $root     = null;
  public $headline = null;

  public function __construct($params = array()) {

    $defaults = array(
      'page'     => page(),
      'headline' => 'Mentions'
    );

    if(is_a($params, 'Page')) {
      $params = array('page' => $params);
    } else if(is_string($params)) {
      $params = array('headline' => $params);
    }

    $this->options  = array_merge($defaults, $params);
    $this->page     = $this->options['page'];
    $this->root     = $this->page->root() . DS . '.webmentions';
    $this->headline = new Field($this->page, 'headline', $this->options['headline']);

    if(!is_dir($this->root)) return;

    $files = dir::read($this->root); 

    // flip direction
    rsort($files);

    foreach($files as $file) {

      // skip the pings cache
      if($file == 'pings.json') continue;

      // register a new webmention

      try {
        $mention = new Mention($this->page, $this->root . DS . $file);
        $this->append($mention->id(), $mention);        
      } catch(Exception $e) {

      }

    }

  }

  public function ping() {

    // check for an existing ping cache
    $cache = $this->root . DS . 'pings.json';

    if(file_exists($cache)) return;

    // The Regular Expression filter
    $expression = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    $triggered  = array();
    $endpoints  = array();

    // Check if there is a url in the text
    if(preg_match_all($expression, (string)$this->page->text(), $urls)) {
      foreach($urls[0] as $url) {      
        if(!in_array($url, $triggered)) {
          if($endpoint = $this->trigger($url)) {
            $endpoints[] = $endpoint;
          }
          $triggered[] = $url;
        }
      }

    } 

    data::write($cache, $endpoints);

  }

  public function trigger($url) {

    $response = remote::get($url);
    $html     = $response->content();

    if(preg_match_all('!\<link(.*?)\>!i', $html, $links)) {

      foreach($links[0] as $link) {

        if(!str::contains($link, 'rel="webmention"')) {
          continue;
        }
        
        if(!preg_match('!href="(.*?)"!', $link, $match)) {
          continue;
        }

        $endpoint = $match[1];
        $src      = $this->page->url();
        $target   = $url;

        // invalid endpoint
        if(!v::url($endpoint)) {
          continue;
        }

        remote::post($endpoint, array(
          'data' => array(
            'source' => $src,
            'target' => $target
          )
        ));

        return $endpoint;
                
      }

    }

  }

  public function toHtml() {

    $snippet = dirname(__DIR__) . DS . 'snippets' . DS . 'mentions.php';

    return tpl::load($snippet, array(
      'mentions'  => $this,
      'likes'     => $this->filterBy('type', 'like'),
      'replies'   => $this->filterBy('type', 'reply'),
      'mentions'  => $this->filterBy('type', 'mention'),
      'reposts'   => $this->filterBy('type', 'repost'),
      'headline'  => $this->headline
    ));

  }

  public function __toString() {
    return (string)$this->toHtml();
  }

}