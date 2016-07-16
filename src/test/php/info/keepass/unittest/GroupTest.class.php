<?php namespace info\keepass\unittest;

use info\keepass\Group;
use info\keepass\Entry;
use info\keepass\ProtectedValue;
use util\UUID;

class GroupTest extends \unittest\TestCase {
  private $entry, $child, $group;

  /** @return void */
  public function setUp() {
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

  #[@test]
  public function entries() {
    $this->assertEquals(
      ['7d986517-3006-454d-b8aa-c2a9a314362e' => new Entry($this->entry, '/Test/Entry #1')],
      iterator_to_array((new Group($this->group, '/Test'))->entries())
    );
  }

  #[@test]
  public function groups() {
    $this->assertEquals(
      ['1bbdd52d-f1f0-914f-9da3-c845f609a87d' => new Group($this->child, '/Test/Child')],
      iterator_to_array((new Group($this->group, '/Test'))->groups())
    );
  }

  #[@test]
  public function passwords() {
    $this->assertEquals(
      ['/Test/Entry #1' => new ProtectedValue("\323\$c", "\274J\006")],
      iterator_to_array((new Group($this->group, '/Test'))->passwords())
    );
  }
}

