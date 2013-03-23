* fuel-packages-dbdocs is generator for database documentation
* You can generate database documentation from command line
* Example(GitLab Database) http://fueldbdocssample.madroom.net/index.html
* Powered by FuelPHP http://fuelphp.com/

You can also use https://github.com/mp-php/fuel-dbdocs

---

## Install

### Getting code

	# In FuelPHP project root
	$ git submodule add git://github.com/mp-php/fuel-packages-dbdocs.git fuel/packages/dbdocs

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
