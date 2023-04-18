<?php

namespace Pematon\Session\DI;

use Nette;
use Nette\DI\Definitions\ServiceDefinition;

class MysqlSessionHandlerExtension extends Nette\DI\CompilerExtension
{
    private array $defaults = [
        "tableName" => "session",
    ];

    public function loadConfiguration(): void
    {
        parent::loadConfiguration();

        $config = $this->getConfig() + $this->defaults;

        $builder = $this->getContainerBuilder();

        $definition = $builder->addDefinition($this->prefix("sessionHandler"))
            ->setType('Pematon\Session\MysqlSessionHandler')
            ->addSetup("setTableName", [$config["tableName"]]);

        /** @var ServiceDefinition $sessionDefinition */
        $sessionDefinition = $builder->getDefinition("session");

        $sessionSetup = $sessionDefinition->getSetup();
        # Prepend setHandler method to other possible setups (setExpiration) which would start session prematurely.
        array_unshift($sessionSetup, new Nette\DI\Definitions\Statement("setHandler", [$definition]));

        $sessionDefinition->setSetup($sessionSetup);
    }
}
