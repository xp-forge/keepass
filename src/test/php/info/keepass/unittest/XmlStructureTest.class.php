<?php namespace info\keepass\unittest;

use info\keepass\XmlStructure;
use info\keepass\Header;
use info\keepass\Randoms;
use lang\FormatException;

class XmlStructureTest extends \unittest\TestCase {
  private $randoms, $xml;

  /** @return void */
  public function setUp() {
    $this->randoms= newinstance(Randoms::class, [], [
      'next' => function($n) { return str_repeat('*', $n); }
    ]);
    $this->xml= '
      <KeePassFile>
        <Meta>
          <Generator>Test</Generator>
        </Meta>
        <Root>
          <Group>
            <UUID>kvQkdO2a2EKOK+fUzdANTw==</UUID>
            <Name>Database Root</Name>
          </Group>
        </Root>
      </KeePassFile>
    ';
  }

  #[@test]
  public function can_create() {
    new XmlStructure($this->randoms);
  }

  #[@test]
  public function meta() {
    $structure= new XmlStructure($this->randoms);
    $structure->parse($this->xml);
    $this->assertEquals('Test', $structure->meta()['Generator']);
  }

  #[@test]
  public function root() {
    $structure= new XmlStructure($this->randoms);
    $structure->parse($this->xml);
    $this->assertEquals('Database Root', $structure->root()['Name']);
  }

  #[@test, @expect(FormatException::class), @values(['', '<KeePassFile>'])]
  public function parsing_fails_on_not_well_formed($input) {
    $structure= new XmlStructure($this->randoms);
    $structure->parse($input);
  }
}