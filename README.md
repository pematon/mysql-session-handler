# MySQL Session handler

Custom PHP session handler for [Nette Framework](http://nette.org/) that uses MySQL database for storage.

## Requirements

- [nette/database](https://github.com/nette/database) 2.2+
- PHP 5.3+

## Installation

Preferred way to install pematon/mysql-session-handler is by using [Composer](http://getcomposer.org/):

```sh
$ composer require pematon/mysql-session-handler:~1.0
```

## Setup

After installation:

1) Create the table sessions using SQL in [sql/create.sql](sql/create.sql).

2) Register an extension in config.neon:

```neon
	extensions:
		sessionHandler: Pematon\Session\DI\MysqlSessionHandlerExtension
```

## Features

- For security reasons, Session ID is stored in the database as an MD5 hash.
- Multi-Master Replication friendly (tested in Master-Master row-based replication setup).
