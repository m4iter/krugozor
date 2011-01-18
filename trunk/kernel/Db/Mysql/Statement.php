<?php
class DB_Mysql_Statement
{
    /**
    * Рузультат SQL-операции в виде ресурса.
    *
    * @var resource
    * @access private
    */
    private $result = null;

    /**
    * Возвращает количество рядов в результате.
    * Эта команда верна только для операторов SELECT.
    *
    * @access public
    * @param void
    * @return int
    */
    public function getNumRows()
    {
        return mysql_num_rows($this->result);
    }

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function fetch_assoc()
    {
        return mysql_fetch_assoc($this->result);
    }

    public function fetch_row()
    {
        return mysql_fetch_row($this->result);
    }

    public function fetch_assoc_array()
    {
        $array = array();

        while(($row = mysql_fetch_assoc($this->result)) !== FALSE)
        {
            $array[] = $row;
        }

        return $array;
    }

    public function fetch_row_array()
    {
        $array = array();

        while(($row = mysql_fetch_row($this->result)) !== FALSE)
        {
            $array[] = $row;
        }

        return $array;
    }

    public function getOne()
    {
        $row = mysql_fetch_row($this->result);

        return $row[0];
    }

    public function free()
    {
        return mysql_free_result($this->result);
    }

    public function __destruct()
    {
        $this->free();
    }
}
?>