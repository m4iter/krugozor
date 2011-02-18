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
                '����', '������', '?i - ��� ����� ������', '����', '�������', '������� ������ ��������');


    echo '��������� � ����: '.$db->getAffectedRows().' ������ <br><br>';

    // ����� ������ ������� ����� - ?i
    // �������� �� ����������� ID �� GET �������
    $_GET['id'] = '2 + �����';

    echo $db->query('SELECT first_name FROM test WHERE id = ?i', $_GET['id'])->getOne().'<br>'; // ����
    // �������� ����������� SQL-������
    echo $db->getQueryString().'<br><br>';

    $str = '������ \ ���������� "backslash", � ������ _ ���������� "underscore"';

    // ������� �� ������� ����� - ?s
    echo $db->query('SELECT "?s"', $str)->getOne().'<br>'; // ������ \ ���������� "backslash", � ������ _ ���������� "underscore"
    // �������� ����������� SQL-������
    echo $db->getQueryString().'<br><br>';

    // ������� �� ������� LIKE ������ - ?S
    echo $db->query('SELECT * FROM test WHERE text LIKE "?S"', $str)->getOne().'<br>';
    // �������� ����������� SQL-������
    echo $db->getQueryString().'<br><br>';

    // �������� ��� � ������� �������� � ��������������� ����.
    $array = array('first_name' => '����', 'last_name' => '� ����', 'text' => $str);
    $db->query('INSERT INTO test SET ?a', $array);
    // �������� ����������� SQL-������
    echo $db->getQueryString().'<br><br>';

    // �������� NULL � �������� ��������
    $db->query('INSERT INTO test VALUES (?n, ?n, ?n, ?n)', NULL, NULL, NULL, NULL);
    // �������� ����������� SQL-������
    echo $db->getQueryString().'<br><br>';

    // ������������� ������������.
    // ���� � ������� `test` ���� `text` ���������� ��������� `?i`
    // ��� ����� `����������` ������ ?, �������� ���:
    echo $db->query('SELECT `text` FROM `test` WHERE `text` LIKE "??i%"')->getOne().'<br>';  // ?i - ��� ����� ������
    // �������� ����������� SQL-������
    echo $db->getQueryString().'<br><br>';

    // �������� ��� ������� �������� ����������:
    print_r($db->getQueries());
}
catch (Db_Mysql_Exception $e)
{
    echo $e;
}
?>