<?php
error_reporting(E_ALL|E_STRICT);

ini_set('mysql.default_user', 'root');
ini_set('mysql.default_password', '');
ini_set('mysql.default_host', 'localhost');
ini_set('mysql.trace_mode', 0);

include('Base.php');
include('Statement.php');
include('Exception.php');

try
{
    $db = Db_Mysql_Base::getInstance();

    $db->setCurrentDb('test');

    $db->query('DROP TABLE IF EXISTS `test`');

    $db->query('CREATE TABLE test (
    id int unsigned not null primary key auto_increment,
    first_name varchar(255),
    last_name varchar(255),
    text text
    )');

     $db->query('INSERT INTO test VALUES (NULL, "?s", "?s", "?s"), (NULL, "?s", "?s", "?s")',
                'Вася', 'Пупкин', '?i - это такой маркер', 'Маша', 'Иванова', 'лялялял лялляя ллялялял');


    echo 'Вставлено в базу: '.$db->getAffectedRows().' записи <br><br>';

    // Выбор записи маркеру числа - ?i
    // Выбираем по невалидному ID из GET запроса
    $_GET['id'] = '2 + мусор';

    echo $db->query('SELECT first_name FROM test WHERE id = ?i', $_GET['id'])->getOne().'<br>'; // Маша
    // Печатаем выполненный SQL-запрос
    echo $db->getQueryString().'<br><br>';

    $str = 'символ \ называется "backslash", а символ _ называется "underscore"';

    // Выборка по маркеру строк - ?s
    echo $db->query('SELECT "?s"', $str)->getOne().'<br>'; // символ \ называется "backslash", а символ _ называется "underscore"
    // Печатаем выполненный SQL-запрос
    echo $db->getQueryString().'<br><br>';

    // Выборка по маркеру LIKE поиска - ?S
    echo $db->query('SELECT * FROM test WHERE text LIKE "?S"', $str)->getOne().'<br>';
    // Печатаем выполненный SQL-запрос
    echo $db->getQueryString().'<br><br>';

    // Передать хэш и вставит значения в соответствующие поля.
    $array = array('first_name' => 'хрен', 'last_name' => 'с горы', 'text' => $str);
    $db->query('INSERT INTO test SET ?a', $array);
    // Печатаем выполненный SQL-запрос
    echo $db->getQueryString().'<br><br>';

    // Передать NULL в качестве значения
    $db->query('INSERT INTO test VALUES (?n, ?n, ?n, ?n)', NULL, NULL, NULL, NULL);
    // Печатаем выполненный SQL-запрос
    echo $db->getQueryString().'<br><br>';

    // Экранирование спецсимволов.
    // Ищем в таблице `test` поле `text` содержащее подстроку `?i`
    // Для этого `экранируем` символ ?, удваивая его:
    echo $db->query('SELECT `text` FROM `test` WHERE `text` LIKE "??i%"')->getOne().'<br>';  // ?i - это такой маркер
    // Печатаем выполненный SQL-запрос
    echo $db->getQueryString().'<br><br>';

    // Получаем все запросы текущего соединения:
    print_r($db->getQueries());
}
catch (Db_Mysql_Exception $e)
{
    echo $e;
}
?>