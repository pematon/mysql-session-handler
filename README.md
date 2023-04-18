# MySQL Session handler

Custom PHP session handler for [Nette Framework](http://nette.org/) that uses MySQL database for storage.

## Requirements

- PHP 8.1+

## Installation

Preferred way to install pematon/mysql-session-handler is by using [Composer](http://getcomposer.org/):

```sh
$ composer require pematon/mysql-session-handler:~2.0
```

## Setup

After installation:

1) Create the table `session` using SQL in [sql/create.sql](sql/create.sql).

2) Register an extension in config.neon:

```neon
extensions:
    sessionHandler: Pematon\Session\DI\MysqlSessionHandlerExtension
```

3) Configure custom table name:

```neon
sessionHandler:
    tableName: session
```

## Features

- For security reasons, Session ID is stored in the database as an MD5 hash.
- Multi-Master Replication friendly (tested in Master-Master row-based replication setup).
