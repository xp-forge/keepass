<?php namespace info\keepass;

class XmlStructure {
  private $randoms, $structure;

  /**
   * Creates a new KeePass database XML structure
   *
   * @param  info.keepass.Randoms $randoms
   */
  public function __construct(Randoms $randoms) {
    $this->randoms= $randoms;
  }

  /**
   * Parse XML
   *
   * @param  string $xml
   * @return void
   */
  public function parse($xml) {
    $parser= xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

    xml_set_object($parser, $this);
    xml_set_element_handler($parser, 'open', 'close');
    xml_set_character_data_handler($parser, 'text');

    $this->structure= [];
    xml_parse($parser, $xml, true);
    xml_parser_free($parser);
  }

  /** @return [:var] */
  public function root() { return $this->structure['Root']; }

  /** @return [:var] */
  public function meta() { return $this->structure['Meta']; }

  private function open($parser, $tag, $attributes) {
    array_unshift($this->structure, new Node($tag, isset($attributes['Protected'])));
  }

  private function close($parser, $tag) {
    $child= array_shift($this->structure);
    if ('Group' === $tag || 'Entry' === $tag) {
      $this->structure[0]->children[$tag][$child->children['UUID']]= $child->children;
    } else if ('Meta' === $tag || 'Root' === $tag || 'History' === $tag) {
      $this->structure[0]->children[$tag]= $child->children;
    } else if ('String' === $tag) {
      $this->structure[0]->children[$tag][$child->children['Key']]= $child->children['Value'];
    } else if ('KeePassFile' === $tag) {
      $this->structure= $child->children;
    } else {
      $this->structure[0]->children[$child->tag]= $child->value;
    }
  }

  private function text($parser, $text){
    if ($this->structure[0]->protected) {
      $value= base64_decode($text);
      $this->structure[0]->value= new ProtectedValue($value, $this->randoms->next(strlen($value)));
    } else {
      $this->structure[0]->value= trim($text);
    }
  }
}