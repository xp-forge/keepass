<?php namespace info\keepass;

interface Randoms {

  /**
   * Get next random bytes
   *
   * @param  int $n
   * @return string
   */
  public function next($n);

}
