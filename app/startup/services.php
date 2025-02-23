<?php
declare(strict_types=1);

use \DI\ContainerBuilder;

return (function () use (&$config): void {
    $diConf = [
        'config' => $config,
        'mysql' => DI\factory(function () use (&$config): \Libs\Db\Mysql\ConnectionsContainer {
            $container = new Libs\Db\Mysql\ConnectionsContainer();
            foreach ((array)$config['database']['mysql']['connections'] as $idx => $params) {
                $nodeConf = $params['nodes'][array_rand($params['nodes'])];
                $container->addConnection($idx, [
                    'dsn' => 'mysql:dbname=' . $params['dbname'] . ';host=' . $nodeConf['host'] . ';port=' . $nodeConf['port'] . ';charset=' . $params['charset'],
                    'username' => $params['username'],
                    'password' => $params['password'],
                ]);
            }
            return $container;
        }),
    ];

    $containerBuilder = new ContainerBuilder();
    $containerBuilder->useAutowiring(false);
    $containerBuilder->useAnnotations(false);
    $containerBuilder->addDefinitions($diConf);
    $container = $containerBuilder->build();

    \Libs\DI::init($container);
})();
