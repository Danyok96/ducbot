<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

$app->get('/', function() use($app) {
	return "Hello World!";
});


$app->post('/bot', function() use($app) {
	$data = json_decode(file_get_contents('php://input'));

	if(!$data)
		return "nioh";

	if($data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation')
		return "nioh";

	switch($data->type)
	{
		case 'confirmation':
			return getenv('VK_CONFIRMATION_CODE');
			break;

		case 'message_new':

			$request_params= array(
				'user_id' => $data->object->peer_id,
				'message' => '����',
				'access_tocken' => getenv('VK_TOKEN'),
				'v' => '5.92'
			);

			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

			return 'ok';
			break;
	}
	return "nioh";
});

$app->run();
