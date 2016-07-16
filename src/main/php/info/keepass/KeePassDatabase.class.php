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

      $this->structure= new XmlStructure($this->header->randoms());
      $this->structure->parse($xml);
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
    $structure= $this->structure()->root();

    if ('/' !== $path) {
      foreach (explode('/', trim($path, '/')) as $segment) {
        if (null === ($located= $this->locate($structure['Group'], $segment))) {
          throw new ElementNotFoundException('No such group `'.$segment.'\' in '.$structure['Name']);
        }
        $structure= $located;
      }
    }

    return new Group($structure, $path);
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
   * @param  string $path Pass "/" to select database root
   * @return [:var]
   * @throws lang.ElementNotFoundException
   */
  public function passwords($path= '/') {
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