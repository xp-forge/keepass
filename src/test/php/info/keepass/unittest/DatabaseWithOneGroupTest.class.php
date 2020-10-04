<?php namespace info\keepass\unittest;

use info\keepass\{Entry, Group, ProtectedValue};
use lang\ElementNotFoundException;
use unittest\{Expect, Test};

class DatabaseWithOneGroupTest extends AbstractDatabaseTest {
  const GROUPID = '572dbca6-50a0-c34b-b963-43ed3f673fd9';
  const ENTRYID = '6c53db9b-7245-1d4d-b9eb-8ae40deb1fe1';

  private $entry, $group;
  protected $fixture= 'one-group';

  /** @return void */
  public function setUp() {
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

  #[Test]
  public function test_group() {
    with ($this->database(), function($db) {
      $this->assertEquals(new Group($this->group, '/Test'), $db->group('/Test'));
    });
  }

  #[Test]
  public function test_password() {
    with ($this->database(), function($db) {
      $this->assertEquals($this->entry['String']['Password'], $db->password('/Test/Test'));
    });
  }

  #[Test]
  public function all_passwords_in_test_group() {
    with ($this->database(), function($db) {
      $this->assertEquals(['/Test/Test' => $this->entry['String']['Password']], iterator_to_array($db->passwords('/Test')));
    });
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function non_existant_password() {
    with ($this->database(), function($db) {
      $db->password('/Test/Non-Existant');
    });
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function password_in_non_existant_folder() {
    with ($this->database(), function($db) {
      $db->password('/Non-Existant/Password');
    });
  }

  #[Test]
  public function groups_in_root() {
    with ($this->database(), function($db) {
      $this->assertEquals([self::GROUPID => new Group($this->group, '/Test')], iterator_to_array($db->groups()));
    });
  }

  #[Test]
  public function subgroups_of_test_group_are_empty() {
    with ($this->database(), function($db) {
      $this->assertEquals([], iterator_to_array($db->groups('/Test')));
    });
  }

  #[Test]
  public function entries_in_test_group() {
    with ($this->database(), function($db) {
      $this->assertEquals([self::ENTRYID => new Entry($this->entry, '/Test/Test')], iterator_to_array($db->group('/Test')->entries()));
    });
  }

  #[Test]
  public function entries_in_root() {
    with ($this->database(), function($db) {
      $this->assertEquals([], iterator_to_array($db->group('/')->entries()));
    });
  }
}