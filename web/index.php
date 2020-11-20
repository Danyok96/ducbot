<?php
define('TIMEZONE', 'Europe/Moscow');
date_default_timezone_set(TIMEZONE);
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
			$user_id = $data->object->from_id;
			$peer_id = $data->object->peer_id;
			$user_resp = [
				'user_ids' => $user_id,
				'fields' => 'sex',
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.21'
			];
			$user_info = json_decode(file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($user_resp)));
			$user_name = $user_info->response[0]->first_name;
			$message = $data->object->text;
			$attachments_type = $data->object->attachments[0]->type;
			$message_to_convert = $message;
			$message = mb_strtolower($message);
			$message_to_calc = $message;
			$dots = array(".",",","?"," ");
			$dots_to_calc = array(",","?"," ");
			$dots_to_convert = array(",","?");
			$message = str_replace($dots, "", $message);
			$message_to_calc = str_replace($dots_to_calc, "", $message_to_calc);
			$message_to_convert = str_replace($dots_to_convert, "", $message_to_convert);
			$date = date("d.m.Y");
			$day = date("D");
			$time = date("H:i");
			$test = strtotime($date);
			$numofweek = date("W",$test);
			$numofstudweek = $numofweek-35;
			$next_date = strtotime("+1 day");
			$nextdate = date("d.m.Y", $next_date);
			$nextday = date("D", $next_date);
			$testnext = strtotime($nextdate);
			$numofnextweek = date("W", $testnext);
			$numofnextstudweek = $numofnextweek-35;//---
			$sex = $user_info->response[0]->sex;
			$edit_flag = 0;
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
			$arr_of_day = [
					'Воскресенье',
					'Понедельник',
					'Вторник',
					'Среда',
					'Четверг',
				  	'Пятница',
				  	'Суббота'
				];
			$day_ru = date('w');
			$day_ru_next = date('w')+1;
			if($day_ru_next == '7'){$day_ru_next = '0';}
			//-----
			if ($user_id == 20017026) { $user_name = 'Создатель';$pref = '';}
			if ($user_id == 201182825) { $user_name = 'Ирусик';$pref = '-тян';}
			if ($user_id == 134572907) { $user_name = "Agent Kuz'mich";$pref = '';}
			if ($user_id == 346654275) { $user_name = 'Фил';$pref = '';}
			//----
					if($message == ''){
					if ($attachments_type == "audio_message") {
						$voice_link_mp = $data->object->attachments[0]->audio_message->link_mp3;
						//$otvet = "Ссылка на запись:\n{$voice_link_mp}";

						$witRoot = "https://api.wit.ai/speech?";
						$witVersion = "20170307";
						$witURL = $witRoot . "v=" . $witVersion ;

						// $ch = curl_init();
						// $header = array("Authorization: Bearer QCDW4ADLLIDB3NYO2OTDAPZAOQQXC2BU","Content-Type: audio/mpeg3");
						$token_bearer = 'QCDW4ADLLIDB3NYO2OTDAPZAOQQXC2BU'; # IAM-токен
						$audioFileName = $voice_link_mp;

						$file = fopen($audioFileName, 'rb');

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $witURL);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token_bearer, 'Content-Type: audio/mpeg3', 'Transfer-Encoding: chunked'));
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

						curl_setopt($ch, CURLOPT_INFILE, $file);
						curl_setopt($ch, CURLOPT_INFILESIZE, filesize($audioFileName));
						$res = curl_exec($ch);
						curl_close($ch);
						$decodedResponse = json_decode($res, true);

						$otvet = "{$decodedResponse["_text"]}";
						$message = 'дуц'.$otvet;
						$message = str_replace($dots, "", $message);
						//-----
					} else {
						//$otvet = "{$attachments_type}";
					}
					}
			//----
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
					$otvet = "[id{$user_id}|{$user_name}{$pref}], нормас чел, теперь наш староста, ебать.=)";
					break;
				case 'дуцдата':
					$ductime = strftime("%A",time());
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\n{$date},{$arr_of_day[$day_ru]}.\nНеделя: {$numofweek}.\nУчебная неделя: {$numofstudweek}.\nЗавтра: {$nextdate}.";
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
				case 'спасибодуц':
				case 'дуцспасибо':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], на здоровья!";
					break;
				case 'дуцкогдалабы':
					 $otvet = "[id{$user_id}|{$user_name}{$pref}],\n>Основы теории и техники радиосистем и комплексов управления:\nПервая лаба в 1147л 08.02.2020 в 10:15.";
					//$otvet = "[id{$user_id}|{$user_name}{$pref}], пока чиль.=)";
					//$media = "photo-180470421_456239020";
					break;
				case 'хейдуц':
					$otvet = "Чё кого, [id{$user_id}|{$user_name}{$pref}]?";
				 	break;
				// case 'дуцрасписание531':
				// case 'дуцрасписание531аудитории':
				// case 'дуцрасписаниелаболт':
				// 	$otvet = "[id{$user_id}|{$user_name}{$pref}],\nhttps://mail.bmstu.ru/~vlo@bmstu.ru/schedule531.html";
				// 	break;
				case 'дуцзаебись':
					$otvet = "Это по-кайфу, [id{$user_id}|{$user_name}{$pref}].=)";
					break;	
				case 'бунд':
				case 'бунт':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], так, блэт. Успокаеваемся!=)";
					break;
					//------------------------
				case 'дуцсписокпреподов':
				case 'дуцпреподы':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\n⚠Куратор:\nКомиссарова Елена Владимировна\nkomissarovaev@bmstu.ru\n✅Расчёт и проектирование антенных систем спутниковой связи и навигации:\nВечтомов Виталий Аркадьевич(Мильпопсов)\nvvechtomov@mail.ru\nvechtomov@bmstu.ru\n✅Основы теории и техники систем и комплексов радиопротиводействия:\nСавельев Алексей Николаевич\nsavelyev.an@bmstu.ru\n✅Моделирование антенн и микроволновых устройств:\nЛитун Владимир Игоревич\nv.i.litun@bmstu.ru\n✅Широкополосные и малогабаритные антенны:\nРусов Юрий Сергеевич\n✅Технологические СВЧ установки:\nХандамиров Виктор Ливанович\n✅Организация и планирование производства:\nПостникова Елена Сергеевна\n✅Техника и элементная база средств цифровой обработки сигналов:\nТачков Александр Анатольевич\n✅Моделирование радиотехнических систем:\n(лек):Нефедова Юлия Сергеевна\n(сем):Баулин Федор Борисович";
					break;
					//------------------------
				case 'дуцоблако':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], \nhttps://cloud.mail.ru/public/NAzt/FJpjdhFpZ";
					break;
				// case 'дуцоблакомыки':
				// case 'дуцоблакоякова':
				// case 'дуцоблакомыкольникова':
				// case 'дуцоблакоор':
				// case 'дуцоблакоора':
				// 	$otvet = "[id{$user_id}|{$user_name}{$pref}], \nhttps://yadi.sk/d/IN37NgpLzhI1SA";
				// 	break;
				case 'дуцсписокгруппы':
					$media = "photo-180470421_457239023";
					break;
				case 'дуцрасписаниеэкзаменов':
				//✅⛔⚠
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\n✅Спутниковые системы связи\n10.06.2020\n(в расписании: 9:00, но Вечтомов сказал, что в 10:00)\n✅Проектирование ФАР и АФАР\n15.06.2020(Крехтунов сказал, что 16)\n9:00 (это не точно)\n✅Основы телевидения\n19.06.2020\n14:00\n✅Основы теории и техники радиосистем и комплексов управления\n23.06.2020\n9:00";
					break;
				case 'начать':
				case 'дуцкоманды':
				case 'дуцсписоккоманд':
					$otvet = "[id{$user_id}|{$user_name}{$pref}], \nдуц облако\nдуц преподы\n**дуц ссылка на презентации\nдуц дата\nдуц время\nдуц трек\nдуц рандом\nдуц список группы\nдуц облако Сереги\nдуц расписание экзаменов\nдуц расписание\nдуц расписание на сегодня\nдуц расписание на завтра\nдуц посчитай...\n⚠Операторы: +, -, *, /, ^\n⚠Разделитель целого '.'\n⚠Константы: pi, e, Inf\n⚠Функции: sqrt, abs, sin, cos, tan, log, exp\n// - временно недоступные\n* - устаревшие, но действующие\n** - коррекция\nГолосовые без \"дуц\"\nОстальные команды пасхальные.=)";
					break;	
				case 'дуцрасписаниенасегодня':
					// $otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nЧиль, сегодня выходной. =)";
					// if ($date == '28.08.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nПока чилибось.=)";}
					// if ($date == '10.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня экзамен по РСПИ (10 января 14:00 1039л)";}
					// if ($date == '13.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня консультация по ТОР (13 января 12:00 507)";}
					// if ($date == '15.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня экзамен по ТОР (15 января 9:00 1146л)";}
					// if ($date == '17.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня консультация по ОЛТ (17 января 10:00 1039л)";}
					// if ($date == '20.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня экзамен по ОЛТ (20 января 9:00 1039л)";}
					// if ($date == '23.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня консультация по ОР (23 января 14:00 1146л)";}
					// if ($date == '24.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nСегодня экзамен по ОР (24 января 14:00 1146л)";}
				//-------------------------------
						if(($numofstudweek % 2) == 0)
						{
    							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
						 	switch ($day) {
								case 'Sun':
								//Воскресенье
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\nЧиль, сегодня выходной. =)";
									break;
								case 'Mon':
								//Понедельник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\n08:30-10:05 Моделирование антенн и микроволновых устройств(лек) 1039л\n10:15-11:50 Широкополосные и малогабаритные антенны(лек) 1039л\n12:00-13:35 Широкополосные и малогабаритные антенны(сем) 1039л";
									break;
								case 'Tue':
								//Вторник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\n13:50-15:25 Технологические СВЧ установки(лек) 1039л\n15:40-17:15 Технологические СВЧ установки(лек) 1039л";
									break;
								case 'Wed':
								//Среда
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\n15:40-17:15 Организация и планирование производства(сем) 425ю\n17:25-19:00 Расчёт и проектирование антенных систем спутниковой связи и навигации(сем) 531";
									break;
								case 'Thu':
								//Четверг
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\n08:30-10:05 Организация и планирование производства(лек) 502ю\n10:15-11:50 Моделирование радиотехнических систем(лек) 1146.1л";
									break;
								case 'Fri':
								//Пятница
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\n10:15-11:50 Моделирование радиотехнических систем(сем) 1039л\n12:00-13:35 Основы теории и техники систем и комплексов радиопротиводействия(сем) 1039л\n13:50-15:25 Основы теории и техники систем и комплексов радиопротиводействия(лек) 1146.1л\n15:40-17:15 Основы теории и техники систем и комплексов радиопротиводействия(лек) 1146.1л";
									break;
								case 'Sat':
								//Суббота
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - знаменатель.\nЧиль, сегодня выходной. =)";
									break;
							}
						 }
						 else
						 {
    		 					//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель.";
							switch ($day) {
								case 'Sun':
								//Воскресенье
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\nЧиль, сегодня выходной. =)";
									break;
								case 'Mon':
								//Понедельник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\n08:30-10:05 Моделирование антенн и микроволновых устройств(лек) 1039л\n10:15-11:50 Широкополосные и малогабаритные антенны(лек) 1039л\n12:00-13:35 Расчёт и проектирование антенных систем спутниковой связи и навигации(лек) 1039л";
									break;
								case 'Tue':
								//Вторник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\nЧиль, сегодня выходной. =)";
									break;
								case 'Wed':
								//Среда
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\n15:40-17:15 Организация и планирование производства(сем) 425ю\n17:25-19:00 Расчёт и проектирование антенных систем спутниковой связи и навигации(сем) 531";
									break;
								case 'Thu':
								//Четверг
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\n08:30-10:05 Техника и элементная база средств цифровой обработки сигналов(лек) 527\n10:15-11:50 Моделирование радиотехнических систем(лек) 1146.1л";
									break;
								case 'Fri':
								//Пятница
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\nЧиль, сегодня выходной. =)";
									break;
								case 'Sat':
								//Суббота
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\nЧиль, сегодня выходной. =)";
									break;
						 	 }
						 }
					 			if ($date == '05.02.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\nЧиль, сегодня выходной.=)";}
					 			if ($date == '08.02.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek} - числитель.\nСегондня лаба в 1147л в 10:15.";}
					// 			if ($date == '15.11.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nУчебная неделя: {$numofstudweek}.\n12:00-13:35 Основы теории и техники радиосистем передачи информации(лек) 1146л\nОЛТ(сем):\nс 13:50 в 531";}
					// 			if ($date == '03.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
					// 			if ($date == '04.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
					// 	}
					// 			if ($date == '09.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}], чиль, выходной. =)";}
					// 			if ($date == '15.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\n13:00 лаба по ЦОСу 934л\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";}
					 			if ($date == '28.08.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nПока чилибось.=)";}
					 			if ($date == '31.08.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nПока чилибось.=)";}
					break;
				case 'дуцрасписаниеназавтра':
					//$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЧиль, завтра выходной. =)";
					// if ($nextdate == '09.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра консультация по РСПИ (9 января 15:00 1146л)";}
					// if ($nextdate == '10.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра экзамен по РСПИ (10 января 14:00 1039л)";}
					// if ($nextdate == '13.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра консультация по ТОР (13 января 12:00 507)";}
					// if ($nextdate == '15.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра экзамен по ТОР (15 января 9:00 1146л)";}
					// if ($nextdate == '17.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра консультация по ОЛТ (17 января 10:00 1039л)";}
					// if ($nextdate == '20.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра экзамен по ОЛТ (20 января 9:00 1039л)";}
					// if ($nextdate == '23.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра консультация по ОР (23 января 14:00 1146л)";}
					// if ($nextdate == '24.01.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nЗавтра экзамен по ОР (24 января 14:00 1146л)";}
				//----------------------------
						 if(($numofnextstudweek % 2) == 0)
						 {
    		 					//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - знаменатель.";
							switch ($nextday){
								case 'Sun':
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\nЧиль, завтра выходной. =)";
									break;
								case 'Mon':
								//Понедельник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\n08:30-10:05 Моделирование антенн и микроволновых устройств(лек) 1039л\n10:15-11:50 Широкополосные и малогабаритные антенны(лек) 1039л\n12:00-13:35 Широкополосные и малогабаритные антенны(сем) 1039л";
									break;
								case 'Tue':
								//Вторник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\n13:50-15:25 Технологические СВЧ установки(лек) 1039л\n15:40-17:15 Технологические СВЧ установки(лек) 1039л";
									break;
								case 'Wed':
								//Среда
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\n15:40-17:15 Организация и планирование производства(сем) 425ю\n17:25-19:00 Расчёт и проектирование антенных систем спутниковой связи и навигации(сем) 531";
									break;
								case 'Thu':
								//Четверг
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\n08:30-10:05 Организация и планирование производства(лек) 502ю\n10:15-11:50 Моделирование радиотехнических систем(лек) 1146.1л";
									break;
								case 'Fri':
								//Пятница
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\n10:15-11:50 Моделирование радиотехнических систем(сем) 1039л\n12:00-13:35 Основы теории и техники систем и комплексов радиопротиводействия(сем) 1039л\n13:50-15:25 Основы теории и техники систем и комплексов радиопротиводействия(лек) 1146.1л\n15:40-17:15 Основы теории и техники систем и комплексов радиопротиводействия(лек) 1146.1л";
									break;
								case 'Sat':
								//Суббота
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - знаменатель.\nЧиль, завтра выходной. =)";
									break;
								}
						 }
						 else
						 {
    							//$otvet = "[id{$user_id}|{$user_name}-дуц],\n {$numofstudweek} - числитель.";
							switch ($nextday) {
								case 'Sun':
								//Воскресенье
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\nЧиль, завтра выходной. =)";
									break;
								case 'Mon':
								//Понедельник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\n08:30-10:05 Моделирование антенн и микроволновых устройств(лек) 1039л\n10:15-11:50 Широкополосные и малогабаритные антенны(лек) 1039л\n12:00-13:35 Расчёт и проектирование антенных систем спутниковой связи и навигации(лек) 1039л";
									break;
								case 'Tue':
								//Вторник
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\nЧиль, завтра выходной. =)";
									break;
								case 'Wed':
								//Среда
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\n15:40-17:15 Организация и планирование производства(сем) 425ю\n17:25-19:00 Расчёт и проектирование антенных систем спутниковой связи и навигации(сем) 531";
									break;
								case 'Thu':
								//Четверг
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\n08:30-10:05 Техника и элементная база средств цифровой обработки сигналов(лек) 527\n10:15-11:50 Моделирование радиотехнических систем(лек) 1146.1л";
									break;
								case 'Fri':
								//Пятница
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\nЧиль, завтра выходной. =)";
									break;
								case 'Sat':
								//Суббота
									$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\nЧиль, завтра выходной. =)";
									break;
								}							
					 			if ($nextdate == '08.02.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek} - числитель.\nЗавтра лаба в 1147л в 10:15.";}	
						// 		if ($nextdate == '01.11.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek}.\n12:00-13:35 Основы теории и техники радиосистем передачи информации(лек) 1146л\nОЛТ(сем):\nс 13:50 в 531";}
						// 		if ($nextdate == '15.11.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nЗавтра: {$nextdate}. {$arr_of_day[$day_ru_next]}.\nУчебная неделя: {$numofnextstudweek}.\n12:00-13:35 Основы теории и техники радиосистем передачи информации(лек) 1146л\nОЛТ(сем):\nс 13:50 в 531";}
						// 		if ($nextdate == '15.05.2019'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\n13:00 лаба по ЦОСу 934л\n15:40-17:15 Экономика(сем) 526\n17:25-19:00 ОКЭ(лек) 505\n19:10-20:45 ОУД(лек) 505";}	
					 			if ($nextdate == '31.08.2020'){$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСегодня: {$date}. {$arr_of_day[$day_ru]}.\nПока чилибось.=)";}
						 }
					break;	
				case 'дуцтрек':
					$rnd = rand(1,14);
					switch ($rnd) {
						case '1':
						//Sayonara детка
							$media = "audio20017026_456239443";
						break;
						case '2':
						//Все хотят меня поцеловать
							$media = "audio20017026_456239320";
						break;
						case '3':
						//Виски кола
							$media = "audio20017026_456239433";
						break;
						case '4':
						//Трахаюсь
							$media = "audio20017026_456239413";
						break;
						case '5':
						//Личка
							$media = "audio20017026_456239412";
						break;
						case '6':
						//Лол
							$media = "audio20017026_456239392";
						break;
						case '7':
						//The exodus
							$media = "audio20017026_456239464";
						break;
						case '8':
						//лбтд
							$media = "audio20017026_456239465";
						break;
						case '9':
						//20К
							$media = "audio20017026_456239428";
						break;
						case '10':
						//Между нами секс
							$media = "audio20017026_456239451";
						break;
						case '11':
						//вторник
							$media = "audio-2001793711_54793711";
						break;
						case '12':
						//мальчик
							$media = "audio-2001793710_54793710";
						break;
						case '13':
						//дура
							$media = "audio-2001793709_54793709";
						break;
						case '14':
						//молчи
							$media = "audio-2001793708_54793708";
						break;
					}
						////$media = "audio20017026_456239320";

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
				// case 'дуцсчётчик':
				// 	$fp = fopen("counter.txt", "r"); // Открываем файл в режиме чтения
				// 	if ($fp)
				// 	{
				// 		while (!feof($fp))
				// 		{
				// 		$mytext = fgets($fp, 999);
				// 		}
				// 	}
				// 	else $mytext = "Ошибка при открытии файла";
				// 	fclose($fp);
				// 	$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСчётчик дуца: {$mytext}";
				// 	break;
				// 	//------------
				// 	case 'дуцсоздайфайл':
				// 	if($user_id == 20017026)
				// 	{
				// 	$fp = fopen("counter.txt", "w"); // Открываем файл в режиме записи
				// 	$mytext = 0; // Исходная строка
				// 	$test = fwrite($fp, $mytext); // Запись в файл
				// 	fclose($fp); //Закрытие файла
				// 	$otvet = "[id{$user_id}|{$user_name}{$pref}],\nФайл создан";
				// 	}
				// 	else
				// 	{
				// 		$otvet = "[id{$user_id}|{$user_name}{$pref}],\nНет";
				// 	}
				// 		break;
					//------------
				// case 'дуцчтениефайла':
				// 	$fp = fopen("counter.txt", "r"); // Открываем файл в режиме чтения
				// 	if ($fp)
				// 	{
				// 		while (!feof($fp))
				// 		{
				// 		$mytext = fgets($fp, 999);
				// 		}
				// 	}
				// 	else $mytext = "Ошибка при открытии файла";
				// 	fclose($fp);
				// 	$otvet = "[id{$user_id}|{$user_name}{$pref}],\nТекст файла: {$mytext}";
				// 	break;
					//----------------------
				// case 'дуцчтениеизаписьфайла':
				// 	$fp = fopen("counter.txt", "r"); // Открываем файл в режиме чтения
				// 	if ($fp)
				// 	{
				// 		while (!feof($fp))
				// 		{
				// 		$mytext = fgets($fp, 999);
				// 		}
				// 	}
				// 	else $mytext = "Ошибка при открытии файла";
				// 	fclose($fp);

				// 	$otvet = "[id{$user_id}|{$user_name}{$pref}],\nТекст файла: {$mytext}";

				// 	$fp = fopen("counter.txt", "r+"); // Открываем файл в режиме записи
				// 	$mytext++; // Исходная строка
				// 	$test = fwrite($fp, $mytext); // Запись в файл
				// 	fclose($fp); //Закрытие файла
				// 	break;
				case 'дуцкнопки':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\nПопробуем запостить кнопки.=)";
					// $buttons = [
					// 			   'one_time' => false,
					// 			   'buttons' => [
					// 			     [[
					// 			       'action' => [
					// 			         'type' => 'text',
					// 			         'payload' => '{\"button\": \"1\"}',
					// 			         'label' => 'дуц расписание на сегодня'
					// 			       ],
					// 			       'color' => 'primary'
					// 			     ],
					// 			    [
					// 			       'action' => [
					// 			         'type' => 'text',
					// 			         'payload' => '{\"button\": \"2\"}',
					// 			         'label' => 'дуц расписание на завтра'
					// 			       ],
					// 			       'color" => "primary'
					// 			     ]]]];
					break;
				case 'дуцуберикнопки':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\nПопробуем убрать кнопки.=)";
					$buttons = [
									'buttons' => [],
								   'one_time' => true
								];
					break;
				case 'дуцоблакосерёги':
				case 'дуцоблакосереги':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\nhttps://yadi.sk/d/mlu282reZHZSIQ";
					break;
				case 'дуцсколькоосталось':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\n" . "Чилить осталось:\n" . time_elapsed_string('2020-02-07 00:00:00', true);
					break;
				case 'дуцссылканапрезентации':
					$otvet = "[id{$user_id}|{$user_name}{$pref}],\nСотСС:\nhttps://cloud.mail.ru/public/cgkk/48CYfcwtt\nТОР:\nhttps://cloud.mail.ru/public/4rtR/3agzjK72Z\nТПРС:\nhttps://cloud.mail.ru/public/wGCj/3xfHLB5AZ";
					break;
				case 'дуцрандом':
					//$rnd = rand(1,100);
					$otvet = rand(1,17);
					break;
				// case 'тест':
				// 	$otvet = time_elapsed_string('2019-09-09 00:00:00', true);
				//  	$media = "photo-180470421_456239020";
				//  	break;	
				case 'дуцрасписание':
					$media = "photo-180470421_457239024";
					break;
				// case 'дуцзаменина':
				// 	//$edit_flag = 1;
				// 	//$edited_name = "Тестовое_изменение";
				// 	$otvet = "Готово!";
				// 	$chat_id_target = '1';
					
				// 	break;
				case 'дуцдуц':
					$otvet = "[id{$user_id}|{$user_name}{$pref}] [id{$user_id}|{$user_name}{$pref}].";
					break;
					}
			//-------
					if (strpos($message, 'дуцзаменина') !== false && $user_id == 20017026)
					{
						$message = str_replace('дуцзаменина', '', $message);
						$chat_id_target = mb_substr( $message, 0, 1);
						$message = mb_substr( $message, 1);
						$edited_name = $message;
						$edit_flag = 1;
						
						$otvet = "Готово!";
					}
			//-------
					if (strpos($message, 'дуцнапиши') !== false && $user_id == 20017026) {
						$message = str_replace('дуцнапиши', '', $message);
						$peer_id = mb_substr( $message, 0, 1);
						$peer_id = $peer_id + 2000000000;
						$message = mb_substr( $message, 1);
						$edited_name = $message;
						
						$otvet = $message;
					}

			//-------
			if (strpos($message_to_calc, 'дуцпосчитай') !== false) // именно через жесткое сравнение
			{
				$message_to_calc = str_replace('дуцпосчитай', '', $message_to_calc);

				 $formula = $message_to_calc;
				 $precision = 2; // Number of digits after the decimal point
				 try {
				     $parser = new FormulaParser($formula, $precision);
				     $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
				     $input_formula = $parser->getFormula();
				   	if($result['1'] !== 'Syntax error'){$otvet = "[id{$user_id}|{$user_name}{$pref}]\nВыражение: ".$input_formula."\nОтвет: ". $result['1'];};
				  	if($result['1'] !== 'Invalid character'){$otvet = "[id{$user_id}|{$user_name}{$pref}]\nВыражение: ".$input_formula."\nОтвет: ". $result['1'];};

			    
				 } catch (\Exception $e) {
				      $otvet = 'Неа...';
				 }
			} else {
    			//$otvet =  'Не найдено';
			}
			//---------------------------------------------------
			//конвертация величин
			if(strpos($message_to_convert, 'дуц переведи') !== false)
			{
				$message_to_convert = str_replace('дуц переведи ', '', $message_to_convert);
				$pos_a = strpos($message_to_convert, 'в');
				$first_part = mb_substr( $message_to_convert, 0, $pos_a-1);
				$message_to_convert = str_replace($first_part, '', $message_to_convert);
				$second_part = $message_to_convert;
				$first_part = str_replace(' в ', '', $first_part);
				$otvet = "Строка: ".$message_to_convert."\nПервая часть: ".$first_part."\nВторая часть: ".$second_part;

			}
			else
			{

			}
			//----------------------------------------------------

			$request_params = [
				//'user_id' => $user_id,
				'random_id' => 0,
				'peer_id' => $peer_id,
				'message' => $otvet,
				'attachment' => $media,
				'keyboard' => $buttons,
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.92'
			];
			//------group_name
			$request_params_edit = [
				'chat_id' => $chat_id_target,
				'title' => $edited_name,
				'access_token' => getenv('VK_TOKEN'),
				'v' => '5.92'
			];

			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
// 
			if ($edit_flag == 1) {
				file_get_contents('https://api.vk.com/method/messages.editChat?' . http_build_query($request_params_edit));
				$edit_flag = 0;
			}
			
					// $fp = fopen("counter.txt", "r"); // Открываем файл в режиме чтения
					// if ($fp)
					// {
					// 	while (!feof($fp))
					// 	{
					// 	$mytext = fgets($fp, 999);
					// 	}
					// }
					// else $mytext = "Ошибка при открытии файла";
					// fclose($fp);

					// $fp = fopen("counter.txt", "r+"); // Открываем файл в режиме записи
					// $mytext++; // Исходная строка
					// $test = fwrite($fp, $mytext); // Запись в файл
					// fclose($fp); //Закрытие файла

			return 'ok';
			break;
	}
	return "Not Ok!";
});
function time_elapsed_string($datetime, $full = false) 
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if($now > $ago){return $string = 'Уже всё.';}
    else
    {
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'Лет',
        'm' => 'Месяцев',
        'w' => 'Недель',
        'd' => 'Дней',
        'h' => 'Часов',
        'i' => 'Минут',
        's' => 'Секунд',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $v . ': ' . $diff->$k . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(";\n", $string) . ' ещё осталось.' : 'сейчас.';
	}
}
$app->run();
