<?php
////======================================================////
////																										  ////
////              	Библиотека php-хелперов	              ////
////																											////
////======================================================////
/**
 *
 * write2log                  | Возбудить событие R2\Event с ключём "m2:write2log"
 * r1_current_user_id         | Получить ID текущего пользователя, на основе кук и кэша пакета M7
 * runcommand                 | Провести авторизацию и выполнить команду
 * r1_get_doc_locale          | Получить локаль документа C,M-пакета
 * r1_get_doc_layoutid        | Получить ID L-пакета - шаблона по умолчанию для указанного D-пакета
 * r1_url_exist               | Узнать, существует ли указанный URL
 * r1_array_unique_recursive  | array_unique для многомерных массивов
 * r1_udatetime               | Get string repres.of datetime with microseconds
 *
 */
////======================================================//*/
//// 			        		         ////
//// 	   Подключение классов	 ////
//// 			         		         ////
////===========================////

// Классы, поставляемые Laravel
use Illuminate\Routing\Controller as BaseController,
    Illuminate\Support\Facades\App,
    Illuminate\Support\Facades\Artisan,
    Illuminate\Support\Facades\Auth,
    Illuminate\Support\Facades\Blade,
    Illuminate\Support\Facades\Bus,
    Illuminate\Support\Facades\Cache,
    Illuminate\Support\Facades\Config,
    Illuminate\Support\Facades\Cookie,
    Illuminate\Support\Facades\Crypt,
    Illuminate\Support\Facades\DB,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\Event,
    Illuminate\Support\Facades\File,
    Illuminate\Support\Facades\Hash,
    Illuminate\Support\Facades\Input,
    Illuminate\Foundation\Inspiring,
    Illuminate\Support\Facades\Lang,
    Illuminate\Support\Facades\Log,
    Illuminate\Support\Facades\Mail,
    Illuminate\Support\Facades\Password,
    Illuminate\Support\Facades\Queue,
    Illuminate\Support\Facades\Redirect,
    Illuminate\Support\Facades\Redis,
    Illuminate\Support\Facades\Request,
    Illuminate\Support\Facades\Response,
    Illuminate\Support\Facades\Route,
    Illuminate\Support\Facades\Schema,
    Illuminate\Support\Facades\Session,
    Illuminate\Support\Facades\Storage,
    Illuminate\Support\Facades\URL,
    Illuminate\Support\Facades\Validator,
    Illuminate\Support\Facades\View;

////======================================================//*/
//// 			         ////
//// 	   Функции	 ////
//// 			         ////
////===============////

  //-----------//
  // write2log //
  //-----------//
  if(!function_exists('write2log')) {
    /**
     * Возбудить событие R2\Event с ключём "m2:write2log"
     * Модуль (или модули), реализующий лог, обработает его, и сделает запись.
     *
     * @param  string $msg
     * @param  array $tags
     *
     * @return mixed
     */
    function write2log($msg, $tags = []) {

      return Event::fire(new \R2\Event([
        'keys'      => ['m2:write2log'],
        'tags'      => $tags,
        'msg'       => $msg
      ]));

    }
  } else \Log::info('Внимание! Пакету R1 не удалось определить функцию write2log, поскольку такая уже есть!');


  //--------------------//
  // r1_current_user_id //
  //--------------------//
	if(!function_exists('lib_current_user_id')) {
		/**
		 * lib_current_user_id | Получить ID текущего пользователя, на основе кук и кэша от модуля M7
     *
		 * @return array
		 */
		function lib_current_user_id() {

      return Request::cookie('m7_auth_cookie') ?: Cache::get('m7_anon_id') ?: -1;

		}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_current_user_id, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_current_user_id, поскольку такая уже есть!', ['R1','r1_current_user_id']);
  }


  //------------//
  // runcommand //
  //------------//
  if(!function_exists('runcommand')) {
    /**
     * Провести авторизацию и выполнить команду
     *
     *  # Что возвращает
     *    - JSON-массив:
     *
     *      [
     *        "status"        // Статус результата выполнения команды
     *        "timestamp"     // Timestamp прихода запроса от клиента
     *        "data"          // Данные
     *      ]
     *
     *  # Какие статусы бывают, зависимость data от статуса
     *
     *    0   // Команда выполнена успешно. В data результаты её выполненя.
     *    -1  // Нет доступа. В data строка-сообщение об этом.
     *    -2  // Команда завершилась с ошибкой. В data текст ошибки.
     *
     *
     * @param  mixed $command
     * @param  array $data
     * @param  mixed $userid
     * @param  mixed $queue
     *
     * @return mixed
     */
    function runcommand($command, $data = [], $userid = 0, $queue = ['on'=>false, 'delaysecs'=>'']) {

//      // 1. Получить ID пользователя, запустившего эту команду
//      $id = !empty($userid) ? $userid : 0;
//
//
//      // 2. Получить все права пользователя с $id
//
//        // 2.1. Если это анонимный пользователь
//        if(!Request::cookie('m7_auth_cookie')) {
//          $permissions = Cache::get('m7_anon_permissions');
//        }
//
//        // 2.2. Если это аутентифицированный пользователь
//        else {
//          $permissions = Cache::tags(['m7', 'm7_permissions_of_user'])->get('m7_permissions_of_user_'.$id);
//        }
//
//        // 2.3. Если $permissions пуста, присвоить ей пустую строку
//        if(empty($permissions)) $permissions = '';
//
//        // 2.4. Преобразовать $permissions в массив с разделителем ','
//        $permissions_arr = explode(',', $permissions);
//
//
//      // 3. Получить код команды в формате, принятом для прав на исполнение команд
//      // - Пример такого кода: "M1_Main_C1"
//
//        // 3.1. Разбить полностью квалифицированный путь к команде на массив сегментов
//        // - Пример такого пути: \M5\Documents\Main\Commands\C1_get_datetime
//        $segments = explode('\\', $command);
//
//        // 3.2. Получить ID модуля, имя документа и ID команды
//
//          // ID модуля
//          $id_module = $segments[1];
//
//          // Имя документа
//          $doc = $segments[3];
//
//          // ID команды
//          $id_command = explode('_', $segments[5])[0];
//
//        // 3.3. Получить код команды в требуемом формате
//        $command_code = $id_module . '_' . $doc . '_' . $id_command;
//
//
//      // 4. Попробовать найти право типа 2, имеющее имя $command_code
//
//        // 4.1. Провести поиск права
//        $p = \M7\Models\MD4_permissions::where('id_type','=',2)->where('name','=',$command_code)->first();
//
//
//      // 5. Определить, имеет ли пользователь, от чего имени исполняется команда, исполнять её
//
//        // 5.1. Подготовить переменную для результата
//        $is_have_permission = false;
//
//        // 5.2. Если $p найдено, и есть в $permissions_arr, значит имеет
//        if(!empty($p)) {
//          if(in_array($p->id, $permissions_arr)) $is_have_permission = true;
//        }
//
//        // 5.3. Если $userid == 0, значит имеет
//        if($userid == 0) $is_have_permission = true;
//
//
//      // 6. Если не имеет права
//      // - Вернуть -1
//      if($is_have_permission == false) {
//
//        return -1;
//
//      }



      // X. Выполнить команду $command
      // - Передав ей данные $data

        // Синхронно
        if($queue['on'] == false) $result = Bus::dispatch(new $command($data));

        // Асинхронно
        else {

          if(empty($queue['delaysecs'])) Queue::push(new $command($data));
          else Queue::later($queue['delaysecs'], new $command($data));

        }


      // Y. Подготовить массив с ответом, и вернуть

        // Если команда выполняется синхронно
        if($queue['on'] == false) {
          $response = [
            "status"    => $result['status'],
            "data"      => $result['data']
          ];
          if(array_key_exists('timestamp', $data))
            $response['timestamp'] = $data['timestamp'];
          return $response;
        }

        // Если команда выполняется асинхронно
        if($queue['on'] == true) {
          $response = [
            "status"    => 0,
            "data"      => ""
          ];
          if(array_key_exists('timestamp', $data))
            $response['timestamp'] = $data['timestamp'];
          return $response;
        }

    }
  } else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию runcommand, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию runcommand, поскольку такая уже есть!', ['R1','runcommand']);
  }


  //-------------------//
  // r1_get_doc_locale //
  //-------------------//
	if(!function_exists('r1_get_doc_locale')) {
		/**
		 * Получить локаль документа C,M-пакета
     *
     * @param  string $packid
     *
		 * @return array
		 */
		function r1_get_doc_locale($packid) {

      // 1] Сохранить текущие настройки Storage
      $default  = config('filesystems.default');
      $root     = config('filesystems.disks.local.root');

      // 2] Настроить Storage на каталог config
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('config')]);

      // 3] Подготовить переменную для локали
      $locale = '';

      // 4] Получить локаль
      if(!file_exists(base_path('config/'.$packid.'.php')))
        $locale = config('app.locale');
      else {
        $locale = config($packid.'.locale');
        if($locale == 'APP') $locale = config('app.locale');
      }
      if(empty($locale)) $locale = 'RU';

      // 5] Установить старые настройки Storage
      config(['filesystems.default' => $default]);
      config(['filesystems.disks.local.root' => $root]);

      // 6] Вернуть результат
      return $locale;

		}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_get_doc_locale, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_get_doc_locale, поскольку такая уже есть!', ['R1','r1_get_doc_locale']);
  }


  //---------------------//
  // r1_get_doc_layoutid //
  //---------------------//
	if(!function_exists('r1_get_doc_layoutid')) {
		/**
		 * Получить ID L-пакета - шаблона по умолчанию для указанного D-пакета
     *
     * @param  string $packid
     *
		 * @return array
		 */
		function r1_get_doc_layoutid($packid) {



      // N] Вернуть результат
      return 'L1';

		}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_get_doc_layoutid, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_get_doc_layoutid, поскольку такая уже есть!', ['R1','r1_get_doc_layoutid']);
  }


  //---------------//
  // r1_url_exists //
  //---------------//
	if(!function_exists('r1_url_exists')) {
		/**
		 * Узнать, существует ли указанный URL
     *
     * @param  string $url
     *
		 * @return bool
		 */
		function r1_url_exists($url) {

      if(preg_match("#^https://#ui", $url) != 0)
        $url = str_replace("https://", "", $url);
      if(preg_match("#^http://#ui", $url) != 0)
        $url = str_replace("http://", "", $url);

      if (strstr($url, "/")) {
          $url = explode("/", $url, 2);
          $url[1] = "/".$url[1];
      } else {
          $url = array($url, "/");
      }

      $fh = fsockopen($url[0], 80);
      if ($fh) {
          fputs($fh,"GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
          if (fread($fh, 22) == "HTTP/1.1 404 Not Found") { return FALSE; }
          else { return TRUE;    }

      } else { return FALSE;}

		}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_url_exists, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_url_exists, поскольку такая уже есть!', ['R1','r1_url_exists']);
  }


  //---------------------------//
  // r1_array_unique_recursive //
  //---------------------------//
	if(!function_exists('r1_array_unique_recursive')) {
		/**
		 * array_unique для многомерных массивов
     *
     * @param  array $array
     *
		 * @return array
		 */
    function r1_array_unique_recursive($array)
    {
      $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
      $result = array_values($result);

      foreach ($result as $key => $value)
      {
        if ( is_array($value) )
        {
          $result[$key] = r1_array_unique_recursive($value);
        }
      }

      return $result;
    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_array_unique_recursive, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_array_unique_recursive, поскольку такая уже есть!', ['R1','r1_array_unique_recursive']);
  }


  //--------------//
  // r1_udatetime //
  //--------------//
	if(!function_exists('r1_udatetime')) {
		/**
		 * Get string repres.of datetime with microseconds
     * r1_udatetime('Y-m-d H:i:s.u');       // "2014-01-01 12:20:24.42342"
     * \Carbon\Carbon::createFromFormat('Y-m-d H:m:s.u', r1_udatetime('Y-m-d H:i:s.u'));
     *
     * @param  string $format
     * @param  string $utimestamp
     *
		 * @return array
		 */
    function r1_udatetime($format = 'u', $utimestamp = null)
    {
      if (is_null($utimestamp))
          $utimestamp = microtime(true);

      $timestamp = floor($utimestamp);
      $milliseconds = round(($utimestamp - $timestamp) * 1000000);

      return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_udatetime, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_udatetime, поскольку такая уже есть!', ['R1','r1_udatetime']);
  }
