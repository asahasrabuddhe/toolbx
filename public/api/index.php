<?php
require_once './vendor/autoload.php';
require_once './config.php';

$includesFiles = (array) glob('includes/*.php');

foreach( $includesFiles as $includesFile ) 
{
   require_once  $includesFile;
}

$classFiles = (array) glob('classes/*.php');

foreach( $classFiles as $classFile ) 
{
   require_once  $classFile;
}

// Register service provider with the container
$container = new \Slim\Container;
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

// Add Authentication Middleware
$auth = new Auth();
$container->register( $auth );

// Add middleware to the application
$app = new \Slim\App($container);
$app->add(new \Slim\HttpCache\Cache('public', 86400));

$app->get('/', function ($request, $response) {
	return $response->getBody()->write('Hello Nuvo v1');
	$db = new ezSQL_mysqli(USER, PASSWORD, DATABASE, HOST);

	$my_tables = $db->get_results("SHOW TABLES",ARRAY_N);
	// Print out last query and results..
	$db->debug();
	// Loop through each row of results..
	foreach ( $my_tables as $table )
	{
		// Get results of DESC table..
		$db->get_results("DESC $table[0]");
		// Print out last query and results..
		$db->debug();
	}
    return $response->getBody()->write('Hello Nuvo v1');
});

$routeFiles = (array) glob('routes/*.php');

foreach( $routeFiles as $routeFile ) 
{
   require_once  $routeFile;
}

$app->run();