<?php namespace info\keepass\unittest;

use info\keepass\Entry;
use info\keepass\ProtectedValue;
use util\UUID;

class EntryTest extends \unittest\TestCase {
  private $entry;

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
      'String' => [
        'Notes'    => 'Notes',
        'Password' => new ProtectedValue("\323\$c", "\274J\006"),
        'Title'    => 'Entry #1',
        'URL'      => 'http://example.com/',
        'UserName' => 'test'
      ],
      'History' => [
      ]
    ];
  }
    
  #[@test]
  public function can_create() {
    new Entry($this->entry, '/Entry #1');
  }

  #[@test]
  public function path() {
    $this->assertEquals('/Entry #1', (new Entry($this->entry, '/Entry #1'))->path());
  }

  #[@test]
  public function uuid() {
    $this->assertEquals(new UUID('7d986517-3006-454d-b8aa-c2a9a314362e'), (new Entry($this->entry, '/Entry #1'))->uuid());
  }

  #[@test]
  public function title() {
    $this->assertEquals('Entry #1', (new Entry($this->entry, '/Entry #1'))->title());
  }

  #[@test]
  public function notes() {
    $this->assertEquals('Notes', (new Entry($this->entry, '/Entry #1'))->notes());
  }

  #[@test]
  public function url() {
    $this->assertEquals('http://example.com/', (new Entry($this->entry, '/Entry #1'))->url());
  }

  #[@test]
  public function username() {
    $this->assertEquals('test', (new Entry($this->entry, '/Entry #1'))->username());
  }

  #[@test]
  public function password() {
    $this->assertEquals(new ProtectedValue("\323\$c", "\274J\006"), (new Entry($this->entry, '/Entry #1'))->password());
  }

  #[@test]
  public function icon_field() {
    $this->assertEquals('42', (new Entry($this->entry, '/Entry #1'))->field('IconID'));
  }
}

