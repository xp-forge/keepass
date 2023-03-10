<?php namespace info\keepass\unittest;

use info\keepass\{Entry, Group, ProtectedValue};
use test\{Assert, Before, Test};
use util\UUID;

class GroupTest {
  private $entry, $child, $group;

  #[Before]
  public function initialize() {
    $this->entry= [
      'UUID'            => 'fZhlFzAGRU24qsKpoxQ2Lg==',
      'IconID'          => '42',
      'ForegroundColor' => null,
      'BackgroundColor' => null,
      'OverrideURL'     => null,
      'Tags'            => null,
      'Times'           => '',
      'String'          => [
        'Notes'    => null,
        'Password' => new ProtectedValue("\323\$c", "\274J\006"),
        'Title'    => 'Entry #1',
        'URL'      => null,
        'UserName' => null
      ]
    ];
    $this->child= [
      'UUID'                    => 'G73VLfHwkU+do8hF9gmofQ==',
      'Name'                    => 'Child',
      'Notes'                   => null,
      'IconID'                  => '48',
      'Times'                   => '',
      'IsExpanded'              => 'False',
      'DefaultAutoTypeSequence' => null,
      'EnableAutoType'          => 'null',
      'EnableSearching'         => 'null',
      'LastTopVisibleEntry'     => 'AAAAAAAAAAAAAAAAAAAAAA==',
    ];
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
      'Entry'                   => [
        'fZhlFzAGRU24qsKpoxQ2Lg==' => $this->entry
      ],
      'Group'                   => [
        'G73VLfHwkU+do8hF9gmofQ==' => $this->child
      ]
    ];
  }
    
  #[Test]
  public function can_create() {
    new Group($this->group, '/Test');
  }

  #[Test]
  public function path() {
    Assert::equals('/Test', (new Group($this->group, '/Test'))->path());
  }

  #[Test]
  public function uuid() {
    Assert::equals(new UUID('572dbca6-50a0-c34b-b963-43ed3f673fd9'), (new Group($this->group, '/Test'))->uuid());
  }

  #[Test]
  public function name() {
    Assert::equals('Test', (new Group($this->group, '/Test'))->name());
  }

  #[Test]
  public function notes() {
    Assert::equals('Notes', (new Group($this->group, '/Test'))->notes());
  }

  #[Test]
  public function icon_field() {
    Assert::equals('48', (new Group($this->group, '/Test'))->field('IconID'));
  }

  #[Test]
  public function entries() {
    Assert::equals(
      ['7d986517-3006-454d-b8aa-c2a9a314362e' => new Entry($this->entry, '/Test/Entry #1')],
      iterator_to_array((new Group($this->group, '/Test'))->entries())
    );
  }

  #[Test]
  public function groups() {
    Assert::equals(
      ['1bbdd52d-f1f0-914f-9da3-c845f609a87d' => new Group($this->child, '/Test/Child')],
      iterator_to_array((new Group($this->group, '/Test'))->groups())
    );
  }

  #[Test]
  public function passwords() {
    Assert::equals(
      ['/Test/Entry #1' => new ProtectedValue("\323\$c", "\274J\006")],
      iterator_to_array((new Group($this->group, '/Test'))->passwords())
    );
  }
}