<?php

namespace Kirby\Webmentions;

use A;
use Obj;
use Data;
use Field;
use Tpl;
use V;
use Exception;

class Mention extends Obj {

  public $data   = null;
  public $id     = null;
  public $page   = null;
  public $file   = null;
  public $author = null;
  public $title  = null;
  public $text   = null;
  public $date   = null;
  public $url    = null;

  public function __construct($page, $file) {

    $data = data::read($file);

    if(!is_array($data) or empty($data)) {
      throw new Exception('Invalid web mention');
    }

    if(empty($data['url'])) {
      throw new Exception('No url found');
    }


    $this->data   = $data;
    $this->page   = $page;
    $this->file   = $file;
    $this->author = new Author($this);
    $this->id     = sha1($file);

    $this->convertTwitterFavorite();
    $this->convertTwitterRepost();

    $this->field('title', 'name');
    $this->field('text');
    $this->field('url');
    $this->field('type');
    $this->field('rsvp');

    $this->date = new Field($this->page, 'date', strtotime($data['published']));      

  } 

  public function convertTwitterFavorite() {

    if(!empty($this->data['url']) and preg_match('!https:\/\/twitter.com\/(.*?)\/status!', $this->data['url'])) {
      
      if(!empty($this->data['name']) and $this->data['name'] == 'likes this.') {
        $this->data['type'] = 'like';        
      } 

    }

  }

  public function convertTwitterRepost() {

    if(!empty($this->data['url']) and preg_match('!https:\/\/twitter.com\/(.*?)\/status!', $this->data['url'])) {
      
      if(!empty($this->data['name']) and $this->data['name'] == 'reposts this.') {
        $this->data['type'] = 'repost';        
      } 

    }

  }


  public function field($key, $field = null) {

    if(is_null($field)) $field = $key;

    $value = a::get($this->data, $field);

    if($key == 'url' and !v::url($value)) {
      $value = null;
    }

    $this->$key = new Field($this->page, $key, esc($value));
  }


  public function is($type) {
    return $this->type->value == $type;
  }

  public function date($format = null) {
    if($format) {
      $handler = kirby()->option('date.handler', 'date');
      return $handler($format, $this->date->value);
    } else { 
      return $this->published;
    }
  }

  public function toHtml() {

    $snippet = kirby()->roots()->snippets() . DS . 'webmentions' . DS . 'types' . DS . $this->type() . '.php';

    if(!file_exists($snippet)) {
      $snippet = dirname(__DIR__) . DS . 'snippets' . DS . 'types' . DS . $this->type() . '.php';
    }

    return tpl::load($snippet, array(
      'mention' => $this,
      'author'  => $this->author
    ));

  }

  public function __toString() {
    return (string)$this->toHtml();
  }

}

