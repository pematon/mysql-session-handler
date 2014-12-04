# MySQL Session handler

Custom PHP session handler for [Nette Framework](http://nette.org/) that uses MySQL database for storage.

## Installation

Prefered way to install is by [Composer](http://getcomposer.org/):
The best way to install pematon/mysql-session-handler is by using [Composer](http://getcomposer.org/):

```sh
$ composer require pematon/mysql-session-handler:~1.0
```

## Setup

After installation register an extension in config.neon:

```neon
	extensions:
		sessionHandler: Pematon\Session\DI\MysqlSessionHandlerExtension
```
