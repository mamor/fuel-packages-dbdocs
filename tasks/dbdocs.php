<?php

namespace Fuel\Tasks;

/**
 * Generate database documentation task
 *
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Dbdocs
{
	/**
	 * Database connection setting
	 * 
	 * @var array
	 */
	private static $config = array();

	/**
	 * Documentation directory
	 * 
	 * @var string
	 */
	private static $dir = null;

	/**
	 * Database connection setting for MySQL
	 * 
	 * @var array
	 */
	private static $config_mysql = array(
		'host'     => '',
		'dbname'   => '',
		'user'     => '',
		'password' => '',
		'charset'  => '',
		'driver'   => 'pdo_mysql',
		'description' => '',
	);

	/**
	 * Database connection setting for PostgreSQL
	 * 
	 * @var array
	 */
	private static $config_pgsql = array(
		'host'     => '',
		'dbname'   => '',
		'user'     => '',
		'password' => '',
		'charset'  => '',
		'driver'   => 'pdo_pgsql',
		'description' => '',
	);

	/**
	 * Database connection setting for SQLite
	 * 
	 * @var array
	 */
	private static $config_sqlite = array(
		'path'     => '',
		'charset'  => '',
		'driver'   => 'pdo_sqlite',
		'description' => '',
	);

	/**
	 * Show help
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs
	 */
	public static function run()
	{
		static::help();
	}

	/**
	 * Show help
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:help
	 */
	public static function help()
	{
		$output = <<<HELP

Description:
  Generate database documentation

Commands:
  php oil refine dbdocs:mysql  <directory>
  php oil refine dbdocs:pgsql  <directory>
  php oil refine dbdocs:sqlite <directory>
  php oil refine dbdocs:saveconfig
  php oil refine dbdocs:deleteconfig
  php oil refine dbdocs:showconfig
  php oil refine dbdocs:fromconfig <directory> [config name]
  php oil refine dbdocs:help

Runtime options:
  -f, [--force]           # Overwrite documentation that already exist
  -n, [--non-interactive] # Non interactive mode

Runtime options with non interactive mode:
MySQL and PostgreSQL:
  --host=<host>
  --dbname=<dbname>
  --user=<user>
  --password=<password>
  --charset=<charset>
  --description=<description>

SQLite:
  --path=<path>
  --charset=<charset>
  --description=<description>

HELP;
		\Cli::write($output);
	}

	/**
	 * Generate Database Documentation for MySQL
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:mysql
	 * 
	 * @param  $dir Documentation directory
	 */
	public static function mysql($dir = null)
	{
		static::$config = static::$config_mysql;

		if ($dir === null)
		{
			static::help();
			exit();
		}

		static::$dir = rtrim($dir, DS).DS.'dbdoc'.DS;

		static::process();
	}

	/**
	 * Generate Database Documentation for PostgreSQL
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:pgsql
	 * 
	 * @param  $dir Documentation directory
	 */
	public static function pgsql($dir = null)
	{
		static::$config = static::$config_pgsql;

		if ($dir === null)
		{
			static::help();
			exit();
		}

		static::$dir = rtrim($dir, DS).DS.'dbdoc'.DS;

		static::process();
	}

	/**
	 * Generate Database Documentation for SQLite
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:sqlite
	 * 
	 * @param  $dir Documentation directory
	 */
	public static function sqlite($dir = null)
	{
		static::$config = static::$config_sqlite;

		if ($dir === null)
		{
			static::help();
			exit();
		}

		static::$dir = rtrim($dir, DS).DS.'dbdoc'.DS;

		static::process();
	}

	/**
	 * Save config
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:saveconfig
	 */
	public static function saveconfig()
	{
		\Config::load('dbdocsconnections', true);
		$connections = \Config::get('dbdocsconnections', array());

		$name = trim(\Cli::prompt('Config name'));

		$options = array(
			1 => '1. MySQL',
			2 => '2. PostgreSQL',
			3 => '3. SQLite',
		);

		$platform = \Cli::prompt("Enter a number.\n".implode("\n", $options)."\n", array_flip($options));

		switch ($platform)
		{
			case 1: static::$config = static::$config_mysql; break;
			case 2: static::$config = static::$config_pgsql; break;
			case 3: static::$config = static::$config_sqlite; break;
		}
		static::prompt();
		static::confirm();

		$connections[$name] = static::$config;
		\Config::save(PKGPATH.'dbdocs/config/dbdocsconnections.php', $connections);

		\Cli::write("Saved configuration \"{$name}\"", 'green');
	}

	/**
	 * Delete config
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:deleteconfig
	 */
	public static function deleteconfig()
	{
		\Config::load('dbdocsconnections', true);
		$connections = \Config::get('dbdocsconnections', array());


		if (empty($connections))
		{
			\Cli::write('Configuration is empty.', 'red');
		}
		else
		{
			$name = trim(\Cli::prompt("Enter a name.\n", array_keys($connections)));
			unset($connections[$name]);
			\Config::save(PKGPATH.'dbdocs/config/dbdocsconnections.php', $connections);

			\Cli::write("Deleted configuration \"{$name}\"", 'green');
		}

	}

	/**
	 * Show config
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:showconfig
	 */
	public static function showconfig()
	{
		\Config::load('dbdocsconnections', true);
		$connections = \Config::get('dbdocsconnections', array());

		if (empty($connections))
		{
			\Cli::write('Configuration is empty.', 'red');
		}
		else
		{
			$name = trim(\Cli::prompt("Enter a name.\n", array_keys($connections)));
			\Cli::write(print_r($connections[$name], true), 'green');
		}

	}

	/**
	 * Generate documentation from config
	 *
	 * Usage (from command line):
	 * 
	 * php oil r dbdocs:fromconfig
	 * 
	 * @param  $dir Documentation directory
	 * @param  $name Name of config
	 */
	public static function fromconfig($dir = null, $name = null)
	{
		if ($dir === null)
		{
			static::help();
			exit();
		}

		\Config::load('dbdocsconnections', true);
		$connections = \Config::get('dbdocsconnections', array());

		if (empty($connections))
		{
			\Cli::write('Configuration is empty.', 'red');
		}
		else
		{
			if ($name === null)
			{
				$name = trim(\Cli::prompt("Enter a name.\n", array_keys($connections)));
			}
			else if ( ! isset($connections[$name]))
			{
				\Cli::write("Not Found configuration \"{$name}\"", 'red');
				exit();
			}

			\Cli::set_option('n', true);

			foreach ($connections[$name] as $k => $v)
			{
				if( ! empty($k) and ($k != 'driver'))
				{
					\Cli::set_option($k, $v);
				}

			}

			switch ($connections[$name]['driver'])
			{
				case 'pdo_mysql': static::mysql($dir); break;
				case 'pdo_pgsql': static::pgsql($dir); break;
				case 'pdo_sqlite': static::sqlite($dir); break;
			}
		}

	}

	/*******************************************************
	 * Private Methods
	 ******************************************************/
	/**
	 * Generate process
	 */
	private static function process()
	{
		/**
		 * interactive mode?
		 */
		if (\Cli::option('n', false) or \Cli::option('non-interactive', false))
		{
			/**
			 * option values into config
			 */
			foreach (static::$config as $k => &$v)
			{
				if (($k != 'driver'))
				{
					$v = \Cli::option($k);
				}
			}
		}
		else
		{
			/**
			 * enter values into config
			 */
			static::prompt();
			static::confirm();
		}

		/**
		 * generate documentation
		 */
		$dd = \Dbdocs::forge('default', static::$config);
		$ret = $dd->generate(static::$dir, (\Cli::option('f') or \Cli::option('force')));

		if ($ret === true)
		{
			\Cli::write('Generated MySQL Documentation in "'.static::$dir.'"', 'green');
		}
		else if (is_string($ret))
		{
			\Cli::write($ret, 'red');
		}
		else
		{
			\Cli::write('System error occurred.', 'red');
		}

	}

	/**
	 * Enter database connection setting
	 * 
	 * @param  $index Number of setting to correct
	 */
	private static function prompt($index = null)
	{
		if($index === null)
		{
			/**
			 * enter all values
			 */
			foreach (static::$config as $k => &$v)
			{
				if (($k != 'driver'))
				{
					do
					{
						$v = trim(\Cli::prompt($k));
					}
					while ( ! in_array($k, array('password', 'description')) and strlen($v) === 0);
				}
			}
		}
		else
		{
			/**
			 * enter selected value
			 */
			$i = 1;
			foreach (static::$config as $k => &$v)
			{
				if ($index == $i)
				{
					$v = \Cli::prompt($k);
					break;
				}

				($k != 'driver') and $i++;
			}
		}
	}

	/**
	 * Confirm database connection setting
	 */
	private static function confirm()
	{
		while(true)
		{
			$options = array();

			/**
			 * entered values
			 */
			$i = 1;
			foreach (static::$config as $k => &$v)
			{
				if (($k != 'driver'))
				{
					$display = ($k === 'password') ? str_pad('', strlen($v), '*') : $v;
					$options[$i] = $i.'. '.$k.':'.$display;
					$i++;
				}
			}

			$options['y'] = 'y. OK.';
			$options['c'] = 'c. Cancel.';

			\Cli::write("\nConfirm", 'green');
			\Cli::write(implode("\n", $options));

			$index = \Cli::prompt('Enter a number, "y" or "c".'."\n", array_flip($options));

			switch ($index)
			{
				case 'y':
					return;
				case 'c':
					\Cli::write('Good bye!!', 'green');
					exit();
				default:
					static::prompt($index);
					break;
			}
		}

	}

}

/* End of file tasks/dbdocs.php */
