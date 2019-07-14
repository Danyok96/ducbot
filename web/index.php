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
				'fields' => 'sex',
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
			$sex = $user_info->response[0]->sex;
			switch ($sex) {
				case '1':
					$pref = '-тян';
					break;
				case '2':
					$pref = '-кун';
					break;
				case '0':
					$pref = '-чмо';
					break;
			}
			//-----
			if ($user_id == 20017026) { $user_name = 'Создатель';$pref = '';}
			if ($user_id == 201182825) { $user_name = 'Ирусик';$pref = '-тян';}
			if ($user_id == 134572907) { $user_name = "Agent Kuz'mich";$pref = '';}
			switch ($message) {
				case 'приветдуц':
					$otvet = "Привет, [id{$user_id}|{$user_name}{$pref}]!";
					break;
				case 'дуцкакдела':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], збс, ведь я не учусь.=)";
					break;
				case 'дуцчиилинечи':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], Чи,да!";
					break;
				case 'дуцчтоумеешь':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], кидать подгоны, чтобы Даня не агрился.=)";
					break;
				case 'дуцктотакойходж':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], нормас чел, иногда сутулый, конечно, но ладно.=)";
					break;
				case 'дуцдата':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], {$date}.\nНеделя: {$numofweek}.\nУчебная неделя: {$numofstudweek}.\nЗавтра: {$nextdate}.";
					break;
				case 'дуцвремя':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], {$time}.";
					break;
				case 'дуцктотакойфил':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], челик, который проебался с арендой тачки.=)";
					break;
				case 'дуцктотакоймакс':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], заебис чел, битки там, хуё-моё, [id34317520|Krzhprd.] в общем.=)";
					break;
				case 'дуцктотакойданя':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], тупо [id20017026|создатель DUC'a].";
					break;
				case 'дуцктотакаяирина':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], 'чудесная и хорошая'(с) [id201182825|Иринус]. =)";
					break;
				case 'дуцктотакойпетёёёк':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], ну просто максимальный [id14806435|Дуц-танкист], который ещё и братков из группы кинул и перешёл в другую группу...";
					break;
				case 'дуцктотакойфирст':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], чики-бирики и в [id117803113|чела на BMW]. =)";
					break;
				case 'дуцктотакойгвоздь':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], просто [id174929520|Doge with the gun]. =)";
					break;
				case 'дуцспокиноки':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], бархатной ночи, вы закроете глаза, а моя душа будет сидеть у ваших ног и охранять сны до рассвета, нежно улыбаясь вам. ^_^";
					break;
				case 'дуцкогдалабы':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\nУГИФС:\n21 мая - защита(вторая подгруппа)\n27 мая - защита(первая подгруппа)\n";
					$media = "photo-180470421_456239020";
					break;
				case 'хейдуц':
					$otvet = "Чё кого, [id{$user_id}|{$user_name}{$pref}]?";
					break;
				case 'дуцзаебис':
					$otvet = "Это по-кайфу, [id{$user_id}|{$user_name}{$pref}].=)";
					break;	
				case 'бунд':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], так, блэт. Успокаеваемся!=)";
					break;
				case 'дуцоблако':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], \nhttps://cloud.mail.ru/public/NAzt/FJpjdhFpZ";
					break;
				case 'дуцоблакопомикропроцессорам':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], \nКоновальцев Антон Вячеславович\nНужно написать 2 и 3 РК.\nПрочитать ReadMe и excel-файл.\nhttps://cloud.mail.ru/public/937498eb7bb7/%D0%9C%D0%B8%D0%BA%D1%80%D0%BE%D0%BF%D1%80%D0%BE%D1%86%D0%B5%D1%81%D1%81%D0%BE%D1%80%D0%BD%D1%8B%D0%B5%20%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D1%8B/";
					break;
				case 'дуцрасписаниеэкзаменов':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], \n✅13 июня 9:00 ОКЭ 1039л(ебал его рот)\n✅(конс.)17 июня 12:00 РА Уч.совет\n✅18 июня 9:00 РА 526\n✅(конс.)21 июня 15:00 ЦОС 1039л\n✅22 июня 14:00 ЦОС 1039л\n✅(конс.)25 июня 14:00 УГиФС 526\n✅26 июня 9:00 УГиФС 526\n✅Сессия сдана!✅";
					break;
				case 'дуцсписоккоманд':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], \nдуц когда лабы\nдуц облако\nдуц облако Сереги\nдуц облако по микропроцессорам\nдуц расписание экзаменов\nдуц расписание на сегодня\nдуц расписание на завтра\nОстальные команды пасхальные.=)";
					break;	
				case 'дуцрасписаниенасегодня1':
						if(($numofstudweek % 2) == 0)
						{
    							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
							switch ($day) {
								case 'Sun':
									$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, сегодня выходной. =)";
									break;
								case 'Mon':
									$otvet = "[id{$user_id}|{$user_name}{$pref}], сегодня только военка первой парой.";
									break;
								case 'Tue':
									if($date == '09.04.2019')
									{
										$otvet = "[id{$user_id}|{$user_name}{$pref}], сегодня лабы по квантам.";
									}
									else
									{
										$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, сегодня выходной. =)";
									}
									break;
								case 'Wed':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n13:50-15:25 Радиоавтоматика(сем) 526\n15:40-17:15 ЗИС(лек) 417ю\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
									break;
								case 'Thu':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ЦОС(сем) 502ю";
									break;
								case 'Fri':
								if ($date == '12.04.2019') 
								{
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 (изм)ЗИС(лек) 417ю";
								} 
								else 
								{
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
								}
									break;
								case 'Sat':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л";
									break;
							}
						}
						else
						{
    							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель.";
							switch ($day) {
								case 'Sun':
									$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, сегодня выходной. =)";
									break;
								case 'Mon':
								if ($date == '15.04.2019') {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], сегодня лабы в п.Орево (первая подгруппа) и военка.=)";
								} else {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], сегодня только военка первой парой.";
								}
									break;
								case 'Tue':
								if ($date == '23.04.2019') {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], сегодня лабы в п.Орево (вторая подгруппа) и военка.=)";
								} else {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, сегодня выходной. =)";								
								}
									break;
								case 'Wed':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
									break;
								case 'Thu':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 ЗИС(сем) 390\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ОКЭ(сем) 526\n17:25-19:00 УГиФС(сем) 502ю";
									break;
								case 'Fri':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
									break;
								case 'Sat':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л\n12:00-13:35 ЧМ(сем) 526";
									break;
							}
								if ($date == '01.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
								if ($date == '02.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
								if ($date == '03.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
								if ($date == '04.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
						}
								if ($date == '09.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
								if ($date == '15.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\n13:00 лаба по ЦОСу 934л\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";}
					break;
				case 'дуцрасписаниеназавтра1':
						if(($numofnextstudweek % 2) == 0)
						{
    							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
							switch ($nextday){
								case 'Sun':
									$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";
									break;
								case 'Mon':
									$otvet = "[id{$user_id}|{$user_name}{$pref}], только военка первой парой.";
									break;
								case 'Tue':
									if($nextday == '09.04.2019')
									{
										$otvet = "[id{$user_id}|{$user_name}{$pref}], лабы по квантам.";
									}
									else
									{
										$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";
									}
									break;
								case 'Wed':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n13:50-15:25 Радиоавтоматика(сем) 526\n15:40-17:15 ЗИС(лек) 417ю\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
									break;
								case 'Thu':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ЦОС(сем) 502ю";
									break;
								case 'Fri':
								if ($nextdate == '12.04.2019') 
								{
									
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 (изм)ЗИС(лек) 417ю";
								} 
								else 
								{
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
								}
									break;
								case 'Sat':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л";
									break;
								}
						}
						else
						{
    							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель.";
							switch ($nextday) {
								case 'Sun':
									$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";
									break;
								case 'Mon':
								if ($nextdate == '15.04.2019') {
									$otvet = "[id{$user_id}|{$user_name}{$pref}],лабы в п.Орево (первая подгруппа) и военка.=)";
								} else {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], только военка первой парой.";
								}
									break;
								case 'Tue':
								if ($nextday == '23.04.2019') {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], лабы в п.Орево (вторая подгруппа) и военка.=)";
								} else {
									$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";								
								}
									break;
								case 'Wed':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";
									break;
								case 'Thu':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 ЗИС(сем) 390\n12:00-13:35 ЧМ(лек) 502ю\n13:50-15:25 Экономика(лек) 502ю\n15:40-17:15 ОКЭ(сем) 526\n17:25-19:00 УГиФС(сем) 502ю";
									break;
								case 'Fri':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n10:15-11:50 УГиФС(лек) 502ю\n12:00-13:35 УГиФС(лек) 502ю\n13:50-15:25 МиСВСВЧПсБО(лек) 417ю\n15:40-17:15 Радиоавтоматика(лек) 417ю";
									break;
								case 'Sat':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\n08:30-10:05 МСиСЦОС(лек) 1146л\n10:15-11:50 ЦОС(лек) 1146л\n12:00-13:35 ЧМ(сем) 526";
									break;
								}	
								if ($nextdate == '01.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
								if ($nextdate == '15.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\n13:00 лаба по ЦОСу 934л\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";}

								
						}
					break;	
				case 'дуцтрек':
						$media = "audio20017026_456239320";
					break;
				case 'дуцответсоветчикам':
					switch ($sex) {
						case '1':
							$otvet = "[id{$user_id}|{$user_name}{$pref}] их отца в кино водила...";
						break;
						case '2':
							$otvet = "Вертел [id{$user_id}|{$user_name}{$pref}] их на своём шампуре...";
						break;
						case '0':
							$otvet = "[id{$user_id}|{$user_name}{$pref}] их отца в кино водила...";
						break;
					}
					break;
				case 'дуцоблакосереги':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],https://yadi.sk/d/Q4kNHFmXDPHHLw";
					break;
				// case 'тест':
				// 			$otvet = "{$sex}";
				// 			break;		
					}
			//-------
			if (strpos($message, 'дуцпосчитай') !== false) // именно через жесткое сравнение
			{
				$message = str_replace('дуцпосчитай', '', $message);
    			$otvet =  $message;
			} else {
    			//$otvet =  'Не найдено';
			}
			//-------
			$request_params = [
				//'user_id' => $user_id,
				'random_id' => 0,
				'peer_id' => $peer_id,
				'message' => $otvet,
				'attachment' => $media,
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
