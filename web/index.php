<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

//use FormulaParser\FormulaParser;

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
			$message = mb_strtolower($message);
			//$message = preg_replace("|[^\d\w ]+|i", "", $message);
			$date = date("d.m.Y  H:i");
			$messages_array = [
				'привет дуц' =>  "Привет, [id{$user_id}|{$user_name}-дуц] !",
				'дуц, как дела?' => "[id{$user_id}|{$user_name}-дуц], збс, ведь я не учусь.=)",
				'дуц, что умеешь?' => "[id{$user_id}|{$user_name}-дуц], кидать подгоны, чтобы Даня не агрился.=)",
				'дуц, кто такой Ходж?' => "[id{$user_id}|{$user_name}-дуц], нормас чел, иногда сутулый, конечно, но ладно.=)",
				'дуц, дата?' => "[id{$user_id}|{$user_name}-дуц], {$date} +3 часа сверху.=)\nНеделя: ".date("W",$date),
				'дуц, кто такой фил?' => "[id{$user_id}|{$user_name}-дуц], челик, который проебался с арендой тачки.=)",
				'дуц, кто такой макс?' => "[id{$user_id}|{$user_name}-дуц], заебис чел, битки там, хуё-моё, [id34317520|Krzhprd.] в общем.=)",
				'дуц, кто такой даня?' => "[id{$user_id}|{$user_name}-дуц], тупо [id20017026|создатель].",
				'дуц, споки ноки' => "[id{$user_id}|{$user_name}-дуц], бархатной ночи, вы закроете глаза, а моя душа будет сидеть у ваших ног и охранять сны до рассвета, нежно улыбаясь вам. ^_^",
				'дуц, когда лабы?' => "[id{$user_id}|{$user_name}-дуц],\nКванты:\n9 апреля\n13-15: Мураш, Фил, Кузьмич, Комлева, Кузин\n15:40 - 19: все остальные\nУГИФС:\n15 апреля(первая подгруппа)\n23 апреля(вторая подгруппа)\n21 мая - защита(вторая подгруппа)\n27 мая - защита(первая подгруппа)",
				'хей дуц' =>  "Чё кого, [id{$user_id}|{$user_name}-дуц]?",
				'дуц, заебис. =)' =>  "Это по-кайфу, [id{$user_id}|{$user_name}-дуц].=)"
				//'Дуц, читы' =>"{$messages_array[0]}"

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
