# fuel-packages-dbdocs [![Build Status](https://travis-ci.org/mp-php/fuel-packages-dbdocs.png)](https://travis-ci.org/mp-php/fuel-packages-dbdocs)

* fuel-packages-dbdocs is generator for database documentation
* You can generate database documentation from command line
* Example(GitLab Database) http://fueldbdocssample.madroom.net/index.html
* Powered by FuelPHP http://fuelphp.com/

You can also use https://github.com/mp-php/fuel-dbdocs

---

## Supporting databases

* MySQL (pdo_mysql is required)
* PostgreSQL (pdo_pgsql is required)
* SQLite (pdo_sqlite is required)
* Microsoft SQLServer (pdo_sqlsrv is required)

## Install
### Setup to fuel/packages/dbdocs
* Use composer https://packagist.org/packages/mp-php/fuel-packages-dbdocs
* git submodule add
* Download zip

### Install vendors

	$ cd fuel/packages/dbdocs
	$ curl -s http://getcomposer.org/installer | php
	$ php composer.phar install

### Configuration

In app/config/config.php

	'always_load' => array('packages' => array(
		'dbdocs',
		...

	'security' => array('whitelisted_classes' => array(
		'Doctrine\\DBAL\\Schema\\Table',
		'Doctrine\\DBAL\\Schema\\View',
		...

## Usage

	$ php oil r dbdocs:help

## License

Copyright 2013, Mamoru Otsuka. Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
