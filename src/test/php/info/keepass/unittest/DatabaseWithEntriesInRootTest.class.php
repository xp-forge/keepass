<?php namespace info\keepass\unittest;

use info\keepass\KeePassDatabase;
use info\keepass\Key;
use info\keepass\Entry;
use info\keepass\ProtectedValue;
use lang\ClassLoader;

class DatabaseWithEntriesInRootTest extends \unittest\TestCase {
  const ID_ONE = '7d986517-3006-454d-b8aa-c2a9a314362e';
  const ID_TWO = '1bbdd52d-f1f0-914f-9da3-c845f609a87d';

  private $key, $input, $entries;

  /** @return void */
  public function setUp() {
    $this->key= new Key('entries-in-root');
    $this->input= ClassLoader::getDefault()->getResourceAsStream('fixtures/entries-in-root.kdbx')->in();
    $this->entries= [
      self::ID_ONE => [
        'UUID'            => 'fZhlFzAGRU24qsKpoxQ2Lg==',
        'IconID'          => '0',
        'ForegroundColor' => null,
        'BackgroundColor' => null,
        'OverrideURL'     => null,
        'Tags'            => null,
        'Times'           => '',
        'String' => [
          'Notes'    => null,
          'Password' => new ProtectedValue("\323\$c", "\274J\006"),
          'Title'    => 'Entry #1',
          'URL'      => null,
          'UserName' => null,
        ],
        'History' => [
        ]
      ],
      self::ID_TWO => [
        'UUID'            => 'G73VLfHwkU+do8hF9gmofQ==',
        'IconID'          => '0',
        'ForegroundColor' => null,
        'BackgroundColor' => null,
        'OverrideURL'     => null,
        'Tags'            => null,
        'Times'           => '',
        'String' => [
          'Notes'    => null,
          'Password' => new ProtectedValue("P\2277", "\$\340X"),
          'Title'    => 'Entry #2',
          'URL'      => null,
          'UserName' => null,
        ],
        'History' => [
        ]
      ]
    ];
  }

  #[@test]
  public function entries_in_root() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals(
        [
          self::ID_ONE => new Entry($this->entries[self::ID_ONE]),
          self::ID_TWO => new Entry($this->entries[self::ID_TWO])
        ],
        iterator_to_array($db->group('/')->entries())
      );
    });
  }

  #[@test]
  public function all_passwords_in_test_group() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals(
        [
          'Entry #1' => $this->entries[self::ID_ONE]['String']['Password'],
          'Entry #2' => $this->entries[self::ID_TWO]['String']['Password']
        ],
        iterator_to_array($db->passwords('/'))
      );
    });
  }
}