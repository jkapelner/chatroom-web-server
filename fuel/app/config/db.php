<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(
	'default' => array(
		'connection'  => array(
			'dsn'        => 'mysql:host=127.0.0.1;dbname=database',
			'username'   => isset($_SERVER['MYSQL_USERNAME']) ? $_SERVER['MYSQL_USERNAME'] : 'username',
			'password'   => isset($_SERVER['MYSQL_PASSWORD']) ? $_SERVER['MYSQL_PASSWORD'] : 'password',
		),
	),
	'redis' => array(
		'default' => array(
			'hostname'  => 'localhost',
			'port'      => 6379,
			'timeout'	=> null,
		)
	),
    'nodejs' => array(
        'chat' => array(
            'host' => null, /* use default host */
            'port' => 0, /* use default port */
        ),
    ),
);
