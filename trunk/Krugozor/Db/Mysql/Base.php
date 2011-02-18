<?php
class Db_Mysql_Base
{
    /**
     * ��������� SQL-�������� � ���� �������.
     *
     * @var resource
     */
    private $result = null;

    /**
     * ������� ��.
     *
     * @var string
     */
    private $current_base = null;

    /**
     * ������ ���������� � ��.
     *
     * @var resource
     */
    private $lnk = null;

    /**
     * ������ SQL-�������.
     *
     * @var string
     */
    private $query = null;

    /**
     * ������ �� ����� ���������.
     * ������������ ��� ������ � �������� �������.
     *
     * @var array
     */
    private static $queries = array();

    /**
     * ������ ���� ����� ����������� �������.
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
     * ����� ������������� ���������� � ����� ������ MySQL.
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
                throw new Db_Mysql_Exception('������ ����������� � ����.');
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
     * ������������� ����� �� $database_name.
     *
     * @param string ��� ���� ������
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
            throw new Db_Mysql_Exception('���������� ������� ���� ������ ' . $database_name);
        }

        return $this;
    }

    /**
     * ���������� ��� ������� ��.
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
    * ��������� SQL-������.
    *
    * ����������� ����� ���� ������������ ������ ��������,
    * ���� ������ ������ {@link DB_MysqlStatement()}.
    *
    * ����� ��������� ������������ �������� - SQL-������ �, � ������ �������,
    * ����� ���������� ���������� - �������� placeholder.
    * ����� ���������� ���������� placeholders - ��� ������� ������ � ������
    * SQL-������� ������������ ����������� �������, � ���� ������ ���������� "�����".
    * ������, ��������� ����� ������� placeholders,
    * ������������ ������������ ��������� �������������,
    * � ����������� �� ���� ��������� (��. �����). �.�. ��� ���
    * ������������� ��������� ���������� � �������
    * ������������� ���� {@link mysql_escape_string()}.
    *
    * ���������� ��������� ����� ��������:
    * ?s � ? - ������ ���� string, ������ ������������ ��� ������
    * ?S - ������ ���� string, ������ ������������ ��� ������ ��� ����������� � LIKE
    * ?i - ������ ���� int, ������ ���������� � ���� int
    * ?a - ������ ���-�������.
    *
    * @access public
    * @param string ������ SQL-�������
    * @param mixed ������������� �������� ��� placeholder-��.
    * @return mixed ���������� ���� bool, ���� ������ ������ {@link DB_MysqlStatement()}.
    */
    public function query()
    {
        $this->connect();

        // ���� ���������� � �������, �� ���������, ���������� FALSE.
        if (!$c = func_num_args()) {
            return false;
        }

        $arg_list = func_get_args();

        // ������ �� ������.
        $q = &$arg_list[0];
        // ���������� ������, �.�. ����� ������� sprintf
        $q = str_replace('%', '%%', $q);

        // ������, � ������� ����� ���������� placeholder's
        // � ������� ��������� �� � ������.
        $ph = array();

        // ����� ������, ����� ��������� ����������� �����.
        $nq = '';

        $strlen = strlen($q);

        // ������ ������, �� ��������.
        for ($i=0; $i<$strlen; $i++)
        {
            // ������������ �����-����������� `?`
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
            // ��� ������� ������.
            else {
                $nq .= $q[$i];
            }
        }

        // SQL-������
        $q = $nq; // ��� � ���������������� ����������� �������� �������� $arg_list[0]

        // �������, ���������� �� ������� �������
        while (($index = array_search('?a', $ph)) !== FALSE)
        {
            $index += 1;

            // ������� ������ SQL-������� � ��������� � ��������� �������� �� �������
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
            // k = 0 - ��� ������ SQL-������, ������� ��������� ��� �� �����!
            if ($k === 0) {
                continue;
            }

            // ���� �������� �����, �� ������� escape �� ������.
            if ($v === '') {
                $w++; // ������� 25 ���
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
                    throw new Exception('������� �������� NULL � ���� ��� �������� '.print_r($v, true));
                }

                $arg_list[$k] = 'NULL';
            }
            $w++;
        }

        // ��������� ������ SQL-�������.
        $this->query = @call_user_func_array('sprintf', $arg_list);

        if (FALSE === $this->query)
        {
            $err = error_get_last();
            throw new Db_Mysql_Exception('������ ������� sprintf: '.$err['message'], '', print_r($arg_list, true));
        }

        $this->result = mysql_query($this->query, $this->lnk);

        self::$queries[] = $this->query;

        if (!$this->result)
        {
            throw new Db_Mysql_Exception('������ � �������: ' . mysql_error());
        }

        if (is_resource($this->result))
        {
            return new Db_Mysql_Statement($this->result);
        }

        return $this->result;
    }

    /**
    * ���������� id, ��������������� ���������� ��������� INSERT.
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
    * �������� ���������� �����,
    * ��������������� � ���������� MySQL-��������.
    * ���������� ���������� �����,
    * ��������������� � ��������� ������� INSERT, UPDATE ��� DELETE.
    * ���� ��������� �������� ��� DELETE ��� ��������� WHERE,
    * ��� ������ ������� ����� �������, �� ������� ��������� ����.
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
    * ���������� �������������� ������ ��� placeholder-� ������ LIKE.
    * ������ ����� ���������� ������� {@link query()} ��� ������������� ����������,
    * ������� ������������ � SQL-��������� ������ LIKE (��. �������� ������ {@link query()}).
    * �������� ������ ��������� ��� LIKE-������ ��. http://phpfaq.ru/slashes#prepared
    *
    * @access public
    * @param string $var ������ � ������� ���������� ������������ ����. �������
    * @param string $chars ����� ��������, ������� ��� �� ���������� ������������.
    * �� ��������� ������������ ��������� �������: "'"%_"
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
    * ��������� MySQL-����������.
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
    * ����������, ���������� $this->close()
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
    * ���������� MySQL-������.
    * ���������� ��������� �������� -
    * ��������� ����������� MySQL-������.
    *
    * @access public
    * @param void
    * @return string ��������� ����������� MySQL-������
    */
    public function getQueryString()
    {
        return $this->query;
    }

    /**
    * ���������� ������ $this->querys
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
    * ���������� id, ��������������� ���������� ��������� INSERT.
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
     * ���������� ������ ������� ������� $table.
     *
     * @access public
     * @param string ��� �������
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
    * ������ ������ str_replace, �� � ��������� ������� ���������.
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