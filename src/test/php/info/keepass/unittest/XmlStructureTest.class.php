<?php namespace info\keepass\unittest;

use info\keepass\{Header, Randoms, XmlStructure};
use lang\FormatException;
use test\{Assert, Expect, Before, Test, Values};

class XmlStructureTest {
  private $randoms, $xml;

  #[Before]
  public function randoms() {
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

  #[Test]
  public function can_create() {
    new XmlStructure($this->randoms);
  }

  #[Test]
  public function meta() {
    $structure= new XmlStructure($this->randoms);
    $structure->parse($this->xml);
    Assert::equals('Test', $structure->meta()['Generator']);
  }

  #[Test]
  public function root() {
    $structure= new XmlStructure($this->randoms);
    $structure->parse($this->xml);
    Assert::equals('Database Root', $structure->root()['Name']);
  }

  #[Test, Expect(FormatException::class), Values(['', '<KeePassFile>'])]
  public function parsing_fails_on_not_well_formed($input) {
    $structure= new XmlStructure($this->randoms);
    $structure->parse($input);
  }
}