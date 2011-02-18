<?php
class Db_Mysql_Base
{
    /**
     * Рузультат SQL-операции в виде ресурса.
     *
     * @var resource
     */
    private $result = null;

    /**
     * Текущая БД.
     *
     * @var string
     */
    private $current_base = null;

    /**
     * Ссылка соединения с БД.
     *
     * @var resource
     */
    private $lnk = null;

    /**
     * Строка SQL-запроса.
     *
     * @var string
     */
    private $query = null;

    /**
     * Массив со всеми запросами.
     * Предназначен для вывода в процессе отладки.
     *
     * @var array
     */
    private static $queries = array();

    /**
     * Массив имен полей запрошенной таблицы.
     *
     * @var array
     */
    private static $list_fields = array();

    public static function create($server, $username, $password)
    {
        $class_name = __CLASS__;
        return new $class_name($server, $username, $password);
    }

    /**
     * @param string $server
     * @param string $username
     * @param string $password
     * @return void
     */
    public function __construct($server, $username, $password)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Метод устанавливает соеденение с базой данных MySQL.
     *
     * @param void
     * @return void
     */
    public function connect()
    {
        if (!is_resource($this->lnk))
        {
            if (!$this->lnk = @mysql_connect($this->server, $this->username, $this->password))
            {
                throw new Db_Mysql_Exception('Ошибка подключения к СУБД.');
            }

            mysql_set_charset('cp1251', $this->lnk);

            // $this->query("set character_set_client='cp1251'");
            // $this->query("set character_set_results='cp1251'");
            // $this->query("set collation_connection='cp1251'");
        }
    }

    /**
     * @see mysql_set_charset
     * @param string $charset
     * @return Db_Mysql_Base
     */
    public function setCharset($charset)
    {
        mysql_set_charset($charset, $this->lnk);

        return $this;
    }

    /**
     * @see mysql_client_encoding
     * @param void
     * @return string
     */
    public function getCharset()
    {
        return mysql_client_encoding($this->lnk);
    }

    /**
     * Устанавливает новую БД $database_name.
     *
     * @param string имя базы данных
     * @return void
     */
    public function setDatabase($database_name)
    {
        if (!is_resource($this->lnk))
        {
            $this->connect();
        }

        $this->current_base = $database_name;

        if (!mysql_select_db($this->current_base, $this->lnk))
        {
            throw new Db_Mysql_Exception('Невозможно выбрать базу данных ' . $database_name);
        }

        return $this;
    }

    /**
     * Возвращает имя текущей БД.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getCurrentDb()
    {
        return $this->current_base;
    }

    /**
    * Выполняет SQL-запрос.
    *
    * Результатом будет либо возвращённое булево значение,
    * либо объект класса {@link DB_MysqlStatement()}.
    *
    * Метод принимает обязательный параметр - SQL-запрос и, в случае наличия,
    * любое количество аргументов - значения placeholder.
    * Метод использует технологию placeholders - для вставки данных в строку
    * SQL-запроса используются специальные маркеры, а сами данные передаются "позже".
    * Данные, прошедшие через систему placeholders,
    * экранируются специальными функциями экранирования,
    * в зависимости от типа сравнения (см. далее). Т.е. вам нет
    * необходимости заключать переменные в функции
    * экранирования типа {@link mysql_escape_string()}.
    *
    * Существует несколько видов маркеров:
    * ?s и ? - маркер типа string, данные экранируются как строка
    * ?S - маркер типа string, данные экранируются как строка для подстановки в LIKE
    * ?i - маркер типа int, данные приводятся к типу int
    * ?a - маркер хеш-массива.
    *
    * @access public
    * @param string строка SQL-запроса
    * @param mixed подставляемые значения для placeholder-ов.
    * @return mixed Возвращает либо bool, либо объект класса {@link DB_MysqlStatement()}.
    */
    public function query()
    {
        $this->connect();

        // Если аргументов в функцию, не переданно, возвращаем FALSE.
        if (!$c = func_num_args()) {
            return false;
        }

        $arg_list = func_get_args();

        // Ссылка на запрос.
        $q = &$arg_list[0];
        // Экранируем символ, т.к. этого требует sprintf
        $q = str_replace('%', '%%', $q);

        // Массмв, в который будем записывать placeholder's
        // в порядке появления их в строке.
        $ph = array();

        // Новый запрос, после обработки нижестоящим кодом.
        $nq = '';

        $strlen = strlen($q);

        // Парсим запрос, по символам.
        for ($i=0; $i<$strlen; $i++)
        {
            // Обнаруживаем метку-заполнитель `?`
            if ($q[$i] === '?')
            {
                if ($q[$i+1] === '?')
                {
                    $nq .= $q[$i];
                }
                else if ($q[$i+1] === 's' && isset($q[$i+1]) && $q[$i+1] !== '?')
                {
                    $nq .= '%s';
                    $ph[] = '?s';
                }
                else if ($q[$i+1] === 'S' && isset($q[$i+1]) && $q[$i+1] !== '?')
                {
                    $nq .= '%s';
                    $ph[] = '?S';
                }
                else if ($q[$i+1] === 'i' && isset($q[$i+1]) && $q[$i+1] !== '?')
                {
                    $nq .= '%s';
                    $ph[] = '?i';
                }
                else if ($q[$i+1] === 'a' && isset($q[$i+1]) && $q[$i+1] !== '?')
                {
                    $nq .= '?a';
                    $ph[] = '?a';
                }
                else if ($q[$i+1] === 'n' && isset($q[$i+1]) && $q[$i+1] !== '?')
                {
                    $nq .= '%s';
                    $ph[] = '?n';
                }

                $i++;
            }
            // это обычный символ.
            else {
                $nq .= $q[$i];
            }
        }

        // SQL-запрос
        $q = $nq; // тут в действительности присваиваем значение элементу $arg_list[0]

        // Смотрим, объявленны ли маркеры массива
        while (($index = array_search('?a', $ph)) !== FALSE)
        {
            $index += 1;

            // Создаем строку SQL-запроса и добавляем в аргументы значения из массива
            if (is_array($arg_list[$index]))
            {
                $array_keys = array_keys($arg_list[$index]);
                $array_values = array_values($arg_list[$index]);
                unset($arg_list[$index]);
                $arg_list = self::array_push_before($arg_list, $array_values, $index);
                $sql_array = '';

                foreach ($array_keys as $value)
                {
                    $sql_array .= " `$value` = \"%s\",";
                    $pls[] = '?s';
                }

                $sql_array = trim($sql_array, ',');

                unset($ph[$index-1]);
                $ph = self::array_push_before($ph, $pls, $index-1);
                $q = self::str_replace_once('?a', $sql_array, $q);
            }
        }

        $w = 0;
        foreach ($arg_list as $k => $v)
        {
            // k = 0 - это всегда SQL-запрос, поэтому эскейпить его не нужно!
            if ($k === 0) {
                continue;
            }

            // Если значение пусто, то никаких escape не делаем.
            if ($v === '') {
                $w++; // добавил 25 мая
                continue;
            }

            if ($ph[$w] === '?s') {
                $arg_list[$k] = mysql_real_escape_string($v, $this->lnk);
            }
            else if ($ph[$w] === '?S') {
                $arg_list[$k] = $this->escape_like($v);
            }
            else if ($ph[$w] === '?i') {
                $arg_list[$k] = filter_var($v, FILTER_SANITIZE_NUMBER_INT);
            }
            else if ($ph[$w] === '?n')
            {
                if ($v !== null)
                {
                    throw new Exception('Попытка записать NULL в базу при значении '.print_r($v, true));
                }

                $arg_list[$k] = 'NULL';
            }
            $w++;
        }

        // Формируем строку SQL-запроса.
        $this->query = @call_user_func_array('sprintf', $arg_list);

        if (FALSE === $this->query)
        {
            $err = error_get_last();
            throw new Db_Mysql_Exception('Ошибка парсера sprintf: '.$err['message'], '', print_r($arg_list, true));
        }

        $this->result = mysql_query($this->query, $this->lnk);

        self::$queries[] = $this->query;

        if (!$this->result)
        {
            throw new Db_Mysql_Exception('Ошибка в запросе: ' . mysql_error());
        }

        if (is_resource($this->result))
        {
            return new Db_Mysql_Statement($this->result);
        }

        return $this->result;
    }

    /**
    * Возвращает id, сгенерированный предыдущей операцией INSERT.
    *
    * @access public
    * @param void
    * @return int
    */
    public function getInsertId()
    {
        return mysql_insert_id($this->lnk);
    }

    /*
    * Получает количество рядов,
    * задействованных в предыдущей MySQL-операции.
    * Возвращает количество рядов,
    * задействованных в последнем запросе INSERT, UPDATE или DELETE.
    * Если последним запросом был DELETE без оператора WHERE,
    * все записи таблицы будут удалены, но функция возвратит ноль.
    *
    * @access public
    * @param void
    * @return int
    */
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->lnk);
    }

    /**
    * Возвращает экранированную строку для placeholder-а поиска LIKE.
    * Данный метод вызывается методом {@link query()} для экранирования переменных,
    * которые используются в SQL-операторе поиска LIKE (см. описание метода {@link query()}).
    * Описание замены переменых для LIKE-поиска см. http://phpfaq.ru/slashes#prepared
    *
    * @access public
    * @param string $var строка в которой необходимо экранировать спец. символы
    * @param string $chars набор символов, которые так же необходимо экранировать.
    * По умолчанию экранируются следующие символы: "'"%_"
    * @return string
    */
    public function escape_like($var, $chars = "%_")
    {
        $var = str_replace('\\','\\\\',$var);
        $var = mysql_real_escape_string($var, $this->lnk);

        if ($chars) {
            $var = addCslashes($var, $chars);
        }

        return $var;
    }

    /**
    * Закрывает MySQL-соединение.
    *
    * @access private
    * @param void
    * @return void
    */
    private function close()
    {
        if (is_resource($this->lnk)) {
            @mysql_close($this->lnk);
        }
    }

    /**
    * Деструктор, вызывающий $this->close()
    *
    * @access public
    * @param void
    * @return void
    */
    public function __destruct()
    {
        $this->close();
    }

    /**
    * Возвращает MySQL-запрос.
    * Возвращает строковой параметр -
    * последний выполненный MySQL-запрос.
    *
    * @access public
    * @param void
    * @return string последний выполненный MySQL-запрос
    */
    public function getQueryString()
    {
        return $this->query;
    }

    /**
    * Возвращает массив $this->querys
    *
    * @access public
    * @param void
    * @return array
    */
    public function getQueries()
    {
        return self::$queries;
    }

    /**
    * Возвращает id, сгенерированный предыдущей операцией INSERT.
    *
    * @access public
    * @param void
    * @return int
    */
    public function getLastInsertId()
    {
        return mysql_insert_id($this->lnk);
    }

    /**
     * Возвращает массив свойств таблицы $table.
     *
     * @access public
     * @param string имя таблицы
     * @return array
     */
    public function getListFields($table)
    {
        if (!isset(self::$list_fields[$table]))
        {
            $fields = mysql_list_fields($this->current_base, $table, $this->lnk);
            $columns = mysql_num_fields($fields);

            for ($i = 0; $i < $columns; $i++)
            {
                $obj = mysql_fetch_field($fields, $i);

                self::$list_fields[$table][$obj->name] = $obj;
            }
        }

        return self::$list_fields[$table];
    }

    /**
     * @param array $src
     * @param array $in
     * @param int|string $pos
     * @return array
    */
    protected static function array_push_before($src,$in,$pos)
    {
        if (is_int($pos)) $R=array_merge(array_slice($src,0,$pos), $in, array_slice($src,$pos));
        else {
            foreach($src as $k=>$v){
                if($k==$pos)$R=array_merge($R,$in);
                $R[$k]=$v;
            }
        }

        return $R;
    }

    /**
    * Аналог функци str_replace, но с единичной заменой подстроки.
    */
    protected static function str_replace_once($search, $replace, $subject) {
        $firstChar = strpos($subject, $search);
        if($firstChar !== false) {
            $beforeStr = substr($subject,0,$firstChar);
            $afterStr = substr($subject, $firstChar + strlen($search));
            return $beforeStr.$replace.$afterStr;
        } else {
            return $subject;
        }
    }
}
?>