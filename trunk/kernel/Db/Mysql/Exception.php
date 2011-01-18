<?php
class Db_Mysql_Exception extends Exception
{
    private $mysql_message;

    private $mysql_query;

    public function __construct($message, $mysql_message=null, $mysql_query=null, $mysql_code=0)
    {
        parent::__construct($message, $mysql_code);
        $this->mysql_message = $mysql_message;
        $this->mysql_query = $mysql_query;
    }
}
?>