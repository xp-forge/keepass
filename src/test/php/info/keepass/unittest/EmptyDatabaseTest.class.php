<?php namespace info\keepass\unittest;

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
  public function root_group() {
    with ($this->database(), function($db) {
      $this->assertEquals(new Group($this->root, '/'), $db->group('/'));
    });
  }

  #[@test]
  public function groups_are_empty() {
    with ($this->database(), function($db) {
      $this->assertEquals([], iterator_to_array($db->groups()));
    });
  }

  #[@test]
  public function passwords_are_empty() {
    with ($this->database(), function($db) {
      $this->assertEquals([], iterator_to_array($db->passwords()));
    });
  }
}