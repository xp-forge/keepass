<?php namespace info\keepass;

use util\UUID;
use lang\ElementNotFoundException;

/**
 * A password entry 
 *
 * @test  xp://info.keepass.unittest.EntryTest
 */
class Entry extends Object {

  /** @return util.UUID */
  public function uuid() { return new UUID($this->decodeUUID($this->backing['UUID'])); }

  /** @return string */
  public function title() { return $this->backing['String']['Title']; }

  /** @return string */
  public function notes() { return $this->backing['String']['Notes']; }

  /** @return string */
  public function url() { return $this->backing['String']['URL']; }

  /** @return var */
  public function username() { return $this->backing['String']['UserName']; }

  /** @return var */
  public function password() { return $this->backing['String']['Password']; }

}