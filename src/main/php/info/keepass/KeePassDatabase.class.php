<?php namespace info\keepass;

use lang\FormatException;
use lang\IndexOutOfBoundsException;
use lang\ElementNotFoundException;

class KeePassDatabase extends \lang\Object implements \lang\Closeable {
  private $version, $header, $blocks;
  private $structure= null;
  private $randoms= null;

  /**
   * Opens a KeePass database file
   *
   * @param  io.streams.InputStream $input
   * @param  info.keepass.Key $key
   * @return self
   * @throws lang.FormatException
   * @throws info.keepass.CannotDecrypt
   */
  public static function open($input, Key $key) {
    $self= new self();

    with (new Reader($input, 'sha256'), function($reader) use($self, $key) {
      $self->version= $reader->version();
      $self->header= $reader->header();

      $cipher= new Cipher($self->header->algorithm(), $key->transform($self->header), $self->header->encryptionIV);
      $decrypted= $cipher->decrypt($reader->remaining());
      $start= strlen($self->header->startBytes);
      if (0 !== strncmp($decrypted, $self->header->startBytes, $start)) {
        throw new CannotDecrypt('Incorrect passphrase?');
      }

      $self->blocks= new Blocks($decrypted, $start);
    });

    return $self;
  }

  /** @return info.keepass.Header */
  public function header() { return $this->header; }

  /**
   * Returns KeePass database structure as XML
   *
   * @return string
   */
  public function structure() {
    if (null === $this->structure) {
      if ($this->header->isCompressed()) {
        $xml= (new Gzipped($this->blocks->all()))->decompress();
      } else {
        $xml= $this->blocks->all();
      }

      $randoms= $this->header->randoms();
      $this->structure= [];

      $parser= xml_parser_create();
      xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
      xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
      xml_set_element_handler(
        $parser,
        function($parser, $tag, $attributes) {
          array_unshift($this->structure, new Node($tag, isset($attributes['Protected'])));
        },
        function($parser, $tag) {
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
      );
      xml_set_character_data_handler(
        $parser,
        function($parser, $text) use($randoms) {
          if ($this->structure[0]->protected) {
            $value= base64_decode($text);
            $this->structure[0]->value= new ProtectedValue($value, $randoms->next(strlen($value)));
          } else {
            $this->structure[0]->value= trim($text);
          }
        }
      );
      xml_parse($parser, $xml, true);
      xml_parser_free($parser);
    }

    return $this->structure;
  }

  private function locate($groups, $name) {
    foreach ($groups as $uuid => $group) {
      if ($name === $group['Name']) return $group;
    }
    return null;
  }

  /**
   * Get a group
   *
   * @param  string $path Pass "/" to select database root
   * @return info.keepass.Group
   * @throws lang.ElementNotFoundException if the group does not exist
   */
  public function group($path) {
    $root= $this->structure()['Root']['Group'];
    $structure= $root[key($root)];

    if ('/' !== $path) {
      foreach (explode('/', trim($path, '/')) as $segment) {
        if (null === ($located= $this->locate($structure['Group'], $segment))) {
          throw new ElementNotFoundException('No such group `'.$segment.'\' in '.$structure['Name']);
        }
        $structure= $located;
      }
    }

    return new Group($structure);
  }

  /**
   * Groups inside the given path
   *
   * @param  string $path Pass "/" to select database root
   * @return php.Generator
   */
  public function groups($path= '/') {
    return $this->group($path)->groups();
  }

  /**
   * Retrieve a password
   *
   * @param  string $path
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function password($path) {
    $p= strrpos($path, '/');
    return $this->group(substr($path, 0, $p))->password(substr($path, $p + 1));
  }

  /**
   * Retrieve all passwords in a given group
   *
   * @param  string $path
   * @return [:var]
   * @throws lang.ElementNotFoundException
   */
  public function passwords($path) {
    return $this->group($path)->passwords();
  }

  /** @return void */
  public function close() {
    $this->structure= null;
  }

  /** @return string */
  public function toString() {
    return sprintf(
      "%s(version= %s)@{\n  %s\n  %s\n}",
      nameof($this),
      $this->version,
      str_replace("\n", "\n  ", $this->header->toString()),
      $this->blocks->toString()
    );
  }
}