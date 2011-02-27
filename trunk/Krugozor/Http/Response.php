<?php
class Http_Response
{
    private static $instance;

    /**
     * ������ HTTP-���������� ���� `��� ���������` => `��������`.
     *
     * @var array
     */
    private $headers = array();

    /**
     * ������ �������� ���������� � cookie.
     * ������ � �������� �������� �������� ������������������
     * ���������� ��� ������ setcookie.
     *
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
     * ������������� 404 HTTP-��������� ������.
     *
     * @param void
     * @return Http_Response
     */
    public function setHeader404()
    {
        $this->setHeader(null, $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

        return $this;
    }

    /**
     * ������������� HTTP-��������� ������.
     *
     * @param string $name ��� ���������
     * @param string $value ���������� ���������
     * @return Http_Response
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * ���������� HTTP-���������.
     *
     * @param bool true, ���� ������� ��������� ����������,
     *             false � ���� ������.
     * @return Http_Response
     */
    public function sendHeaders($clear=true)
    {
        foreach ($this->headers as $name => $value)
        {
            if ($name)
            {
                header($name . ': ' . $value);
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

        return $this;
    }

    /**
     * ���������� ������ ������������� ��� �������� cookie
     * ��� ���� cookie, ���� �������� � ���.
     *
     * @param string ��� cookie
     * @return mixed
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
     * @param see setcookie
     * @return Http_Response
     */
    public function setCookie($name, $value=null, $expire=0, $path=null, $domain=null, $secure=false, $httponly=false)
    {
        $this->cookies[$name] = array($value, $expire, $path, $domain, $secure, $httponly);

        return $this;
    }

    /**
     * ���������� ��� ������������� cookie.
     *
     * @param void
     * @return Http_Response
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

        $this->cookies = array();

        return $this;
    }

    /**
     * ������� ��������� ������.
     *
     * @param void
     * @return Http_Response
     */
    public function clearHeaders()
    {
        $this->headers = array();

        return $this;
    }
}