# MySQL Session handler

Custom PHP session handler for [Nette Framework](http://nette.org/) that uses MySQL database for storage.

## Requirements

- [nette/database](https://github.com/nette/database) 2.2+
- PHP 5.3+

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

## Features

- For security reasons Session ID is stored as MD5 hash into database.
