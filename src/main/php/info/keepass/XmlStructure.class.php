<?php namespace info\keepass;

use lang\FormatException;

/**
 * KeePass database XML tree
 *
 * @test  xp://info.keepass.unittest.XmlStructureTest
 */
class XmlStructure {
  private $randoms, $tree;

  /**
   * Creates a new KeePass database XML tree
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

    $this->tree= [];
    if (!xml_parse($parser, $xml, true)) {
      $error= xml_get_error_code($parser);
      $line= xml_get_current_line_number($parser);
      xml_parser_free($parser);
      throw new FormatException('Error #'.$error.': '.xml_error_string($error).' at line '.$line);
    }

    xml_parser_free($parser);
  }

  /**
   * Returns root group
   *
   * @return [:var]
   */
  public function root() {
    $groups= $this->tree['Root']['Group'];
    return $groups[key($groups)];
  }

  /**
   * Returns meta information
   *
   * @return [:var]
   */
  public function meta() {
    return $this->tree['Meta'];
  }

  private function open($parser, $tag, $attributes) {
    array_unshift($this->tree, new Node($tag, isset($attributes['Protected'])));
  }

  private function close($parser, $tag) {
    $child= array_shift($this->tree);
    if ('Group' === $tag || 'Entry' === $tag) {
      $this->tree[0]->children[$tag][$child->children['UUID']]= $child->children;
    } else if ('Meta' === $tag || 'Root' === $tag || 'History' === $tag) {
      $this->tree[0]->children[$tag]= $child->children;
    } else if ('String' === $tag) {
      $this->tree[0]->children[$tag][$child->children['Key']]= $child->children['Value'];
    } else if ('KeePassFile' === $tag) {
      $this->tree= $child->children;
    } else {
      $this->tree[0]->children[$child->tag]= $child->value;
    }
  }

  private function text($parser, $text){
    if ($this->tree[0]->protected) {
      $value= base64_decode($text);
      $this->tree[0]->value= new ProtectedValue($value, $this->randoms->next(strlen($value)));
    } else {
      $this->tree[0]->value= trim($text);
    }
  }
}