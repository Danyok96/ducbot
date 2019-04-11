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
			$day = date("D");
			$time = date("H:i");
			$test = strtotime($date);
			$numofweek = date("W",$test);
			$numofstudweek = $numofweek-5;
			$next_date = strtotime("+1 day");
			$nextdate = date("d.m.Y", $next_date);
			$nextday = date("D", $next_date);
			$testnext = strtotime($nextdate);
			$numofnextweek = date("W", $testnext);
			$numofnextstudweek = $numofnextweek-5;
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
					$otvet = "[id{$user_id}|{$user_name}-дуц], {$date}.\nНеделя: {$numofweek}.\nУчебная неделя: {$numofstudweek}.\nЗавтра: {$nextdate}.";
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
					$otvet = "[id{$user_id}|{$user_name}-дуц],\nУГИФС:\n15 апреля(первая подгруппа)\n23 апреля(вторая подгруппа)\n21 мая - защита(вторая подгруппа)\n27 мая - защита(первая подгруппа)";
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
				// case 'дуцрасписаниенасегодня':
				// 		if(($numofstudweek % 2) == 0)
				// 		{
    // 							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
				// 			switch ($day) {
				// 				case 'Sun':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";
				// 					break;
				// 				case 'Mon':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня только военка первой парой.";
				// 					break;
				// 				case 'Tue':
				// 					if($date == '09.04.2019')
				// 					{
				// 						$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня лабы по квантам.";
				// 					}
				// 					else
				// 					{
				// 						$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";
				// 					}
				// 					break;
				// 				case 'Wed':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n13:50-15:25 Радиоавтоматика(сем) 526\n15:40-17:15 ЗИС(лек) 417ю\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
				// 					break;
				// 				case 'Thu':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ЦОС(сем) 502ю";
				// 					break;
				// 				case 'Fri':
				// 				if ($date == '12.04.2019') 
				// 				{
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 (изм)ЗИС(лек) 417ю";
				// 				} 
				// 				else 
				// 				{
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
				// 				}
				// 					break;
				// 				case 'Sat':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л";
				// 					break;
				// 			}
				// 		}
				// 		else
				// 		{
    // 							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель.";
				// 			// switch ($day) {
				// 			// 	case 'Sun':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";
				// 			// 		break;
				// 			// 	case 'Mon':
				// 			// 	if ($date == '15.04.2019') {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня лабы в п.Орево (первая подгруппа) и военка.=)";
				// 			// 	} else {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня только военка первой парой.";
				// 			// 	}
				// 			// 		break;
				// 			// 	case 'Tue':
				// 			// 	if ($date == '23.04.2019') {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня лабы в п.Орево (вторая подгруппа) и военка.=)";
				// 			// 	} else {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";								
				// 			// 	}
				// 			// 		break;
				// 			// 	case 'Wed':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
				// 			// 		break;
				// 			// 	case 'Thu':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ОКЭ(сем) 526\n17:25-19:00 УГиФС(сем) 502ю";
				// 			// 		break;
				// 			// 	case 'Fri':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
				// 			// 		break;
				// 			// 	case 'Sat':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л\n12:00-13:35 ЧМ(сем) 526";
				// 			// 		break;
				// 			}
				// 		}
				// 	break;
				// case 'дуцрасписаниеназавтра':
				// 		if(($numofnextstudweek % 2) == 0)
				// 		{
    // 							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
				// 			switch ($nextday){
				// 				case 'Sun':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";
				// 					break;
				// 				case 'Mon':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня только военка первой парой.";
				// 					break;
				// 				case 'Tue':
				// 					if($nextday == '09.04.2019')
				// 					{
				// 						$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня лабы по квантам.";
				// 					}
				// 					else
				// 					{
				// 						$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";
				// 					}
				// 					break;
				// 				case 'Wed':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n13:50-15:25 Радиоавтоматика(сем) 526\n15:40-17:15 ЗИС(лек) 417ю\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
				// 					break;
				// 				case 'Thu':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ЦОС(сем) 502ю";
				// 					break;
				// 				case 'Fri':
				// 				if ($nextdate == '12.04.2019') 
				// 				{
									
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 (изм)ЗИС(лек) 417ю";
				// 				} 
				// 				else 
				// 				{
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
				// 				}
				// 					break;
				// 				case 'Sat':
				// 					$otvet = "[id{$user_id}|{$user_name}-дуц],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л";
				// 					break;
				// 				}
				// 		}
				// 		else
				// 		{
    // 							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель.";
				// 			// switch ($nextday) {
				// 			// 	case 'Sun':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";
				// 			// 		break;
				// 			// 	case 'Mon':
				// 			// 	if ($nextdate == '15.04.2019') {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня лабы в п.Орево (первая подгруппа) и военка.=)";
				// 			// 	} else {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня только военка первой парой.";
				// 			// 	}
				// 			// 		break;
				// 			// 	case 'Tue':
				// 			// 	if ($nextday == '23.04.2019') {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], сегодня лабы в п.Орево (вторая подгруппа) и военка.=)";
				// 			// 	} else {
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц], чиль, сегодня выходной. =)";								
				// 			// 	}
				// 			// 		break;
				// 			// 	case 'Wed':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
				// 			// 		break;
				// 			// 	case 'Thu':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ОКЭ(сем) 526\n17:25-19:00 УГиФС(сем) 502ю";
				// 			// 		break;
				// 			// 	case 'Fri':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
				// 			// 		break;
				// 			// 	case 'Sat':
				// 			// 		$otvet = "[id{$user_id}|{$user_name}-дуц],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л\n12:00-13:35 ЧМ(сем) 526";
				// 			// 		break;
				// 		}		
				// 	break;	
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
