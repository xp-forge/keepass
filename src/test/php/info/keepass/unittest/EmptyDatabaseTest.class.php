<?php namespace info\keepass\unittest;

use info\keepass\Group;
use test\{Assert, Before, Test};

class EmptyDatabaseTest extends AbstractDatabaseTest {
  protected $fixture= 'empty';
  private $root;

  #[Before]
  public function root() {
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

  #[Test]
  public function root_group() {
    with ($this->database(), function($db) {
      Assert::equals(new Group($this->root, '/'), $db->group('/'));
    });
  }

  #[Test]
  public function groups_are_empty() {
    with ($this->database(), function($db) {
      Assert::equals([], iterator_to_array($db->groups()));
    });
  }

  #[Test]
  public function passwords_are_empty() {
    with ($this->database(), function($db) {
      Assert::equals([], iterator_to_array($db->passwords()));
    });
  }
}