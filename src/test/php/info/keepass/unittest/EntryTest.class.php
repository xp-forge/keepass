<?php namespace info\keepass\unittest;

use info\keepass\{Entry, ProtectedValue};
use test\{Assert, Before, Test};
use util\UUID;

class EntryTest {
  private $entry;

  #[Before]
  public function entry() {
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
    
  #[Test]
  public function can_create() {
    new Entry($this->entry, '/Entry #1');
  }

  #[Test]
  public function path() {
    Assert::equals('/Entry #1', (new Entry($this->entry, '/Entry #1'))->path());
  }

  #[Test]
  public function uuid() {
    Assert::equals(new UUID('7d986517-3006-454d-b8aa-c2a9a314362e'), (new Entry($this->entry, '/Entry #1'))->uuid());
  }

  #[Test]
  public function title() {
    Assert::equals('Entry #1', (new Entry($this->entry, '/Entry #1'))->title());
  }

  #[Test]
  public function notes() {
    Assert::equals('Notes', (new Entry($this->entry, '/Entry #1'))->notes());
  }

  #[Test]
  public function url() {
    Assert::equals('http://example.com/', (new Entry($this->entry, '/Entry #1'))->url());
  }

  #[Test]
  public function username() {
    Assert::equals('test', (new Entry($this->entry, '/Entry #1'))->username());
  }

  #[Test]
  public function password() {
    Assert::equals(new ProtectedValue("\323\$c", "\274J\006"), (new Entry($this->entry, '/Entry #1'))->password());
  }

  #[Test]
  public function icon_field() {
    Assert::equals('42', (new Entry($this->entry, '/Entry #1'))->field('IconID'));
  }
}