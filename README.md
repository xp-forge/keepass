KeePass databases
=================

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/keepass.svg)](http://travis-ci.org/xp-forge/keepass)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/keepass/version.png)](https://packagist.org/packages/xp-forge/keepass)

Read access to KeePass database files.

Example
-------

```php
use info\keepass\{KeePassDatabase, Key};
use io\streams\FileInputStream;
use util\cmd\Console;

$db= KeePassDatabase::open(new FileInputStream('database.kdbx'), new Key('passphrase'));
Console::writeLine($db);

// Show top-level groups and password entries inside these
foreach ($db->groups() as $group) {
  foreach ($group->passwords() as $name => $password) {
    Console::writeLine($name, ': ', $password);
  }
}
$db->close();
```

See also
--------
http://keepass.info/