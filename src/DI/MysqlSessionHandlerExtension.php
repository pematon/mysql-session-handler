<?php

namespace Pematon\Session\DI;

use Nette;

class MysqlSessionHandlerExtension extends Nette\DI\CompilerExtension
{
	private $defaults = [
		'tableName' => 'sessions',
	];

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$config = $this->getConfig($this->defaults);

		$builder = $this->getContainerBuilder();

		$definition = $builder->addDefinition($this->prefix('sessionHandler'))
			->setClass('Pematon\Session\MysqlSessionHandler')
			->addSetup('setTableName', [$config['tableName']]);

		$builder->getDefinition('session')
			->addSetup('setHandler', array($definition));
	}
}
