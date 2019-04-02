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
		return "miss data";

	if($data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation')
		return "wrong data";

	switch($data->type)
	{
		case 'confirmation':
			return getenv('VK_CONFIRMATION_CODE');
			break;

		case 'message_new':
			$user_id = $data->object->user_id;
			$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&v=5.69"));
			$user_name = $user_info->response[0]->first_name;
			$message = $data->object->body;
			$messages_array = [
				'Привет дуц' => "Привет {$user_name}!",
				'Дуц, как дела?' => "Збс, ведь я не учусь.=)",
				'Дуц, что умеешь?' => "Кидать подгоны, чтобы Даня не агрился.=)",
				'Дуц, кто такой ходж?' => "Нормас чел, иногда сутулы, конечно, но ладно.=)"
			];
			foreach ($messages_array as $k => $v) {
				if ($message == $k) {
					$otvet = $v;
				}
			}

			$request_params = [
				'user_id' => $user_id,
				'message' => $otvet,
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.69'
			];
			
			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

			return 'ok';
			break;
	}
	return "nioh";
});

$app->run();
