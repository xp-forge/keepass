KeePass databases change log
============================

## ?.?.? / ????-??-??

## 2.1.0 / 2024-03-24

* Made compatible with XP 12 - @thekid

## 2.0.0 / 2024-02-04

* Implemented xp-framework/rfc#341: Drop XP <= 9 compatibility - @thekid
* Added PHP 8.4 to test matrix - @thekid
* Merged PR #3: Migrate to new testing library - @thekid

## 1.0.2 / 2021-10-21

* Made library compatible with XP 11 - @thekid

## 1.0.1 / 2020-04-05

* Implemented RFC #335: Remove deprecated key/value pair annotation syntax
  (@thekid)

## 1.0.0 / 2019-12-01

* Implemented feature request #2, also allowing `io.File` instances as
  parameter for `KeepassDatabase::open()`.
  (@thekid)
* Implemented xp-framework/rfc#334: Drop PHP 5.6. The minimum required
  PHP version is now 7.0.0!
  (@thekid)

## 0.6.1 / 2019-12-01

* Added compatibility with XP 10, see xp-framework/rfc#333 - @thekid

## 0.6.0 / 2017-10-14

* Added forward compatibility with PHP 7.2 - @thekid
* Added forward compatibility with XP 9.0.0 - @thekid

## 0.5.0 / 2016-08-28

* Added forward compatibility with XP 8.0.0 - @thekid
* **Heads up: Dropped PHP 5.5 support**. Minimum PHP version is now PHP 5.6.0
  (@thekid)

## 0.4.0 / 2016-07-16

* Added path accessor to groups and entries - @thekid

## 0.3.0 / 2016-07-16

* Fixed issue #1: HHVM XML parsing - @thekid

## 0.2.0 / 2016-07-14

* Added support for iterating over groups and entries, as well as
  selecting them by a path syntax
  (@thekid)
* Added support for `util.Secret` instances in `Key` constructor
  (@thekid)

## 0.1.0 / 2016-07-13

* Hello World! First release - @thekid