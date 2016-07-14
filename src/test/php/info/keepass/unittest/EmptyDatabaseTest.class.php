<?php namespace info\keepass\unittest;

use info\keepass\KeePassDatabase;
use info\keepass\Key;
use info\keepass\Header;
use info\keepass\Group;
use lang\ClassLoader;

class EmptyDatabaseTest extends \unittest\TestCase {
  private $key, $input;

  /** @return void */
  public function setUp() {
    $this->key= new Key('test');
    $this->input= ClassLoader::getDefault()->getResourceAsStream('fixtures/empty.kdbx')->in();
    $this->root= [
      'UUID'                    => 'jjLuM4mTBkCOy4HzFWpv5w==',
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
  public function open() {
    KeePassDatabase::open($this->input, $this->key)->close();
  }

  #[@test]
  public function header() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertInstanceOf(Header::class, $db->header());
    });
  }

  #[@test]
  public function root() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals(new Group($this->root), $db->group('/'));
    });
  }

  #[@test]
  public function groups_are_empty() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals([], iterator_to_array($db->groups()));
    });
  }
}