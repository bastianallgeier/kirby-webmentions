<?php

namespace Kirby\Webmentions;

use A;
use Field;
use Obj;
use F;
use Media;
use Str;
use Url;
use C;
use Remote;
use Tpl;
use V;

class Author extends Obj {

  public $mention = null;
  public $data    = array();
  public $page    = null;
  public $name    = null;
  public $url     = null;
  public $photo   = null;

  public function __construct($mention) {

    $this->mention = $mention;
    $this->data    = $mention->data['author'];
    $this->page    = $mention->page;

    if(empty($this->data['url'])) {
      $this->data['url'] = $this->mention->data['url'];
    }

    if(empty($this->data['name'])) {
      $this->data['name'] = url::short(url::base($this->data['url']));
    }

    $this->field('url');
    $this->field('name');

  }

  public function field($key, $field = null) {
    if(is_null($field)) $field = $key;

    $value = a::get($this->data, $field);

    if($key == 'url' and !v::url($value)) {
      $value = null;
    }

    $this->$key = new Field($this->page, $key, esc($value));
  }

  public function photo() {

    if(!is_null($this->photo)) return $this->photo;

    $extension = f::extension($this->data['photo']);
    $filename  = rtrim(sha1($this->url) . '.' . $extension, '.');    
    $path      = c::get('webmentions.images', 'assets/images/mentions');
    $root      = kirby()->roots()->index() . DS . str_replace('/', DS, $path) . DS . $filename;
    $url       = kirby()->urls()->index() . '/' . $path . '/' . $filename;
    $photo     = new Media($root, $url);
    
    if(!$photo->exists()) {    

      $image   = remote::get($this->data['photo']);
      $allowed = array('image/jpeg', 'image/png', 'image/gif');

      f::write($root, $image->content());

      if(!in_array($photo->mime(), $allowed) or $photo->size() == 0) {
        $photo->delete();
      }

    }

    if(!$photo->exists() or !$photo->type() == 'image') {
      $photo = new Obj(array(
        'url'    => $this->data['photo'],
        'exists' => false
      ));
    }

    return $this->photo = $photo;    

  }

  public function toHtml() {

    $snippet = kirby()->roots()->snippets() . DS . 'webmentions' . DS . 'author.php';

    if(!file_exists($snippet)) {
      $snippet = dirname(__DIR__) . DS . 'snippets' . DS . 'author.php';
    }

    return tpl::load($snippet, array(
      'author'  => $this,
      'mention' => $this->mention
    ));

  }

  public function __toString() {
    return (string)$this->toHtml();
  }

}