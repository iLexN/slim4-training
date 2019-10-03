<?php
declare(strict_types=1);

use Illuminate\Database\Capsule\Manager;


//DB_CONNECTION=mysql
//DB_HOST=127.0.0.1
//DB_PORT=3306
//DB_DATABASE=laravel-rss
//DB_USERNAME=root
$db = [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'port'      => '3306',
    'database'  => 'laravel-rss',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    //'collation' => 'utf8_general_ci',
    //'prefix'    => '',
];

$capsule = new Manager();
$capsule->addConnection($db);
$capsule->setAsGlobal();
$capsule->bootEloquent();
//if ($dbSetting['logging']) {

//}
