<?php
class Http_Response
{
    private static $instance;

    /**
     * ������ HTTP-���������� ����
     * ��� => ��������.
     *
     * @access private
     * @var array
     */
    private $headers = array();

    /**
     * ������ �������� ���������� � cookie.
     * ������ ��������� �������� �������� ������������������
     * ���������� ��� ������ setcookie.
     *
     * @access private
     * @var array
     */
    private $cookies = array();

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * ������������� 404 HTTP-���������.
     *
     * @access public
     * @param void
     * @return void
     */
    public function setHeader404()
    {
        $this->setHeader(null, $_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
    }

    /**
     * ������������� HTTP-���������.
     *
     * @access public
     * @param string $name ��� ���������
     * @param string $value ���������� ���������
     * @return void
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return self::$instance;
    }

    /**
     * ���������� HTTP-���������.
     *
     * @access public
     * @param void
     * @return void
     */
    public function sendHeaders($clear=true)
    {
        foreach ($this->headers as $name => $value)
        {
            if ($name)
            {
                header($name.': '.$value);
            }
            else
            {
                header($value);
            }
        }

        if ($clear)
        {
            $this->headers = array();
        }
    }

    /**
     *
     * @param $name
     */
    public function getCookie($name=null)
    {
        if ($name !== null)
        {
            return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
        }

        return $this->cookies;
    }

    /**
     * ������������� cookie �� ���������� ������������� ������.
     * API - ������ PHP-������� cookie.
     *
     * @access public
     * @param see setcookie
     * @return void
     */
    public function setCookie($name, $value=null, $expire=0, $path=null, $domain=null, $secure=false, $httponly=false)
    {
        $this->cookies[$name] = array($value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * ���������� cookie.
     *
     * @access public
     * @param void
     * @return void
     */
    public function sendCookie()
    {
        foreach ($this->cookies as $name => $data)
        {
            $args = array($name);

            foreach ($data as $value)
            {
                if ($value !== null)
                {
                    $args[] = $value;
                }
            }

            call_user_func_array('setcookie', $args);
        }
    }

    /**
     * ������� ��������� ������.
     *
     * @access public
     * @param void
     * @return void
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }
}
?>