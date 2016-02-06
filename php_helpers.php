<?php
////======================================================////
////																										  ////
////              	Библиотека php-хелперов	              ////
////																											////
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

  //-----//
  // r1_ //
  //-----//
  if(!function_exists('r1_')) {
    /**
     *  <h1>Список хелперов пакета R1</h1>
     *  <pre>
     *
     *    write2log                  | Возбудить событие R2\Event с ключём "m2:write2log"
     *    runcommand                 | Провести авторизацию и выполнить команду
     *    r1_get_doc_locale          | Получить локаль документа C,M-пакета
     *    r1_get_doc_layoutid        | Получить ID L-пакета - шаблона по умолчанию для указанного D-пакета
     *    r1_url_exist               | Узнать, существует ли указанный URL
     *    r1_array_unique_recursive  | array_unique для многомерных массивов
     *    r1_udatetime               | Get string repres.of datetime with microseconds
     *    r1_fs                      | Get new FilesystemManager instance
     *    r1_fs2                     | Get new Filesystem instance
     *    r1_config_set              | Set value for specified option of specified config
     *    r1_query                   | Inter-m-packages-save-queries
     *    r1_isJSON                  | Является ли переданная строка валидным JSON
     *    r1_is_schema_exists        | Существует ли указанная база данных
     *    r1_hasTable                | Проверить наличие таблицы в указанной БД
     *    r1_hasColumn               | Проверить наличие столбца в указанной таблице в указанной БД
     *    r1_getColumns              | Получить список имён столбцов из указанной таблицы указанной БД
     *    r1_rel_exists              | Существует ли указанная связь в указанной модели
     *
     *  </pre>
     * @return bool
     */
    function r1_() {

      return true;

    }
  } else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_, поскольку такая уже есть!', ['R1','r1_']);
  }


  //-----------//
  // write2log //
  //-----------//
  if(!function_exists('write2log')) {
    /**
     *  <h1>Описание</h1>
     *  <pre>
     *    Возбудить событие R2\Event с ключём "m2:write2log"
     *    M-пакет M2 перехватит его, и запишет $msg в лог, с тегами $tags
     *    Не вернёт ничего в случае успеха.
     *    Вернёт следующий массив в случае ошибки:
     *
     *      [
     *        "status"  => -2,
     *        "data"    => $errortext  // Текст ошибки
     *      ];
     *
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    write2log("сообщение", ["тег1", "тег2"]);
     *  </pre>
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


  //------------//
  // runcommand //
  //------------//
  if(!function_exists('runcommand')) {
    /**
     *  <h1>Описание</h1>
     *  <pre>
     *    Провести авторизацию и выполнить команду
     *  </pre>
     *  <h1>Что возвращает</h1>
     *  <pre>
     *    - JSON-массив:
     *
     *      [
     *        "status"        // Статус результата выполнения команды
     *        "timestamp"     // Timestamp прихода запроса от клиента
     *        "data"          // Данные
     *      ]
     *  </pre>
     *  <h1>Какие статусы бывают, зависимость data от статуса</h1>
     *  <pre>
     *    0   // Команда выполнена успешно. В data результаты её выполненя.
     *    -1  // Нет доступа. В data строка-сообщение об этом.
     *    -2  // Команда завершилась с ошибкой. В data текст ошибки.
     *  </pre>
     *  <h1>Примеры использования</h1>
     *  <pre>
     *
     *    1. Синхронное выполнение
     *
     *      1.1. Простой синхронный запуск
     *        runcommand('\M1\Commands\C1_parseapp');
     *
     *      1.2. С передачей данных
     *        runcommand('\M1\Commands\C1_parseapp', ['key1'=>'value1','key2'=>'value2']);
     *
     *      1.3. С авторизацией по ID текущего пользователя (ID == "15" в примере)
     *        runcommand('\M1\Commands\C1_parseapp', [], 15);
     *
     *    2. Добавление в очередь задач
     *
     *      2.1. Без дополнительной отсрочки выполнения
     *      runcommand('\M1\Commands\C1_parseapp', [], "", ['on'=>true, 'delaysecs'=>'']);
     *
     *      2.2. С дополнительной отсрочкой выполнения в 10 секунд
     *      runcommand('\M1\Commands\C1_parseapp', [], "", ['on'=>true, 'delaysecs'=>'10']);
     *
     *  </pre>
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
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить установленную локаль M,D,L,W-пакета
     *  </pre>
     *  <h1>Правила вычисления локали пакета</h1>
     *  <pre>
     *
     *    1. Если нет опубликованного конфига пакета
     *      - В этом случае применяется локаль "RU".
     *
     *    2. Если
     *        - Есть опубликованный конфиг.
     *        - В нём есть параметры $locales и $locale.
     *        - $locales является массивом, а $locale строкой.
     *        - $locale есть в $locales.
     *       То
     *        - Возвращается локаль $locale.
     *
     *    3. Если
     *        - Есть опубликованный конфиг.
     *        - $locales нет, или это не массив строк.
     *        - Возвращается локаль "RU".
     *
     *    4. Если
     *        - Есть опубликованный конфиг.
     *        - $locales есть, и это не пустой массив строк.
     *        - $locale нет, или это пустая строка.
     *        - $applocale есть, и это строка.
     *        - $applocale есть в $locales.
     *       То:
     *        - Возвращается локаль $applocale.
     *
     *    5. Если
     *        - Есть опубликованный конфиг.
     *        - $locales есть, и это не пустой массив строк.
     *        - $locale нет, или это пустая строка.
     *        - $applocale есть, и это строка.
     *        - $applocale нет в $locales.
     *       То:
     *        - Возвращается локаль $locales[0].
     *
     *    6. Если ни одно из предыдущих не сработало
     *      - Возвращается локаль "RU".
     *
     *  </pre>
     *  <h1>Возвращает</h1>
     *  <pre>
     *    В случае успеха возвращает найденную локаль.
     *    В случае ошибки возвращает локаль "RU" и пишет сообщение в лог.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Получить локаль пакета M1:
     *    r1_get_doc_locale("M1")
     *  </pre>
     *
		 * Получить установленную локаль M,D,L,W-пакета
     *
     * @param  string $packid
     *
		 * @return array
		 */
		function r1_get_doc_locale($packid)
    { try {

      // 1] Провести валидацию значения $packid
      if(preg_match("/^[MDLW]{1}[1-9]{1}[0-9]*$/ui", $packid) == 0)
        throw new \Exception('Параметр packid не является валидным ID M,D,L,W-пакета');

      // 2] Подготовить переменную для локали
      $locale = '';

      // 3] Вернуть локаль

        // 3.1] Проверить наличие опубликованного конфига пакета $packid
        if(!file_exists(base_path('config/'.mb_strtoupper($packid).'.php')))
          throw new \Exception('Конфиг пакета '.$packid.' не найден в каталоге config.');

        // 3.2]
      



//        // 3.1] Проверить наличие опубликованного конфига пакета $packid
//        if(!file_exists(base_path('config/'.mb_strtoupper($packid).'.php')))
//          throw new \Exception('Конфиг пакета '.$packid.' не найден в каталоге config.');
//
//        // 3.2] Получить значения параметров locales, locale и applocale
//        $locales    = config(mb_strtoupper($packid).'.locales');
//        $locale     = config(mb_strtoupper($packid).'.locale');
//        $applocale  = config('app.locale');
//
//        // 3.3] Если $locales нет, или это не массив, или она пуста
//        // - Вернуть локаль "RU"
//        if(is_null($locales) || !is_array($locales) || empty($locales)) {
//          write2log("Проблема с локалями в пакете $packid - либо конфиг не опубликован, либо параметр locales пуст или не массив.",['r1_get_doc_locale']);
//          return [
//            "status"  => 0,
//            "data"    => "RU"
//          ];
//        }
//
//        // 3.4] Если $applocale не пуста и строка, $locale NULL или пуста
//        // - А $applocale в $locales: вернуть $applocale
//        // - А $applocale не в $locales: вернуть 1-ю локаль из $locales
//        if(!empty($applocale) && is_string($applocale) && empty($locale)) {
//          return [
//            "status"  => 0,
//            "data"    => in_array(mb_strtolower($applocale), array_map('strtolower', $locales)) ? $applocale : (array_key_exists(0, $locales) && is_string($locales[0])) ? $locales[0] : "RU"
//          ];
//        }
//
//        // 3.5] Вернуть локаль "RU"
//        return [
//          "status"  => 0,
//          "data"    => in_array($applocale, $locales) ? $applocale : (array_key_exists(0, $locales) && is_string($locales[0])) ? $locales[0] : "RU"
//        ];

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_get_doc_locale: '.$e->getMessage(), ['r1_get_doc_locale']);
      return "RU";
    }}
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


  //-------//
  // r1_fs //
  //-------//
	if(!function_exists('r1_fs')) {
		/**
		 * Get new FilesystemManager instance
     *
     * @param  string $path
     *
		 * @return object
		 */
    function r1_fs($path)
    {

      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path($path)]);
      return new \Illuminate\Filesystem\FilesystemManager(app());

    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_fs, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_fs, поскольку такая уже есть!', ['R1','r1_fs']);
  }


  //--------//
  // r1_fs2 //
  //--------//
	if(!function_exists('r1_fs2')) {
		/**
		 * Get new Filesystem instance
     *
		 * @return object
		 */
    function r1_fs2()
    {

      return new \Illuminate\Filesystem\Filesystem();

    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_fs2, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_fs2, поскольку такая уже есть!', ['R1','r1_fs2']);
  }


  //-------------//
  // r1_countdim //
  //-------------//
	if(!function_exists('r1_countdim')) {
		/**
		 * Get new Filesystem instance
     *
     * @param  string $array
     *
		 * @return object
		 */
    function r1_countdim($array)
    { try {

      // 1] Если $array не массив, возбудить исключение
      if(!is_array($array))
        throw new \Exception('Параметр array не является массивом');

      // 2] Подготовить переменную для результата
      $result = 1;

      // 3] Если длина массива $array == 0, вернуть $result
      if(count($array) == 0) return $result;

      // 4] Написать рекурсивную функцию для прощупывания глубины
      $recur = function($item, $depth = 0) USE (&$recur) {

        // 4.1] Если $item не массив, вернуть 0
        if(!is_array($item)) return 0;

        // 4.2] Если $item это пустой массив
        if(count($item) == 0) return $depth;

        // 4.3] Если же $item это массив
        $results = [];
        foreach($item as $elem) {

          // 4.3.1] Если $elem не массив
          if(!is_array($elem)) array_push($results, +$depth);

          // 4.3.2] Если $elem массив
          else array_push($results, $recur($elem, +$depth+1));

        }

        // 4.4] Вернуть максимальное из $results
        return max($results);

      };

      // 5] Вернуть результат
      return +$result + +$recur($array);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_countdim: '.$e->getMessage(), ['r1_countdim']);
      return 0;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_countdim, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_countdim, поскольку такая уже есть!', ['R1','r1_countdim']);
  }


  //---------------//
  // r1_config_set //
  //---------------//
	if(!function_exists('r1_config_set')) {
		/**
		 * Set value for specified option of specified config
     *
     * @param  string $option
     * @param  string $value2set
     *
		 * @return string
		 */
    function r1_config_set($option, $value2set)
    { try {

      // 1. Создать новую FS относительно каталога config
      $fs = r1_fs('config');

      // 2. Разобрать $option на составляющие
      //
      //  $option_arr[0]   | имя конфига
      //  $option_arr[1]   | имя опции в конфиге
      //  $option_arr[2..] | для опций-массивов, имена ключей массивов
      //
      $option_arr = explode('.',$option);
      if(!array_key_exists(0,$option_arr) || !array_key_exists(1,$option_arr))
        throw new \Exception('Значение параметра option не соответствует формату. Пример правильного значения: "M5.common_ison"');

      // 3. Получить значение опции
      $value = config($option_arr[0].'.'.$option_arr[1]);

      // 4. Узнать тип значения опции
      $type = call_user_func(function() USE ($value) {

        switch (gettype($value)) {
          case 'boolean':       return 'boolean';
          case 'integer':       return 'integer';
          case 'double':        return 'double';
          case 'string':        return 'string';
          case 'array':         return 'array';
          case 'object':        return 'object';
          case 'resource':      return 'resource';
          case 'NULL':          return 'NULL';
          case 'unknown type':  return 'unknown type';
          default:              return 'unknown type';
        }

      });
      if(in_array($type, ['object', 'resource', 'NULL', 'unknown type']))
        throw new \Exception("Хелпер r1_config_set не работает со значениями следующих типов: 'object', 'resource', 'NULL', 'unknown type'");

      // 5. Узнать тип значения $value2set
      $type2set = call_user_func(function() USE ($value2set) {

        switch (gettype($value2set)) {
          case 'boolean':       return 'boolean';
          case 'integer':       return 'integer';
          case 'double':        return 'double';
          case 'string':        return 'string';
          case 'array':         return 'array';
          case 'object':        return 'object';
          case 'resource':      return 'resource';
          case 'NULL':          return 'NULL';
          case 'unknown type':  return 'unknown type';
          default:              return 'unknown type';
        }

      });
      if(in_array($type2set, ['object', 'resource', 'NULL', 'unknown type']))
        throw new \Exception("Хелпер r1_config_set не работает со значениями следующих типов: 'object', 'resource', 'NULL', 'unknown type'");
      if($type != 'array' && $type !==  $type2set)
        throw new \Exception('Тип назначаемого значения должен соответствовать типу опции: '.$type);

      // 6. Если $type относится к тем, что умещаются на 1-й строке
      if(in_array($type, ['boolean', 'integer', 'double', 'string'])) {

        // 6.1. Преобразовать $value2set к его строковому эквиваленту
        switch (gettype($value2set)) {
          case 'boolean':  $value2set = $value2set ? 'true' : 'false'; break;
          case 'integer':  $value2set = ''.$value2set; break;
          case 'double':   $value2set = ''.$value2set; break;
          case 'string':   break;
        }

        // 6.2. Извлечь содержимое конфига $option_arr[0]
        $config = r1_fs('config')->get($option_arr[0].'.php');

        // 6.3. Найти и заменить в конфиге значение опции $option_arr[1]
        if(in_array($type, ['boolean', 'integer', 'double']))
          $config = preg_replace("/'".$option_arr[1]."' *=> *.*/ui", "'".$option_arr[1]."' => ".$value2set.",", $config);
        else
          $config = preg_replace("/'".$option_arr[1]."' *=> *.*/ui", "'".$option_arr[1]."' => '".$value2set."',", $config);

        // 6.4. Перезаписать $config
        r1_fs('config')->put($option_arr[0].'.php', $config);

      }

      // 7. Если $type - массив
      if(in_array($type, ['array'])) {

        // 7.1. Извлечь значение, которое надо изменить, и его тип
        $value2change = call_user_func(function() USE ($value, $type, $option_arr, $type2set) {

          // 1] Если в $option_arr лишь 2 элемента, вернуть $value и $type
          if(count($option_arr) == 2) return [
            "value" => $value,
            "type"  => $type
          ];

          // 2] Проверить существование искомого значения
          $arrpath = "";
          for($i=2; $i<count($option_arr); $i++) {

            // 2.1] Дополнить $arrpath
            $arrpath = $arrpath . '.' . $option_arr[$i];

            // 2.2] Проверить существование такого св-ва массива
            if(is_null(config($option_arr[0].'.'.$option_arr[1].$arrpath)))
              throw new \Exception('Попытка изменить значение несуществующего в опции-массиве свойства: '.$arrpath);

          }

          // 3] Получить искомое значение
          $res_value = config($option_arr[0].'.'.$option_arr[1].$arrpath);

          // 4] Получить тип искомого значения
          $res_type = call_user_func(function() USE ($res_value) {

            switch (gettype($res_value)) {
              case 'boolean':       return 'boolean';
              case 'integer':       return 'integer';
              case 'double':        return 'double';
              case 'string':        return 'string';
              case 'array':         return 'array';
              case 'object':        return 'object';
              case 'resource':      return 'resource';
              case 'NULL':          return 'NULL';
              case 'unknown type':  return 'unknown type';
              default:              return 'unknown type';
            }

          });
          if(in_array($res_type, ['object', 'resource', 'NULL', 'unknown type']))
            throw new \Exception("Хелпер r1_config_set не работает со значениями следующих типов: 'object', 'resource', 'NULL', 'unknown type'");
          if($type2set !== $res_type)
            throw new \Exception('Тип назначаемого значения должен соответствовать типу опции: '.$res_type);

          // 5] Вернуть результат
          return [
            "value" => $res_value,
            "type"  => $res_type
          ];

        });

        // 7.2. Узнать кол-во измерений массива config($option_arr[0].'.'.$option_arr[1].$arrpath
        $dimensions = r1_countdim(config($option_arr[0].'.'.$option_arr[1]));

        // 7.3. Если $value2change['type'] относится к следующим
        if(in_array($value2change['type'], ['boolean', 'integer', 'double', 'string', 'array'])) {

          // 1] Преобразовать $value2set к его строковому эквиваленту
          switch (gettype($value2set)) {
            case 'boolean':  $value2set = $value2set ? 'true' : 'false'; break;
            case 'integer':  $value2set = ''.$value2set; break;
            case 'double':   $value2set = ''.$value2set; break;
            case 'string':   break;
          }

          // 2] Извлечь содержимое конфига $option_arr[0]
          $config = r1_fs('config')->get($option_arr[0].'.php');

          // 3] Напис.функц.для получения ссылки на св-во многомерного массива
          $get_multidem_prop = function &(&$array, $keys){
            $result = &$array;
            foreach($keys as &$key) {
              $result = &$result[$key];
            }
            return $result;
          };

          // 4] Изменить значение $value
          $keys = [];
          for($i=2; $i<count($option_arr); $i++) {
            array_push($keys, $option_arr[$i]);
          }
          $newvalue = &$get_multidem_prop($value, $keys);
          $newvalue = $value2set;

          // 5] Сформировать строку для замены $value

            // 5.1] Создать новый экземпляр энкодера
            $encoder = new \Riimu\Kit\PHPEncoder\PHPEncoder();

            // 5.2] Закодировать $value
            $value = $encoder->encode($value, ['array.indent' => 2, 'array.base' => 4]);

          // 6] Найти и заменить $value в $config

            // 6.1] Сформировать регулярное выражение
            $regex = "/'" . $option_arr[1] . "' *=> *\[.*\]";

            // 6.2] Учесть кол-во измерений в массиве
            if(+$dimensions >= 2) {
              for($i=0; $i<count($dimensions) + 1; $i++) {
                $regex = $regex . '.*\]';
              }
            }

            // 6.3] Завершить регулярное выражение
            $regex = $regex . "/smuiU";

            // 6.4] Заменить
            $config = preg_replace($regex, "'".$option_arr[1]."' => ".$value, $config);

          // 7] Перезаписать $config
          r1_fs('config')->put($option_arr[0].'.php', $config);

        }

      }

      // n] Вернуть ответ
      return 1;

    }
    catch(\Exception $e) {
      write2log('При попытке назначить новое значение опции M5.common_ison возникла ошибка: '.$e->getMessage(), ['r1_config_set']);
      return 0;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_config_set, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_config_set, поскольку такая уже есть!', ['R1','r1_config_set']);
  }


  //----------//
  // r1_query //
  //----------//
	if(!function_exists('r1_query')) {
		/**
		 * Inter-m-packages-save-queries
     *
     * @param  string $callback
     *
		 * @return object
		 */
    function r1_query($callback)
    { try {

      $result = call_user_func($callback);
      return $result;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_query: '.$e->getMessage(), ['r1_query']);
      return NULL;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_query, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_query, поскольку такая уже есть!', ['R1','r1_query']);
  }


  //-----------//
  // r1_isJSON //
  //-----------//
	if(!function_exists('r1_isJSON')) {
		/**
		 * Является ли переданная строка валидным JSON
     *
     * @param  string $string
     *
		 * @return object
		 */
    function r1_isJSON($string)
    { try {

      json_decode($string);
      return (json_last_error() == JSON_ERROR_NONE);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_isJSON: '.$e->getMessage(), ['r1_isJSON']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_isJSON, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_isJSON, поскольку такая уже есть!', ['R1','r1_isJSON']);
  }


  //---------------------//
  // r1_is_schema_exists //
  //---------------------//
	if(!function_exists('r1_is_schema_exists')) {
		/**
		 * Существует ли указанная база данных
     *
     * @param  string $schema
     *
		 * @return bool
		 */
    function r1_is_schema_exists($schema)
    { try {

      $check = \DB::SELECT("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$schema."'");
      if(empty($check)) return false;
      return true;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_is_schema_exists: '.$e->getMessage(), ['r1_is_schema_exists']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_is_schema_exists, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_is_schema_exists, поскольку такая уже есть!', ['R1','r1_is_schema_exists']);
  }


  //-------------//
  // r1_hasTable //
  //-------------//
	if(!function_exists('r1_hasTable')) {
		/**
		 * Проверить наличие таблицы в указанной БД
     *
		 * @param  string $db_name
		 * @param  string $table_name
     *
		 * @return bool
		 */
    function r1_hasTable($db_name, $table_name)
    { try {

			// Проверить
			$exists = DB::table('information_schema.tables')
					->where('table_schema','=',$db_name)
					->where('table_name','=',$table_name)
					->first();

			// Вернуть результат
			return !empty($exists);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_hasTable: '.$e->getMessage(), ['r1_hasTable']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_hasTable, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_hasTable, поскольку такая уже есть!', ['R1','r1_hasTable']);
  }


  //--------------//
  // r1_hasColumn //
  //--------------//
	if(!function_exists('r1_hasColumn')) {
		/**
		 * Проверить наличие столбца в указанной таблице в указанной БД
     *
		 * @param  string $db_name
		 * @param  string $table_name
		 * @param  string $column_name
     *
		 * @return bool
		 */
    function r1_hasColumn($db_name, $table_name, $column_name)
    { try {

			// Проверить
			$exists = DB::table('information_schema.columns')
						->where('table_schema','=',$db_name)
						->where('table_name','=',$table_name)
						->where('column_name','=',$column_name)
						->first();

			// Вернуть результат
			return !empty($exists);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_hasColumn: '.$e->getMessage(), ['r1_hasColumn']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_hasColumn, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_hasColumn, поскольку такая уже есть!', ['R1','r1_hasColumn']);
  }


  //---------------//
  // r1_getColumns //
  //---------------//
	if(!function_exists('r1_getColumns')) {
		/**
		 * Получить список имён столбцов из указанной таблицы указанной БД
     *
		 * @param  string $db_name
		 * @param  string $table_name
     *
		 * @return bool
		 */
    function r1_getColumns($db_name, $table_name)
    { try {

      // Получить
      $columns = DB::SELECT("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db_name."' AND TABLE_NAME = '".$table_name."'");

      // Отвильтровать
      $columns = array_map(function($item){
        return $item->COLUMN_NAME;
      }, $columns);

			// Вернуть результат
			return $columns;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_getColumns: '.$e->getMessage(), ['r1_getColumns']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_getColumns, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_getColumns, поскольку такая уже есть!', ['R1','r1_getColumns']);
  }


  //---------------//
  // r1_rel_exists //
  //---------------//
	if(!function_exists('r1_rel_exists')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить список имён столбцов из указанной таблицы указанной БД
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Сущетсвует ли связь "m4_routes" в модели "MD2_packages" в M-пакете "m1":
     *    r1_rel_exists("m1","md2_packages","m4_routes");
     *  </pre>
     *
		 * @param  string $packid
		 * @param  string $model
		 * @param  string $relation
     *
		 * @return bool
		 */
    function r1_rel_exists($packid, $model, $relation)
    { try {

      // 1. Провести валидацию
      $validator = r4_validate(["packid"=>$packid,"model"=>$model,"relation"=>$relation], [

        "packid"      => ["required", "regex:/^M[1-9]{1}[0-9]*$/ui"],
        "model"       => ["required", "regex:/^MD[1-9]{1}[0-9]*_/ui"],
        "relation"    => ["required"]

      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Обработать некоторые аргументы
      $packid = mb_strtoupper($packid);
      $model = preg_replace("/^md/ui", "MD", $model);

      // 3. Вернуть результат
      return method_exists("\\".$packid."\\Models\\".$model, $relation);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_rel_exists: '.$e->getMessage(), ['r1_rel_exists']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_rel_exists, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_rel_exists, поскольку такая уже есть!', ['R1','r1_rel_exists']);
  }

