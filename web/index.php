<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

use FormulaParser\FormulaParser;

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
			$formula = $data->object->body;
			$precision = 2; // Number of digits after the decimal point


			$request_params = array(
				'user_id' => $data->object->user_id,
				'message' => 'Тест',
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.69'
			);
			
			try {
			    $parser = new FormulaParser($formula, $precision);
			    $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
			    $request_params['message'] = 'Ответ: '. $result['1'];
			    if($data->object->body == 'ходж'){$request_params['message'] = 'Собака сутулая.=)';};
			} catch (\Exception $e) {
			     $request_params['message'] = 'Неа...';
			}

			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

			return 'ok';
			break;
	}
	return "nioh";
});

$app->run();
