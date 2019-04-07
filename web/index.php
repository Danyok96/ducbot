<?php
define('TIMEZONE', 'Europe/Moscow');
date_default_timezone_set(TIMEZONE);
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
			$dots = array(".",",","?"," ");
			$message = str_replace($dots, "", $message);
			$date = date("d.m.Y");
			$time = date("H:i");
			$test = strtotime($date);
			$numofweek = date("W",$test);
			$numofstudweek = $numofweek-5;
			switch ($message) {
				case 'приветдуц':
					$otvet = "Привет, [id{$user_id}|{$user_name}-дуц] !";
					break;
				case 'дуцкакдела':
					$otvet = "[id{$user_id}|{$user_name}-дуц], збс, ведь я не учусь.=)";
					break;
				case 'дуцчтоумеешь':
					$otvet = "[id{$user_id}|{$user_name}-дуц], кидать подгоны, чтобы Даня не агрился.=)";
					break;
				case 'дуцктотакойходж':
					$otvet = "[id{$user_id}|{$user_name}-дуц], нормас чел, иногда сутулый, конечно, но ладно.=)";
					break;
				case 'дуцдата':
					$otvet = "[id{$user_id}|{$user_name}-дуц], {$date}.\nНеделя: {$numofweek}.\nУчебная неделя: {$numofstudweek}.";
					break;
				case 'дуцвремя':
					$otvet = "[id{$user_id}|{$user_name}-дуц], {$time}.";
					break;
				case 'дуцктотакойфил':
					$otvet = "[id{$user_id}|{$user_name}-дуц], челик, который проебался с арендой тачки.=)";
					break;
				case 'дуцктотакоймакс':
					$otvet = "[id{$user_id}|{$user_name}-дуц], заебис чел, битки там, хуё-моё, [id34317520|Krzhprd.] в общем.=)";
					break;
				case 'дуцктотакойданя':
					$otvet = "[id{$user_id}|{$user_name}-дуц], тупо [id20017026|создатель].";
					break;
				case 'дуцктотакаяирина':
					$otvet = "[id{$user_id}|{$user_name}-дуц], 'чудесная и хорошая'(с) [id201182825|Иринус]. =)";
					break;
				case 'дуцктотакойпетёёёк':
					$otvet = "[id{$user_id}|{$user_name}-дуц], ну просто максимальный [id14806435|Дуц-танкист], который ещё и братков из группы кинул и перешёл в другую группу...";
					break;
				case 'дуцктотакойфирст':
					$otvet = "[id{$user_id}|{$user_name}-дуц], чики-бирики и в [id117803113|чела на BMW]. =)";
					break;
				case 'дуцктотакойгвоздь':
					$otvet = "[id{$user_id}|{$user_name}-дуц], просто [id174929520|Doge with the gun]. =)";
					break;
				case 'дуцспокиноки':
					$otvet = "[id{$user_id}|{$user_name}-дуц], бархатной ночи, вы закроете глаза, а моя душа будет сидеть у ваших ног и охранять сны до рассвета, нежно улыбаясь вам. ^_^";
					break;
				case 'дуцкогдалабы':
					$otvet = "[id{$user_id}|{$user_name}-дуц],\nКванты:\n9 апреля\n13-15: Мураш, Фил, Кузьмич, Комлева, Кузин\n15:40 - 19: все остальные\nУГИФС:\n15 апреля(первая подгруппа)\n23 апреля(вторая подгруппа)\n21 мая - защита(вторая подгруппа)\n27 мая - защита(первая подгруппа)";
					break;
				case 'хейдуц':
					$otvet = "Чё кого, [id{$user_id}|{$user_name}-дуц]?";
					break;
				case 'дуцзаебис':
					$otvet = "Это по-кайфу, [id{$user_id}|{$user_name}-дуц].=)";
					break;	
				case 'бунд':
					$otvet = "[id{$user_id}|{$user_name}-дуц], так, блэт. Успокаеваемся!=)";
					break;
				case 'дуцрасписание':
						if(($numofstudweek % 2) == 0){
    							$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
						}else{
    							$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель."+"asdas";
							}
					break;	
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
