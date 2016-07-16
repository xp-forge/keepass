<?php namespace info\keepass\unittest;

use info\keepass\Group;
use util\UUID;

class GroupTest extends \unittest\TestCase {
  private $group;

  /** @return void */
  public function setUp() {
    $this->group= [
      'UUID'                    => 'Vy28plCgw0u5Y0PtP2c/2Q==',
      'Name'                    => 'Test',
      'Notes'                   => 'Notes',
      'IconID'                  => '48',
      'Times'                   => '',
      'IsExpanded'              => 'True',
      'DefaultAutoTypeSequence' => null,
      'EnableAutoType'          => 'null',
      'EnableSearching'         => 'null',
      'LastTopVisibleEntry'     => 'AAAAAAAAAAAAAAAAAAAAAA==',
    ];
  }
    
  #[@test]
  public function can_create() {
    new Group($this->group, '/Test');
  }

  #[@test]
  public function path() {
    $this->assertEquals('/Test', (new Group($this->group, '/Test'))->path());
  }

  #[@test]
  public function uuid() {
    $this->assertEquals(new UUID('572dbca6-50a0-c34b-b963-43ed3f673fd9'), (new Group($this->group, '/Test'))->uuid());
  }

  #[@test]
  public function name() {
    $this->assertEquals('Test', (new Group($this->group, '/Test'))->name());
  }

  #[@test]
  public function notes() {
    $this->assertEquals('Notes', (new Group($this->group, '/Test'))->notes());
  }

  #[@test]
  public function icon_field() {
    $this->assertEquals('48', (new Group($this->group, '/Test'))->field('IconID'));
  }
}

