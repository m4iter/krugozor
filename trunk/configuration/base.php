<?php
// Program use php-modules: mysql, filter, iconv, dom, gd, spl, SimpleXML

/**
 * ����� ������ ������ �������.
 * ������������� ������������� ��� ���������� ����������.
 */
define('TIME_START', microtime(true));

/**
 * ������ �������� ������ - ������!
 */
error_reporting (E_ALL | E_STRICT);

if (get_magic_quotes_runtime())
{
    set_magic_quotes_runtime(0);
}

//setlocale(LC_ALL, 'ru_RU.CP1251');

date_default_timezone_set('Europe/Moscow');

/**
 * DOCUMENT_ROOT �����
 *
 * @var string
 */
define('DOCUMENTROOT_PATH', dirname(dirname(__FILE__)));

/**
 * ���� � ���������� � �����������.
 *
 * @var string
 */
define('FRAMEWORK_PATH', DOCUMENTROOT_PATH . DIRECTORY_SEPARATOR . 'Krugozor');

ini_set('error_log', DOCUMENTROOT_PATH . DIRECTORY_SEPARATOR .
                    'logs' . DIRECTORY_SEPARATOR . 'php_error_log.txt');

/**
 * todo: ������� ����������� ���������, ��� ������ ����� $class_name
 */
function __autoload($class_name)
{
    $realpath = FRAMEWORK_PATH . DIRECTORY_SEPARATOR.
                str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

    if (file_exists($realpath))
    {
        require($realpath);
    }
    else
    {
        throw new RuntimeException('�� ������ ������������ ���� �� ������: ' . $realpath);
    }
}

if ('adverts' == $_SERVER['HTTP_HOST'] || 'www.adverts' == $_SERVER['HTTP_HOST'])
{
    ini_set('mysql.default_user', 'root');
    ini_set('mysql.default_password', '');
    ini_set('mysql.default_host', 'localhost');
    ini_set('mysql.trace_mode', 0);
    define('DATABASE_NAME', 'adverts');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    define('ENABLED_DEBUG_INFO', true);
}
else
{
    ini_set('mysql.default_user', 'nameofruss_adver');
    ini_set('mysql.default_password', 'adver-ts1234');
    ini_set('mysql.default_host', 'localhost');
    ini_set('mysql.trace_mode', 0);
    define('DATABASE_NAME', 'nameofruss_adver');

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    define('ENABLED_DEBUG_INFO', false);
}

$Base_Registry = Base_Registry::getInstance();

// ����, ����������� ��� ����������� ������ ��� COOKIE.
$Base_Registry['config']['user_cookie_salt'] = '4357435felwfew455km';

// ���� �� ���������. ����������� ��� ������ i18n � �.�.
$Base_Registry['config']['lang'] = 'ru';

// �� ���� �������� noreply ������.
$Base_Registry['config']['robot_email_adress'] = 'noreply@adverts.ru';

// ���������� �� �������� ��� captcha.
$Base_Registry['path']['font'] = DOCUMENTROOT_PATH . DIRECTORY_SEPARATOR .
                                 'etc' . DIRECTORY_SEPARATOR .
                                 'fonts' . DIRECTORY_SEPARATOR;


// ������� ������������.
// �� ����� �������, ��� ������������� ������������� ��� ������� �������.

/**
 * Print_r � ���������������.
 *
 * @param $in ����������
 * @param $f �������� �� �� ���������.
 */
function pr($in, $exit=false)
{
    echo '<pre style="font-size:110%">' . htmlspecialchars(print_r($in, 1), 3) . "</pre>";
    if ($exit) exit;
}

/**
 * var_dump � ���������������.
 *
 * @param $in ����������
 * @param $f �������� �� �� ���������.
 */
function v($in, $exit=false)
{
    echo '<pre style="font-size:110%">'; var_dump($in); echo "</pre>"; if ($exit) exit;
}