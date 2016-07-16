<?php namespace info\keepass\unittest;

use info\keepass\Header;
use info\keepass\Group;

class EmptyDatabaseTest extends AbstractDatabaseTest {
  protected $fixture= 'empty';
  private $root;

  /** @return void */
  public function setUp() {
    $this->root= [
      'UUID'                    => 'zcS4XXVOskKF32QfoqxFcQ==',
      'Name'                    => 'Database Root',
      'Notes'                   => null,
      'IconID'                  => '48',
      'Times'                   => '',
      'IsExpanded'              => 'True',
      'DefaultAutoTypeSequence' => null,
      'EnableAutoType'          => 'null',
      'EnableSearching'         => 'null',
      'LastTopVisibleEntry'     => 'AAAAAAAAAAAAAAAAAAAAAA=='
    ];
  }

  #[@test]
  public function header() {
    with ($this->database(), function($db) {
      $this->assertInstanceOf(Header::class, $db->header());
    });
  }

  #[@test]
  public function root() {
    with ($this->database(), function($db) {
      $this->assertEquals(new Group($this->root), $db->group('/'));
    });
  }

  #[@test]
  public function groups_are_empty() {
    with ($this->database(), function($db) {
      $this->assertEquals([], iterator_to_array($db->groups()));
    });
  }
}