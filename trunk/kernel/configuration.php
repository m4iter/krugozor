<?php
// Program use php-modules: mysql, filter, iconv, dom, gd, spl, SimpleXML

if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(0);
}

function getmicrotime()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = getmicrotime();

//setlocale(LC_ALL, 'ru_RU.CP1251');
date_default_timezone_set('Europe/Moscow');

define('DOCUMENT_ROOT', dirname(dirname(__FILE__)));

ini_set('error_log', DOCUMENT_ROOT.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'php_error_log.txt');

function __autoload($class_name)
{
    $realparh = DOCUMENT_ROOT.DIRECTORY_SEPARATOR.
                'kernel'.DIRECTORY_SEPARATOR.
                str_replace('_', DIRECTORY_SEPARATOR, $class_name).'.php';

    if (file_exists($realparh)) {
        require($realparh);
    }
    else {
        throw new RuntimeException('Не найден подключаемый файл по адресу: '.$realparh);
    }
}

$Base_Registry = Base_Registry::getInstance();

if ('adverts' == $_SERVER['HTTP_HOST'] || 'www.adverts' == $_SERVER['HTTP_HOST'])
{
    ini_set('mysql.default_user', 'root');
    ini_set('mysql.default_password', '');
    ini_set('mysql.default_host', 'localhost');
    ini_set('mysql.trace_mode', 0);
    define('MYSQL_DEFAULT_DB', 'adverts');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    $Base_Registry['config']['enabled_debug_info'] = 1;
}
else
{
    ini_set('mysql.default_user', 'nameofruss_adver');
    ini_set('mysql.default_password', 'adver-ts1234');
    ini_set('mysql.default_host', 'localhost');
    ini_set('mysql.trace_mode', 0);
    define('MYSQL_DEFAULT_DB', 'nameofruss_adver');

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    $Base_Registry['config']['enabled_debug_info'] = 0;
}

// Соль, применяемая при хэшировании пароля для COOKIE.
$Base_Registry['config']['user_cookie_salt'] = '4357435felwfew455km';

// Язык по умолчанию. Применяется для выбора i18n и т.д.
$Base_Registry['config']['lang'] = 'ru';

// От кого отсылаем noreply письма.
$Base_Registry['config']['robot_email_adress'] = 'noreply@adverts.ru';

// Директория со шрифтами для captcha.
$Base_Registry['path']['font'] = DOCUMENT_ROOT.DIRECTORY_SEPARATOR.
                                 'etc'.DIRECTORY_SEPARATOR.
                                 'fonts'.DIRECTORY_SEPARATOR;

// Пути к статическим элементам сайта (js, css и т.д.)
$Base_Registry['path']['http']['index'] = '/http/';
$Base_Registry['path']['http']['js'] = '/http/js/';
$Base_Registry['path']['http']['css'] = '/http/css/';

$Base_Registry['path']['http']['image']['index'] = '/http/image/';
$Base_Registry['path']['http']['image']['system']['icon'] = '/http/image/system/icon/';
$Base_Registry['path']['http']['image']['desing']['icon'] = '/http/image/desing/icon/';

$db = Db_Mysql_Base::getInstance();
$db->setCurrentDb(MYSQL_DEFAULT_DB);

$Base_Registry['objects']['db'] = $db; unset($db);

/**
 * Print_r с форматированием.
 *
 * @param $in переменная
 * @param $f выходить ли из программы.
 */
function pr($in, $exit=0)
{
    echo '<pre style="font-size:110%">'. htmlspecialchars(print_r($in, 1), 3)."</pre>";
    if($exit) exit;
}

/**
 * var_dump с форматированием.
 *
 * @param $in переменная
 * @param $f выходить ли из программы.
 */
function v($in, $exit=0)
{
    echo '<pre style="font-size:110%">'. htmlspecialchars(var_dump($in), 3)."</pre>";
    if($exit) exit;
}