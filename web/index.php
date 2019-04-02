<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

//use FormulaParser\FormulaParser;
date_default_timezone_set(«UTC+3»);

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
			$user_id = $data->object->from_id;
			$peer_id = $data->object->peer_id;
			$user_resp = [
				'user_ids' => $user_id,
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.0'
			];
			$user_info = json_decode(file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($user_resp)));
			$user_name = $user_info->response[0]->first_name;
			$message = $data->object->text;
			$date = date("d.m.Y  H:i");
			$messages_array = [
				'Привет дуц' =>  "Привет, [id{$user_id}|{$user_name}] !",
				'Дуц, как дела?' => "Збс, ведь я не учусь.=)",
				'Дуц, что умеешь?' => "Кидать подгоны, чтобы Даня не агрился.=)",
				'Дуц, кто такой ходж?' => "Нормас чел, иногда сутулы, конечно, но ладно.=)",
				'Дуц, дата?' => "{$date}"
			];
			foreach ($messages_array as $k => $v) {
				if ($message == $k) {
					$otvet = $v;
				}
			}

			$request_params = [
				//'user_id' => $user_id,
				'random_id' => 0,
				'peer_id' => $peer_id,
				'message' => $otvet,
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.92'
			];
			

			// $formula = $data->object->body;
			// $precision = 2; // Number of digits after the decimal point
			// try {
			//     $parser = new FormulaParser($formula, $precision);
			//     $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
			//   	// if($result['1'] !== 'Syntax error'){$request_params['message'] = 'Ответ: '. $result['1'];};
			//   	if($result['1'] !== 'Invalid character'){$request_params['message'] = 'Ответ: '. $result['1'];};

			    
			// } catch (\Exception $e) {
			//      $request_params['message'] = 'Неа...';
			// }


			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

			return 'ok';
			break;
	}
	return "nioh";
});

$app->run();
