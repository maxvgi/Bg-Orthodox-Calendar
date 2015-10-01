<?php
/*******************************************************************************
// Функция определяет является ли указанный год високосным
*******************************************************************************/  
function isLeap($year) {
	return ((($year % 4)==0) && (($year % 100)!=0) || (($year % 400)==0)) ? true : false;
}
/*******************************************************************************
// Функция определяет количество дней в месяце
*******************************************************************************/  
function numDays ($month, $year) {
	$dim = array(31,28,31,30,31,30,31,31,30,31,30,31);
	return ($month == 2 && isLeap($year)) ? 29 : $dim[(int)$month-1];
}
/*******************************************************************************
// Функция возвращает количество дней между датами по новому и старому стилю
*******************************************************************************/  
function dd($year) {
	return ($year-$year%100)/100 - ($year-$year%400)/400 - 2;
}
/*******************************************************************************
// Функция возвращает дату по старому стилю
*******************************************************************************/  
function oldStyle($format, $month, $day, $year){
	$replace = array ();
	$week = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
	$wk = array ('Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб');
	$dd = dd($year);
	for ($i=0; $i<7; $i++) {
		$di = ($i+$dd)%7;								// Смещение дня недели
		$replace[$week[$i]] = $week[$di];
		$replace[$wk[$i]] = $wk[$di];
	}
	return  strtr ( dateRU ( date( $format, mktime ( 0, 0, 0, $month, $day, $year ) - ( $dd*24*3600 ) ) ), $replace );
}
/*******************************************************************************
// Перевод названия месяца на русский язык (именительный падеж, заглавная буква)
*******************************************************************************/  
function monthRU ( $str ) {
	$replace = array (	'January' => 'Январь',
						'February' => 'Февраль',
						'March' => 'Март',
						'April' => 'Апрель',
						'May' => 'Май',
						'June' => 'Июнь',
						'July' => 'Июль',
						'August' => 'Август',
						'September' => 'Сентябрь',
						'October' => 'Октябрь',
						'November' => 'Ноябрь',
						'December' => 'Декабрь');
	return strtr ( $str, $replace );
}
/*******************************************************************************
// Перевод даты на русский язык (родительный падеж, строчная буква)
*******************************************************************************/  
function dateRU ( $str ) {
	$replace = array (	'January' => 'января',
						'February' => 'февраля',
						'March' => 'марта',
						'April' => 'апреля',
						'May' => 'мая',
						'June' => 'июня',
						'July' => 'июля',
						'August' => 'августа',
						'September' => 'сентября',
						'October' => 'октября',
						'November' => 'ноября',
						'December' => 'декабря',
						'Jan' => 'янв',
						'Feb' => 'фев',
						'Mar' => 'мар',
						'Apr' => 'апр',
						'May' => 'мая',
						'Jun' => 'июн',
						'Jul' => 'июл',
						'Aug' => 'авг',
						'Sep' => 'сен',
						'Oct' => 'окт',
						'Nov' => 'ноя',
						'Dec' => 'дек',
						'Monday' => 'Понедельник',
						'Tuesday' => 'Вторник',
						'Wednesday' => 'Среда',
						'Thursday' => 'Четверг',
						'Friday' => 'Пятница',
						'Saturday' => 'Суббота',
						'Sunday' => 'Воскресенье',
						'Mon' => 'Пн',
						'Tue' => 'Вт',
						'Wed' => 'Ср',
						'Thu' => 'Чт',
						'Fri' => 'Пт',
						'Sat' => 'Сб',
						'Sun' => 'Вс'
					);
	return strtr ( $str, $replace );
}
/*******************************************************************************
// Функция определяет день Пасхи на заданный год
*******************************************************************************/
$easter_date=array();
function easter($format, $year, $old=false) {
	global $easter_date;
	$key=$format.'_'.$year.'_'.$old;
	if(!isset($easter_date[$key])) {
		$a=((19*($year%19)+15)%30);
		$b=((2*($year%4)+4*($year%7)+6*$a+6)%7);
		if ($a+$b>9) {
			$day=$a+$b-9;
			$month=4;
		} else {
			$day=22+$a+$b;
			$month=3;
		}
		if ($old) $dd = 0;
		else $dd = dd($year);
		$easter_date[$key]=dateRU ( date( $format, mktime ( 0, 0, 0, $month, $day, $year ) + ( $dd*24*3600 ) ) );
	}
	return $easter_date[$key];
}
/*******************************************************************************
// Функция определяет название Седмицы (Недели) по годичному кругу богослужений
*******************************************************************************/  
function sedmica ($month, $day, $year) {			
	$cd = date( 'z', mktime ( 0, 0, 0, $month, $day, $year )); 	// Порядковый номер дня в году
	$ed = easter('z', $year);									// Порядковый номер дня пасхи в году
	$wd = date( 'w', mktime ( 0, 0, 0, $month, $day, $year )); 	// Порядковый номер дня недели от 0 (воскресенье) до 6 (суббота)
	
	if ($cd < $ed-70) {				// До Недели о мытаре и фарисее идут седмицы по Пятидесятнице прошлого года
		$ed = easter('z', $year-1);								// Порядковый номер дня пасхи в предыдущем году
		$nw = (int)(($cd+(isLeap($year-1)?366:365)-($ed+49))/7)+1;
		if ($wd == 0) return "Неделя ".($nw-1)."-я по Пятидесятнице";
		else return "Седмица ".$nw."-я по Пятидесятнице";
	}
	else if ($cd == $ed-70) return "Неделя о мытаре и фарисее";	// Седмицы подготовительные
	else if ($cd < $ed-63) return "Седмица о мытаре и фарисее";	
	else if ($cd == $ed-63) return "Неделя о блудном сыне";	
	else if ($cd < $ed-56) return "Седмица о блудном сыне";	
	else if ($cd == $ed-56) return "Неделя мясопустная, о Страшнем суде";	
	else if ($cd < $ed-49) return "Сырная седмица (масленица)";						
	else if ($cd == $ed-49) return "Неделя сыропустная. Воспоминание Адамова изгнания. Прощеное воскресенье";						
	else if ($cd < $ed-13) {									// Седмицы Великого поста
		$nw = (int)(($cd - ($ed-49))/7)+1;
		if ($wd == 0) return "Неделя ".($nw-1)."-я Великого поста";
		else return "Седмица ".$nw."-я Великого поста";
	}
	else if ($cd < $ed-7) return "Седмица 6-я Великого поста (седмица ваий)";
	else if ($cd == $ed-7) return "Неделя 6-я Великого поста ваий (цветоносная, Вербное воскресенье)";
	else if ($cd < $ed) return "Страстная седмица";
	else if ($cd == $ed) return "";
	else if ($cd < $ed+7) return "Пасхальная (Светлая) седмица";
	else if ($cd < $ed+50) {									// Седмицы по Пасхе
		$nw = (int)(($cd - $ed)/7)+1;
		if ($wd == 0) return "Неделя ".$nw."-я по Пасхе";
		else return "Седмица ".$nw."-я по Пасхе";
	}
	else  {														// Седмицы по Пятидесятнице
		$nw = (int)(($cd - ($ed+49))/7)+1;
		if ($wd == 0) return "Неделя ".($nw-1)."-я по Пятидесятнице";
		else return "Седмица ".$nw."-я по Пятидесятнице";
	}

	return "";
}
/*******************************************************************************
// Функция получает данные из XML файла	
*******************************************************************************/  
function getXML ($url) {
    $bg_curl_val = get_option( 'bg_ortcal_curl' );
    $bg_fgc_val = get_option( 'bg_ortcal_fgc' );
    $bg_fopen_val = get_option( 'bg_ortcal_fopen' );
	
	$code = false;
	
	if ($bg_fgc_val == 'on' && !$code) {									// Попытка 1. Попробуем применить file_get_contents()
		$code = file_get_contents($url);		
	}

	if ($bg_fopen_val == 'on' && !$code) {									// Попытка 2. Если данные опять не получены попробуем применить fopen() 
		$ch=fopen($url, "r" );													// Открываем файл для чтения
		if($ch)	{
			while (!feof($ch))	{$code .= fread($ch, 2097152);}					// загрузка текста (не более 2097152 байт)
			fclose($ch);														// Закрываем файл
		}
	}
	
	if ($bg_curl_val == 'on' && function_exists('curl_init') && !$code) {	// Попытка 3. Если данные не получены и установлен cURL				
		$url = substr ($url, strlen(ABSPATH)-1);								// Путь из корневого каталога сайта
		$url = site_url( $url );												// URL файла
		$ch = curl_init($url);													// создание нового ресурса cURL
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);							// возврат результата передачи в качестве строки из curl_exec() вместо прямого вывода в браузер
		$code = curl_exec($ch);													// загрузка текста
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);										
		if ($httpCode != '200') $code = false;									// Проверка на код http 200
		curl_close($ch);														// завершение сеанса и освобождение ресурсов
	} 
	if (!$code) return false;												// Увы. Паранойя хостера достигла апогея. Файл не прочитан или ошибка
	return xml_array($code, true);											// Возвращаем PHP массив
}
/*******************************************************************************
// Функция для преобразования XML в PHP Array
*******************************************************************************/  
function xml_array($xml){
	$result = json_decode(json_encode((array)simplexml_load_string($xml)),1);
	return $result;
}
/*******************************************************************************
// Функция подготовки данных о событиях дня
*******************************************************************************/  
function dayEvents($month, $day, $year){
	global $events;
	
	$date = oldStyle('U', $month, $day, $year);						// Дата по старому стилю
	$leap = isLeap(oldStyle('Y', $month, $day, $year));				// true - если високосный год по старому стилю
	$ny =  date( 'U', mktime ( 0, 0, 0, 1, 1, $year ));				// Новый год по григорианскому календарю
	$easter = easter('U', $year, true);								// Пасха в текущем году
	$easter_prev = easter('U', $year-1, true);						// Пасха в предыдущем году
	$f_e = (date( 'U', mktime ( 0, 0, 0,12, 25, $year-1 ))-$easter_prev)/(3600*24)-date( 'w', mktime ( 0, 0, 0, 12, 25+dd($year-1), $year-1 )); // Кол-во дней до Недели св.отцов от Пасхи
	$ep_e = (date( 'U', mktime ( 0, 0, 0, 1, 6, $year ))-$easter_prev)/(3600*24)-date( 'w', mktime ( 0, 0, 0, 1, 6+dd($year), $year )); 		// Кол-во дней до Недели перед Богоявлением от Пасхи
	$y=$year;
	if ($date < $ny) $year-=1;										// Если дата по старому стилю до Нового года, то это предыдущий год
	$dd = dd($year);												// Отклонение григорианского календаря от юлианского в днях
	$wd = date( 'w', mktime ( 0, 0, 0, $month, $day, $year ));
	if ($wd == 3 || $wd == 5) $post = "Постный день";				// Пост по средам и пятницам
	else $post = "";
	if ($wd == 2 || $wd == 4 || $wd == 6) $noglans = "Браковенчание не совершается";	// Браковенчание не совершается накануне среды и пятницы всего года (вторник и четверг), и воскресных дней (суббота)
	else $noglans = "";
	
	$result = array();
	$cnt = count($events);
	$y = 0;
	if ($cnt) {
		for ($i=0; $i < $cnt; $i++) {
			$event = $events[$i];
			$s_date = (int) $event["s_date"];
			$s_month = (int) $event["s_month"];
			$f_date = (int) $event["f_date"];
			$f_month = (int) $event["f_month"];
			$name = $event["name"];
			$type = (int) $event["type"];


			//проверяем наличие кешированных даты начала и конца
			//если есть, не рассчитываем их второй раз
			if(!$event['start'] || !$event['finish']) {
				// Если невисокосный год, то события которые приходятся на 29 февраля празднуются 28 февраля
				if ( ! $leap ) {
					if ( $s_month == 2 && $s_date == 29 ) {
						$s_date == 28;
					}
					if ( $f_month == 2 && $f_date == 29 ) {
						$f_date == 28;
					}
				}

				if ( $s_month < 0 ) {        //  Сб./Вс. перед/после праздника или Праздник в Сб./Вс. перед/после даты
					// Если неделя Богоотцов совпадает с Неделей перед Богоявлением, то чтения Недели перед Богоявлением переносятся на 1 января ст.ст.
					if ( ( $f_e + 7 == $ep_e ) && ( $s_month == - 1 && $s_date == 0 && $f_month == 1 && $f_date == 6 && ( $type == 204 || $type == 207 ) ) ) {
						$finish = date( 'U', mktime( 0, 0, 0, 1, 1, $year ) );
						$start  = $finish;
					} else {
						$we     = date( 'w', mktime( 0, 0, 0, $f_month, $f_date, $year + $y ) + $dd * 3600 * 24 );                // День недели
						$finish = date( 'U', mktime( 0, 0, 0, $f_month, $f_date, $year + $y ) + ( $s_date - $we ) * 3600 * 24 );    // Смещение относительно даты на $s_date-$we дней
						$start  = $finish;

						if ( $s_month == - 1 && $we == $s_date ) {
							$name = "";
						}                                                // Если Сб./Вс. приходится на самый день праздника, то не отмечается
						if ( $s_month == - 3 && $we != $s_date ) {
							$name = "";
						}                                                // Если праздник не совпадает с указанным днем недели, то не отмечается
						if ( $y == 0 ) {
							$i --;
							$y = 1;
						}                                                                    // Проверяем дважды: для текущего и следующего года
						else {
							$y = 0;
						}
					}
				} else {
					if ( $s_month > 0 ) {
						$start = date( 'U', mktime( 0, 0, 0, $s_month, $s_date, $year ) );
					}            // Неподвижные события - начало периода
					else if ( $s_month == 0 ) {                                                                        // Переходящие события - начало периода
						if ( $type == 202 ) {
							$start = shift202( $s_date, $date, $year );
						}                                        // Чтения на утрени
						else if ( $type == 204 ) {
							$start = shift204( $s_date, $date, $year );
						}                                    // Апостол на Литургии
						else if ( $type == 207 ) {
							$start = shift207( $s_date, $date, $year );
						}                                    // Евангелие на Литургии
						else if ( $type >= 301 && $type <= 309 ) {
							$start = shift300( $s_date, $date, $year );
						}                    // Псалтирь
						else {                                                                                            // Все остальные события
							if ( $date >= $ny ) {
								$start = $easter + $s_date * 3600 * 24;
							}                                                // После Нового года - отсчет от текущей Пасхи
							else {
								$start = $easter_prev + $s_date * 3600 * 24;
							}                                                        // До Нового года - отсчет от предыдущей Пасхи
						}
					}

					if ( $f_month > 0 ) {
						$finish = date( 'U', mktime( 0, 0, 0, $f_month, $f_date, $year ) );
					}            // Неподвижные события - конец периода
					else if ( $f_month == 0 ) {                                                                        // Переходящие события - конец периода
						if ( $type == 202 ) {
							$finish = shift202( $s_date, $date, $year );
						}                                        // Чтения на утрени
						else if ( $type == 204 ) {
							$finish = shift204( $s_date, $date, $year );
						}                                // Апостол на Литургии
						else if ( $type == 207 ) {
							$finish = shift207( $s_date, $date, $year );
						}                                // Евангелие на Литургии
						else if ( $type >= 301 && $type <= 309 ) {
							$finish = shift300( $s_date, $date, $year );
						}                    // Псалтирь
						else {                                                                                            // Все остальные события
							if ( $date >= $ny ) {
								$finish = $easter + $f_date * 3600 * 24;
							}                                            // После Нового года - отсчет от текущей Пасхи
							else {
								$finish = $easter_prev + $f_date * 3600 * 24;
							}                                                    // До Нового года - отсчет от предыдущей Пасхи
						}
					}
				}

				// Обрабатываем коллизию, связанную со сменой года
				if ($start > $finish) {
					if ($start > $date) $start -= ($leap?366:365)*3600*24;	// Начало в прошлом году
					else $finish -= ($leap?366:365)*3600*24;				// Окончание в следующем году
				}

				//сохраняем дату начала и конца в кеш
				$events[$i]['start']=$start;
				$events[$i]['finish']=$finish;
			}

			$start=$event['start'];
			$finish=$event['finish'];

			if ($start && $finish) {
				// Событие относится к данному дню, если
				// - его наименование не пустое,
				// - день попадает в интервал между начальной и конечной датой события
				if ($name != "" && $date >= $start && $date <= $finish) {
					$s = $start+$dd*3600*24;
					$f = $finish+$dd*3600*24;
					$result[] = array (	"s_date" => date ("d", $s),
					                       "s_month" => date ("m", $s),
					                       "s_year" => date ("Y", $s),
					                       "f_date" => date ("d", $f),
					                       "f_month" => date ("m", $f),
					                       "f_year" => date ("Y", $f),
					                       "name" => $name,
					                       "type" => $event["type"]);
					if ($event["type"] == '10' || $event["type"] == '100') $post = "";	// Уже установлен пост по другой причине или сплошная седмица
					if ($event["type"] == '20') $noglans = "";							// Уже запрещено браковенчание по другой причине
				}
			}
		}
	}
	if ($post != "") {													// Пост по средам и пятницам
		$result[] = array (	"s_date" => $day, 
							"s_month" => $month,
							"s_year" => $year,
							"f_date" => $day, 
							"f_month" => $month,
							"f_year" => $year,
							"name" => $post,
							"type" => "10");
	}
	if ($noglans != "") {												// Браковенчание не совершается по вторникам, четвергам и субботам
		$result[] = array (	"s_date" => $day, 
							"s_month" => $month,
							"s_year" => $year,
							"f_date" => $day, 
							"f_month" => $month,
							"f_year" => $year,
							"name" => $noglans,
							"type" => "20");
	}
	return $result;
}

//массив для кеша:
$shift202_cache=array();
function shift202 ($d, $date, $year) {				// Смещение даты для Чтений на Утрене
	global $shift202_cache;
	$key=$d.'_'.$date.'_'.$year;
	if(!isset($shift202_cache[$key])) {
		if ($date < easter('U', $year, true)-7*3600*24) $year -= 1;		// До Вербного Воскресенья - отсчет от предыдущей Пасхи
		$easter = easter('U', $year, true);								// Пасха в текущем году
		$e_e = ($easter - easter('U', $year-1, true))/(3600*24);		// Количество дней от Пасхи до Пасхи
		if ($d >= $e_e-7) return false; 								// Отбрасываем лишние дни в конце цикла
		$shift202_cache[$key]=$easter+$d*3600*24;
	}
	return $shift202_cache[$key];
}

//массив для кеша:
$shift204_cache=array();
function shift204 ($d, $date, $year) {				// Смещение даты для Апостола на Литургии
	global $shift204_cache;
	$key=($d*3600*24+$date).'_'.$year;
	if(!isset($shift204_cache[$key])) {
		$easter = easter('U', $year, true);							// Пасха в текущем году
		$dd = dd($year);											// Отклонение григорианского календаря от юлианского в днях
		$easter_prev = easter('U', $year-1, true);					// Пасха в предыдущем году

		$end = $easter_prev/(3600*24) + 273;						// День конца годичного цикла
		$ec_e = $easter/(3600*24) - $end;							// Количество дней от конца годичного цикла до Пасхи
		if ($ec_e <= 70) {											// Если внутрь-Пасха
			if ($d > 210+$ec_e) return false; 							// Отбрасываем лишние дни в конце цикла
			if ($d < -70) return false;									// Отсчет нового цикла с Недели о мытаре и фарисее
		} else {													// Если вне-Пасха
			if ($ec_e > 97) {										// Если недостает более 4 седмиц, вставляем 17-ю седмицу между 31-й и 32-й
				if ($d < -97) $d = $d + 14;							// 17-я седмица хранится в начале базы данных с датами от -104 до -98
				else if ($d >= -97 && $d <= -84) $d = $d - 7;
			}
			else if ($d < -$ec_e) return false;						// Отсчет нового цикла начинаем за $ec_e дней до Пасхи
		}
		// Евангельское зачало Недели 28-й и апостольское зачало Недели 29-й читаются в Неделю святых праотец,
		// поэтому они меняются местами с соответствующими рядовым чтениями той Недели,
		// на которую придется в данном году Неделя свв. праотец.
		$ff_e = (date( 'U', mktime ( 0, 0, 0, 12, 25, $year ))-$easter)/(3600*24)-(7+date( 'w', mktime ( 0, 0, 0, 12, 25+$dd, $year ))); // Кол-во дней до Недели Праотцов от Пасхи
		if ($d == 252) $d = $ff_e;									// Неделя 29-я
		else if ($d == $ff_e) $d = 252;								// Неделя Праотцов

		if ($date <= $end*3600*24) $easter = $easter_prev;	// До конца годичного цикла - отсчет от предыдущей Пасхи

		$shift204_cache[$key]=$easter+$d*3600*24;
	}
	return $shift204_cache[$key];
}

//массив для кеша:
$shift207_cache=array();
function shift207 ($d, $date, $year) {				// Смещение даты для Евангелие на Литургии
	global $shift207_cache;
	$key=($d*3600*24+$date).'_'.$year;
	if(!isset($shift207_cache[$key])) {
		$easter      = easter( 'U', $year, true );                            // Пасха в текущем году
		$dd          = dd( $year );                                            // Отклонение григорианского календаря от юлианского в днях
		$er_e        = ( date( 'U', mktime( 0, 0, 0, 9, 14, $year ) ) - $easter ) / ( 3600 * 24 ) + ( 7 - date( 'w', mktime( 0, 0, 0, 9, 14 + $dd, $year ) ) ); // Кол-во дней до Недели по воздвижении от Пасхи
		$easter_prev = easter( 'U', $year - 1, true );                    // Пасха в предыдущем году
		$dd_prev     = dd( $year - 1 );                                    // Отклонение григорианского календаря от юлианского в днях в предыдущем году
		$er_e_prev   = ( date( 'U', mktime( 0, 0, 0, 9, 14, $year - 1 ) ) - $easter_prev ) / ( 3600 * 24 ) + ( 7 - date( 'w', mktime( 0, 0, 0, 9, 14 + $dd_prev, $year - 1 ) ) ); // Кол-во дней до Недели по воздвижении от Пасхи в предыдущем году

		$end  = $easter_prev / ( 3600 * 24 ) + 280 + ( $er_e_prev - 168 );        // День конца годичного цикла
		$ec_e = $easter / ( 3600 * 24 ) - $end;                            // Количество дней от конца годичного цикла до Пасхи

		if ( $ec_e <= 70 ) {                                            // Если внутрь-Пасха
			if ( $d > 210 + $ec_e ) {
				return false;
			}                            // Отбрасываем лишние дни в конце цикла
			if ( $d < - 70 ) {
				return false;
			}                                    // Отсчет нового цикла с Недели о мытаре и фарисее
		} else {                                                    // Если вне-Пасха
			if ( $ec_e > 97 ) {                                        // Если недостает более 4 седмиц, вставляем 17-ю седмицу между 31-й и 32-й
				if ( $d < - 97 ) {
					$d = $d + 14;
				}                            // 17-я седмица хранится в начале базы данных с датами от -104 до -98
				else if ( $d >= - 97 && $d <= - 84 ) {
					$d = $d - 7;
				}
			} else if ( $d < - $ec_e ) {
				return false;
			}                        // Отсчет нового цикла начинаем за $ec_e дней до Пасхи
		}

		// Евангельское зачало Недели 28-й и апостольское зачало Недели 29-й читаются в Неделю святых праотец,
		// поэтому они меняются местами с соответствующими рядовым чтениями той Недели,
		// на которую придется в данном году Неделя свв. праотец.
		$ff_e = ( date( 'U', mktime( 0, 0, 0, 12, 25, $year ) ) - $easter ) / ( 3600 * 24 ) - ( 7 + date( 'w', mktime( 0, 0, 0, 12, 25 + $dd, $year ) ) ) - ( $er_e - 168 ); // Кол-во дней до Недели Праотцов от Пасхи с учетом воздвиженской отступки
		if ( $d == 245 ) {
			$d = $ff_e;
		}                                    // Неделя 28-я
		else if ( $d == $ff_e ) {
			$d = 245;
		}

		if ( $date <= $end * 3600 * 24 ) {        // До конца годичного цикла - отсчет от предыдущей Пасхи
			$easter = $easter_prev;                // Пасха в указанном году
			$er_e   = $er_e_prev;                // Кол-во дней до Недели по воздвижении от Пасхи
		}
		// Воздвиженская отступка: начало Чтения Луки всегда в Пн. после Недели по Воздвижении
		if ( $d > 168 ) {
			$d = $d + ( $er_e - 168 );
		}                            // Смещение на величину Воздвиженской отступки $er_e-168
		if ( $date > $easter + 168 * ( 3600 * 24 ) && $date <= $easter + $er_e * ( 3600 * 24 ) ) {    // Если 18-я седмица случилась до Недели по Воздвижении,
			if ( $d > 168 - ( $er_e - 168 ) && $d <= 168 ) {
				$d = $d + ( $er_e - 168 );
			}                // то дублируем чтения предыдущих(ей) седмиц(ы)
		}

		$shift207_cache[$key]=$easter + $d * 3600 * 24;
	}
	return $shift207_cache[$key];
}

//массив для кеша:
$shift300_cache=array();
function shift300 ($d, $date, $year) {				// Смещение даты для Чтений Псалтири
	global $shift300_cache;
	$key=($d*3600*24+$date).'_'.$year;
	if(!isset($shift300_cache[$key])) {
		if ( $date < easter( 'U', $year, true ) - 50 * 3600 * 24 ) {
			$year -= 1;
		}    // До субботы перед Неделей сыропустной - отсчет от предыдущей Пасхи
		$easter = easter( 'U', $year, true );                                // Пасха в текущем году
		$d_e    = ( $date - $easter ) / ( 3600 * 24 );                                // Дней после Пасхи
		if ( $d_e > 12 ) {                                                // От субботы перед Антипасхой до субботы перед Неделей сыропустной
			$dw = ( $d_e % 7 < 6 ) ? ( $d_e % 7 + 7 ) : 6;                                // По 2 кафизмы с недельным циклом
			// От отдания Воздвижения (2.09 ст.ст.) до предпразднество Рождества Христова (20.12 ст.ст.) и от предпразнества Богоявления (15.01 ст.ст.) до субботы перед Неделей о блудном сыне
			if ( ( $date > date( 'U', mktime( 0, 0, 0, 9, 21, $year ) ) && $date < date( 'U', mktime( 0, 0, 0, 12, 20, $year ) ) ) ||
			     ( $date > date( 'U', mktime( 0, 0, 0, 1, 15, $year + 1 ) ) && $date < easter( 'U', $year + 1, true ) - 64 * 3600 * 24 )
			) {
				$dw += 350;
			} // По 3 кафизмы с недельным циклом

			if ( $d == $dw ) {
				return $date;
			}
		}

		$shift300_cache[$key]=$easter + $d * 3600 * 24;
	}
	return $shift300_cache[$key];
}
/*******************************************************************************
// Функция выводит на экран информацию об указанном дне
*******************************************************************************/  
function showDayInfo ( $day,				// День (по умолчанию - сегодня)
					$month,					// Месяц (по умолчанию - сегодня)
					$year,					// Год (по умолчанию - сегодня)
					$date,					// Формат даты по нов. стилю
					$old,					// Формат даты по ст. стилю
					$sedmica,				// Седмица
					$memory,				// Памятные дни
					$honor,					// Дни поминовения усопших
					$holiday,				// Праздники (уровень значимости)
					$img,					// Значок праздника по Типикону
					$saints,				// Святые
					$martyrs,				// Новомученники и исповедники российские
					$icons,					// Дни почитания икон Богоматери
					$posts,					// Постные дни
					$noglans,				// Дни, в которые браковенчание не совершается
					$readings,				// Чтения Апостола и Евангелие
					$links )				// Ссылки и цитаты
{
	if ($day == 'post') {							// Дата создания текущего поста
		$year = get_the_date('Y');
		$month = get_the_date('m');
		$day = get_the_date('d');
	}
	else if (function_exists ($day) ) {				// Пользовательская функция определения даты
		$year = call_user_func ($day, 'Y');
		$month = call_user_func ($day, 'm');
		$day = call_user_func ($day, 'd');
	}
	else if (substr ($day,0,1) == '-') {			// Смещение текущей даты назад
		$year = date('Y');
		$month = date('m');
		$day = date('d')-((int)(substr($day,1)));
	}
	else if (substr ($day,0,1) == '+') {			// Смещение текущей даты вперед 
		$year = date('Y');
		$month = date('m');
		$day = date('d')+((int)(substr($day,1)));
	}
	else {
		if ($year == '') $year = date('Y');
		if ($month == '' || ($month < 1 || $month > 12)) $month = date('m');
		if ($day == '') $day = date('d');
		$days = numDays ($month, $year);
		if ($day < 1) $day = 1;			// если день задан меньше единицы то первое число 
		if ($day > $days) $day = $days;	// а если дата больше количества дней в месяце, последний день месяца
	}
	// Нормализуем дату
	$mtime = mktime(0, 0, 0, $month, $day, $year);
	$day = date('d', $mtime);
	$month = date('m', $mtime);
	$year = date('Y', $mtime);
	$wd = date('w', $mtime);
	
	if ($sedmica != 'off') $sedmica = 'on';
	if ($memory != 'off') $memory = 'on';
	if ($honor != 'off') $honor = 'on';
	
	if (!is_numeric ( $holiday ) && $holiday != 'off') $holiday = 7;
	if ($holiday < 0) $holiday = 0;
	if ($holiday > 7) $holiday = 7;
	if ($img != 'off') $img = 'on';
	
	// Тип отображения ссылок и цитат 
	if ($links == 'on') $links = '';		// on - отображать ссылки, off - ничего не отображать, verses, quotes, и т.д. - цитаты.
	
	$quote = '';
	if ($date != 'off' && $date != '') $quote .= '<span class="bg_ortcal_date'.(($wd==0)?' bg_ortcal_sunday':'').'">'.dateRU (date($date, mktime(0, 0, 0, $month, $day, $year))).'</span>';
	if ($old != 'off' && $old != '') $quote .= '<span class="bg_ortcal_old'.(($wd==0)?' bg_ortcal_sunday':'').'">'.oldStyle ($old,  $month, $day, $year).'</span><br>';
	if ($sedmica != 'off') $quote .= '<span class="bg_ortcal_sedmica'.(($wd==0)?' bg_ortcal_sunday':'').'">'.sedmica ($month, $day, $year).'</span><br>';

	$e = dayEvents($month, $day, $year);
	$e1 = dayEvents($month, $day+1, $year);
	$cnt = count($e);
	$cnt1 = count($e1);
	if ($cnt) {
		// Памятные даты
		if ($memory != 'off') {
			$q = "";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 8) $q .= $e[$i]['name'].'. ';
			}
			if ($q) $quote .= '<span class="bg_ortcal_memory">'.$q.'</span><br>';
		}
		// Дни поминовения усопших
		if ($honor != 'off') {
			$q = "";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 9) $q .= $e[$i]['name'].'. ';
			}
			if ($q) $quote .= '<span class="bg_ortcal_honor">'.$q.'</span><br>';
		}
		// Праздники
		if ($holiday != 'off') {
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] <= $holiday) {
					if ($e[$i]['type'] <= 2) $quote .= '<span class="bg_ortcal_great">'.(($img=='off')?'':imgTypicon($e[$i]['type'])).$e[$i]['name'].'</span><br>';
					else if ($e[$i]['type'] <= 4) $quote .= '<span class="bg_ortcal_middle">'.(($img=='off')?'':imgTypicon($e[$i]['type'])).$e[$i]['name'].'</span><br>';
					else $quote .= '<span class="bg_ortcal_small">'.(($img=='off')?'':imgTypicon($e[$i]['type'])).$e[$i]['name'].'</span><br>';
				}
			}
		}
		// Дни почитания святых
		if ($saints != 'off') {
			$q = "";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 18) $q .= $e[$i]['name'].'. ';
			}
			if ($q) $quote .= (($saints!='on')?htmlspecialchars_decode($saints):'').'<span class="bg_ortcal_saints">'.$q.'</span><br>';
		}
		// Дни почитания исповедников и новомучеников российских
		if ($martyrs != 'off') {
			$q = "";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 19) $q .= $e[$i]['name'].'. ';
			}
			if ($q) $quote .= (($martyrs!='on')?htmlspecialchars_decode($martyrs):'').'<span class="bg_ortcal_martyrs">'.$q.'</span><br>';
		}
		// Дни почитания икон
		if ($icons != 'off') {
			$q = "";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 17) $q .= $e[$i]['name'].'. ';
			}
			if ($q) $quote .= (($icons!='on')?htmlspecialchars_decode($icons):'').'<span class="bg_ortcal_icons">'.$q.'</span><br>';
		}
		// Посты и светлые седмицы
		if ($posts != 'off') {
			$q ="";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 10) $q = $e[$i]['name'].'. ';
			}
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 100) $q = $e[$i]['name'].'. ';
			}
			if ($q) $quote .= (($posts!='on')?htmlspecialchars_decode($posts):'').'<span class="bg_ortcal_posts">'.$q.'</span><br>';
		}
		// Дни, в которые браковенчание не проводится
		if ($noglans != 'off') {
			$q ="";
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] == 20) $q = $e[$i]['name'].'. ';
			}
			if ($q) $quote .= (($noglans!='on')?htmlspecialchars_decode($noglans):'').'<span class="bg_ortcal_noglans">'.$q.'</span><br>';
		}
		// Евангельские чтения
		if ($readings != 'off') {
			$q ="";
			for ($id=0; $id < 110; $id++) {$qq[$id]=""; $qq1[$id]="";}
			$qtitle = '';
			for ($i=0; $i < $cnt; $i++) {
				if ($e[$i]['type'] >= 200 && $e[$i]['type'] <= 309) {							
					$id = $e[$i]['type']-200;
					$qq[$id] .= (($qq[$id]!="")?('; '):('')).$e[$i]['name'];	// На сегодня
				}
			}
			for ($i=0; $i < $cnt1; $i++) {
				if ($e1[$i]['type'] >= 200 && $e1[$i]['type'] <= 309) {							
					$id = $e1[$i]['type']-200;
					$qq1[$id] .= (($qq1[$id]!="")?('; '):('')).$e1[$i]['name'];	// На завтра
				}
			}

			if ($readings == 'M') {												// Только Утрени
				if ($qq[2] != "") $q .= $qq[2];
			}
			else if ($readings == 'A') {										// Только Апостол на Литургию
				if ($qq[4] != "") $q .= $qq[4];
			}
			else if ($readings == 'G') {										// Только Евангелие на Литургию
				if ($qq[7] != "") $q .= $qq[7];
			}
			else if ($readings == 'AG') {										// Только Апостол и Евангелие на Литургию
				if ($qq[4] != "") $q .= $qq[4];
				if ($qq[7] != "") $q .= (($q!="")?('; '):('')).$qq[7];
			}
			else if ($readings == 'MAG') {										// Только Утрени и Апостол и Евангелие на Литургию
				if ($qq[2] != "") $q .= $qq[2];
				if ($qq[4] != "") $q .= (($q!="")?('; '):('')).$qq[4];
				if ($qq[7] != "") $q .= (($q!="")?('; '):('')).$qq[7];
			}
			else if ($readings == 'E') {										// Только Вечерни
				if ($qq[8] != "") $q .= $qq[8];
			}
			else if ($readings == 'H') {										// Только Часы
				if ($qq[1] != "") $q .= $qq[1];
				if ($qq[3] != "") $q .= (($q!="")?('; '):('')).$qq[3];
				if ($qq[6] != "") $q .= (($q!="")?('; '):('')).$qq[6];
				if ($qq[9] != "") $q .= (($q!="")?('; '):('')).$qq[9];
			}
			else if ($readings == 'F') {										// Только Праздники
				$q .= printReadins ($qq, 1);
				$q .= (($q!="")?('<br>'):('')).printReadins ($qq, 2);
				$q .= (($q!="")?('<br>'):('')).printReadins ($qq, 3);
				$q .= (($q!="")?('<br>'):('')).printReadins ($qq, 4);
				$q .= (($q!="")?('<br>'):('')).printReadins ($qq, 5);
				$q .= (($q!="")?('<br>'):('')).printReadins ($qq, 6);
			}
			else if ($readings == 'P') {										// Только Псалтирь
				if ($qq[102] != "") $q .= $qq[102];
				if ($qq[108] != "") $q .= (($q!="")?('; '):('')).$qq[108];
				if ($qq[101] != "") $q .= (($q!="")?('; '):('')).$qq[101];
				if ($qq[103] != "") $q .= (($q!="")?('; '):('')).$qq[103];
				if ($qq[106] != "") $q .= (($q!="")?('; '):('')).$qq[106];
				if ($qq[109] != "") $q .= (($q!="")?('; '):('')).$qq[109];
			}
			else {																// Все чтения
				$qz = "";
				$q1 = printReadins ($qq, 1);		// Великие Господские праздники
				$q2 = printReadins ($qq, 2);		// Великие Богородничные праздники
				$q3 = printReadins ($qq, 3);		// Бденнные праздники
				$q4 = printReadins ($qq, 4);		// Полиелейные праздники
				$q5 = printReadins ($qq, 5);		// Славословные праздники
				$q6 = printReadins ($qq, 6);		// Шестиричные праздники
				$q8 = printReadins ($qq, 8);		// Особые чтения дня ВМЕСТО рядовых
				$q9 = printReadins ($qq, 9);		// Особые чтения дня ВМЕСТЕ с рядовыми (под зачало)
				$q10 = printReadins ($qq, 10);		// Чтения Псалтири
// То же, но на завтра
				$q11 = printReadins ($qq1, 1);		// Великие Господские праздники
				$q12 = printReadins ($qq1, 2);		// Великие Богородничные праздники
				$q13 = printReadins ($qq1, 3);		// Бденнные праздники
				$q14 = printReadins ($qq1, 4);		// Полиелейные праздники
				$q15 = printReadins ($qq1, 5);		// Славословные праздники
				$q16 = printReadins ($qq1, 6);		// Шестиричные праздники
				$q18 = printReadins ($qq1, 8);		// Особые чтения дня ВМЕСТО рядовых
				$q19 = printReadins ($qq1, 9);		// Особые чтения дня ВМЕСТЕ с рядовыми (под зачало)
				$q20 = printReadins ($qq1, 10);		// Чтения Псалтири

// В церковном году есть воскресенья, когда, помимо рядовых чтений, положены еще и особые: чтения Недели святых праотец, святых отец пред Рождеством Христовым, по Рождестве Христове, пред Просвещением, по Просвещении. 
// Устав предписывает рядовые чтения этих Недель или опускать совсем, как в Недели святых праотец и святых отец, или разрешает читать под зачало, то есть два подряд, в случае, «аще не будет отступки» (см. Типикон под 26 декабря, 9-е «зри»). 
// В праздники Рождества Христова и Богоявления Господня, случившиеся в воскресенье, рядовое воскресное зачало не читается. 
				if ($q8 != "") $qz .= $q8;
				if ($q9 != "") $qz .= (($qz!="")?('<br>'):('')).$q9;
				// В великие праздники Господские, Богородичные и святых, которым положено бдение, рядовые Апостол и Евангелие не читаются, а только данному празднику или святому. 
				if ($q1 != "") $qz .= (($qz!="")?('<br>'):(''))."<em><strong>Праздник:</strong></em> ".$q1;
				// Но если великий Богородичен праздник или святого с бдением случится в воскресный день тогда читается сначала воскресный рядовой Апостол и Евангелие, а потом уже праздника или святого.
				if ($q8 == "" && $q1 == "" && !(($q2 != "" || $q3 != "")&& $wd != 0)) $qz .= (($qz!="")?('<br><em>Ряд.: </em>'):('')).printReadins ($qq, 0);	// Рядовые чтения
				// Если завтра случится праздник Господский, или Богородичный и святого, которому положено бдение и при этом завтра не воскресенье (сегодня не суббота), то сегодня читаются рядовые Апостол и Евангелие завтрешнего дня "под зачало".
				if ($q18 != "" || $q11 != "" || (($q12 != "" || $q13 != "")&& $wd != 6)) $qz .= (($qz!="")?('<br>'):(''))."<em>Под зач.: </em>".printReadins ($qq1, 0); // Под зачало, наконуне праздников
				if ($q2 != "" || $q3 != "") $qz .= (($qz!="")?('<br>'):(''))."<em><strong>Праздник:</strong></em> ".$q2." ".$q3;
				if ($q4 != "" || $q5 != "" || $q6 != "") $qz .= (($qz!="")?('<br>'):(''))."<em>Праздник:</em> ".$q4." ".$q5." ".$q6;
				if ($qz != "") $qz = "<em><strong>Евангелие и Апостол:</strong></em><br>".$qz;
				if ($readings != 'N' && $q10 != "") $qz .= (($qz!="")?('<br>'):(''))."<em><strong>Псалтирь:</strong></em><br>".$q10;
				$q .= $qz;
				// Если не $readings специальные символы, то выводим их на экран
				if ($readings!='on' && $readings != 'N') $qtitle = htmlspecialchars_decode($readings);
			}
// Если подключен плагин Bg Bible References, то допускается расширенное представление Чтений
			if (function_exists ('bg_bibfers_convertTitles') && $links != 'off') {
				$q = bg_bibfers_convertTitles($q, $links); // Преобразуем заголовки и подсвечиваем ссылки или выводим на экран текст Священного Писания
			}
			if ($q) $quote .= $qtitle.'<span class="bg_ortcal_readings">'.$q.'</span><br>';
		}
		
	}
	return "{$quote}";
}

/*******************************************************************************
// Функция формирует вывод на экран списка чтений Евангелие и Апостола
*******************************************************************************/  
function printReadins ($qq, $type) {
	$q = "";
	if ($qq[$type*10+2] != "") $q .= '<em> На утр.: - </em>'.$qq[$type*10+2];
	if ($qq[$type*10+4] != "" || $qq[$type*10+7] != "") {
		$q .= '<em> На лит.: - </em>';
		if ($qq[$type*10+4] != "") $q .= '<em> Ап.: </em>'.$qq[$type*10+4];
		if ($qq[$type*10+7] != "") $q .= '<em> Ев.: </em>'.$qq[$type*10+7];
	}
	if ($qq[$type*10+8] != "") $q .= '<em> На веч.: - </em>'.$qq[$type*10+8];
	if ($qq[$type*10+1] != "") $q .= '<em> На 1-м часе: - </em>'.$qq[$type*10+1];
	if ($qq[$type*10+3] != "") $q .= '<em> На 3-м часе: - </em>'.$qq[$type*10+3];
	if ($qq[$type*10+6] != "") $q .= '<em> На 6-м часе: - </em>'.$qq[$type*10+6];
	if ($qq[$type*10+9] != "") $q .= '<em> На 9-м часе: - </em>'.$qq[$type*10+9];
	if ($qq[$type*10+5] != "") $q .= ' '.$qq[$type*10+5];

	return $q;
}

/*******************************************************************************
// Функция выводит на экран значок по Типикону
*******************************************************************************/  
function imgTypicon ($lavel) {
	$title = array ('Светлое Христово Воскресение. Пасха', 'Двунадесятый праздник', 'Великий праздник', 'Средний бденный праздник', 'Средний полиелейный праздник', 'Малый славословный праздник', 'Малый шестиричный праздник', 'Вседневный праздник. Cовершается служба, не отмеченная в Типиконе никаким знаком');
	return '<img src="'.plugins_url( 'js/S'.$lavel.'.gif' , dirname(__FILE__) ).'" title="'.$title[$lavel].'" /> ';
}
