<?php
/**
 * Объект-хранилище, содержащий все "звёздные" объекты системы.
 */
class Base_Context
{
    /**
     * @var Base_Context
     */
    protected static $instance;

    /**
     * @var Http_Request
     */
    protected $request;

    /**
     * @var Http_Response
     */
    protected $response;

    /**
     * @var Db_Mysql_Base
     */
    protected $db;

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Http_Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Http_Response $response)
    {
        $this->response = $response;

        return $this;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function setDb(Db_Mysql_Base $db)
    {
        $this->db = $db;

        return $this;
    }

    private function __construct(){}
}