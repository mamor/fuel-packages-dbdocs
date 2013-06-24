<?php

namespace Dbdocs;

use \Doctrine\DBAL\Platforms\AbstractPlatform;

class Types_EnumType extends \Doctrine\DBAL\Types\Type
{

	public function getName()
	{
		return 'enum';
	}

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		//TODO
	}
}
