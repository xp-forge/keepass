<?php namespace info\keepass;

use util\UUID;
use lang\ElementNotFoundException;

class Group extends Object {

  /** @return util.UUID */
  public function uuid() { return new UUID($this->decodeUUID($this->backing['UUID'])); }

  /** @return string */
  public function name() { return $this->backing['Name']; }

  /** @return string */
  public function notes() { return $this->backing['Notes']; }

  /**
   * Retrieve a password
   *
   * @param  string $title
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function password($title) {
    if (isset($this->backing['Entry'])) {
      foreach ($this->backing['Entry'] as $uuid => $entry) {
        if ($title === $entry['String']['Title']) return $entry['String']['Password'];
      }
    }
    throw new ElementNotFoundException('No such password `'.$title.'\'');
  }

  /**
   * Retrieve all passwords
   *
   * @return php.Generator
   * @throws lang.ElementNotFoundException
   */
  public function passwords() {
    if (isset($this->backing['Entry'])) {
      foreach ($this->backing['Entry'] as $uuid => $entry) {
        yield $entry['String']['Title'] => $entry['String']['Password'];
      }
    }
  }

  /**
   * Groups inside this group
   *
   * @return php.Generator
   */
  public function groups() {
    if (isset($this->backing['Group'])) {
      foreach ($this->backing['Group'] as $uuid => $group) {
        yield $this->decodeUUID($uuid) => new Group($group);
      }
    }
  }

  /**
   * Entries inside this group
   *
   * @return php.Generator
   */
  public function entries() {
    if (isset($this->backing['Entry'])) {
      foreach ($this->backing['Entry'] as $uuid => $entry) {
        yield $this->decodeUUID($uuid) => new Entry($entry);
      }
    }
  }
}