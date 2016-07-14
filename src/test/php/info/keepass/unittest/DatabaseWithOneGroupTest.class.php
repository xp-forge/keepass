<?php namespace info\keepass\unittest;

use info\keepass\KeePassDatabase;
use info\keepass\Key;
use info\keepass\Group;
use info\keepass\Entry;
use info\keepass\Header;
use info\keepass\ProtectedValue;
use lang\ClassLoader;
use lang\ElementNotFoundException;

class DatabaseWithOneGroupTest extends \unittest\TestCase {
  const GROUPID = '572dbca6-50a0-c34b-b963-43ed3f673fd9';
  const ENTRYID = '6c53db9b-7245-1d4d-b9eb-8ae40deb1fe1';

  private $key, $input, $entry, $group;

  /** @return void */
  public function setUp() {
    $this->key= new Key('one-group');
    $this->input= ClassLoader::getDefault()->getResourceAsStream('fixtures/one-group.kdbx')->in();
    $this->entry= [
      'UUID'            => 'bFPbm3JFHU2564rkDesf4Q==',
      'IconID'          => '0',
      'ForegroundColor' => null,
      'BackgroundColor' => null,
      'OverrideURL'     => null,
      'Tags'            => null,
      'Times'           => '',
      'String' => [
        'Notes'    => 'Unittest fixture',
        'Password' => new ProtectedValue("\205>{'\321 ", "\366[\030U\264T"),
        'Title'    => 'Test',
        'URL'      => 'http://example.com/',
        'UserName' => 'dummy',
      ],
      'History' => [
      ]
    ];
    $this->group= [
      'UUID'                    => 'Vy28plCgw0u5Y0PtP2c/2Q==',
      'Name'                    => 'Test',
      'Notes'                   => 'The test group',
      'IconID'                  => '48',
      'Times'                   => '',
      'IsExpanded'              => 'True',
      'DefaultAutoTypeSequence' => null,
      'EnableAutoType'          => 'null',
      'EnableSearching'         => 'null',
      'LastTopVisibleEntry'     => 'AAAAAAAAAAAAAAAAAAAAAA==',
      'Entry' => [
        'bFPbm3JFHU2564rkDesf4Q==' => $this->entry
      ]
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
  public function test_group() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals(new Group($this->group), $db->group('/Test'));
    });
  }

  #[@test]
  public function test_password() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals($this->entry['String']['Password'], $db->password('/Test/Test'));
    });
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function non_existant_password() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $db->password('/Test/Non-Existant');
    });
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function password_in_non_existant_folder() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $db->password('/Non-Existant/Password');
    });
  }

  #[@test]
  public function groups_in_root() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals([self::GROUPID => new Group($this->group)], iterator_to_array($db->groups()));
    });
  }

  #[@test]
  public function subgroups_of_test_group_are_empty() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals([], iterator_to_array($db->groups('/Test')));
    });
  }

  #[@test]
  public function entries_in_test_group() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals([self::ENTRYID => new Entry($this->entry)], iterator_to_array($db->group('/Test')->entries()));
    });
  }

  #[@test]
  public function entries_in_root() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals([], iterator_to_array($db->group('/')->entries()));
    });
  }
}