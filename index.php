<?php
use Slim\Http\Response;

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->add(new \Slim\Middleware\ContentTypes());

$persistence = new \Persistence\Persistence();

$app->post(
	'/l8s', 
	function() use ($app, $persistence) 
	{
		$token = $persistence->createL8();
		$app->response()->status(201);
		$app->response()['Location'] = '/l8s/'.$token; // Location: <collection>/<id>
		echo json_encode(array('id' => $token)); // 201 Created
	}
);

$app->get(
	'/l8s/:token',
	function($token) use ($app, $persistence)
	{
		$l8 = $persistence->getL8($token);
		$app->render('simulat8r.php', array('token' => $token, 'l8' => $l8));
	}
);

$app->put(
		'/l8s/:token',
		function($token) use ($app, $persistence)
		{
			$request = $app->request()->getBody();
			foreach ($request as $key => $value) {
				$persistence->updateL8($token, $key, $value);
			}
			echo json_encode(array());
		}
);

$app->get(
	'/l8s/:token/led/:led',
	function($token, $led) use ($app, $persistence)
	{
		echo json_encode($persistence->readLED($token, $led));
	}
);

$app->get(
	'/l8s/:token/led',
	function($token) use ($app, $persistence)
	{
		echo json_encode($persistence->readLEDs($token));
	}
);

$app->get(
		'/l8s/:token/superled',
		function($token) use ($app, $persistence)
		{
			echo json_encode($persistence->readSuperLED($token));
		}
);

$app->put(
		'/l8s/:token/superled',
		function($token) use ($app, $persistence)
		{
			$request = $app->request()->getBody();
			foreach ($request as $key => $value) {
				if ($key == 'superled') {
					$persistence->updateSuperLED($token, $value);
				}
			}
			echo json_encode(array());
		}
);

$app->get(
	'/l8s/:token/sensor/:sensor',
	function($token, $sensor) use ($app, $persistence)
	{
		if ($sensor == 'acceleration') {
			$result = $persistence->readAcceleration($token);
			echo json_encode($result);
		}
		if ($sensor == 'temperature') {
			$result = strval($persistence->readL8($token, $sensor.'_sensor_data'));
			echo json_encode(array($sensor.'_celsius_data' => $result, $sensor.'_fahrenheit_data' => $result));						
		}
		if ($sensor == 'proximity' || $sensor == 'noise' || $sensor == 'ambientlight') {
			$result = strval($persistence->readL8($token, $sensor.'_sensor_data'));
			echo json_encode(array($sensor.'_data' => $result));
		}  
	}
);

$app->get(
	'/l8s/:token/sensor/:sensor/enabled',
	function($token, $sensor) use ($app, $persistence)
	{
		$result = strval($persistence->readL8($token, $sensor.'_sensor_enabled'));
		echo json_encode(array($sensor.'_enabled' => $result));
	}
);

$app->get(
	'/l8s/:token/bluetooth_enabled',
	function($token) use ($app, $persistence)
	{
		$result = strval($persistence->readL8($token, 'bluetooth_enabled'));
		echo json_encode(array('bluetooth_enabled' => $result));
	}
);

$app->get(
		'/l8s/:token/battery_status',
		function($token) use ($app, $persistence)
		{
			$result = strval($persistence->readL8($token, 'battery_status'));
			echo json_encode(array('battery_status' => $result));
		}
);

$app->get(
		'/l8s/:token/button',
		function($token) use ($app, $persistence)
		{
			$result = strval($persistence->readL8($token, 'button'));
			echo json_encode(array('button' => $result));
		}
);

$app->get(
		'/l8s/:token/memory_size',
		function($token) use ($app, $persistence)
		{
			$result = strval($persistence->readL8($token, 'memory_size'));
			echo json_encode(array('memory_size' => $result));
		}
);

$app->get(
		'/l8s/:token/free_memory',
		function($token) use ($app, $persistence)
		{
			$result = strval($persistence->readL8($token, 'free_memory'));
			echo json_encode(array('free_memory' => $result));
		}
);

$app->get(
		'/l8s/:token/version',
		function($token) use ($app, $persistence)
		{
			$result = $persistence->readVersions($token);
			echo json_encode($result);
		}
);

$app->run();