<?php namespace info\keepass;

class Node {
  public $tag;
  public $protected;
  public $value;
  public $children;

  public function __construct($tag, $protected= false, $value= null, $children= []) {
    $this->tag= $tag;
    $this->protected= $protected;
    $this->value= $value;
    $this->children= $children;
  }
}