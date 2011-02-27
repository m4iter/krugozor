<?php
class Http_Response
{
    private static $instance;

    /**
     * Массив HTTP-заголовков вида `имя заголовка` => `значение`.
     *
     * @var array
     */
    private $headers = array();

    /**
     * Массив массивов информации о cookie.
     * Данные в массивах хранятся согласно последовательности
     * аргументов для функци setcookie.
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
     * Устанавливает 404 HTTP-заголовок ответа.
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
     * Устанавливает HTTP-заголовок ответа.
     *
     * @param string $name имя заголовка
     * @param string $value содержание заголовка
     * @return Http_Response
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Отправляет HTTP-заголовки.
     *
     * @param bool true, если очищать хранилище заголовков,
     *             false в ином случае.
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
     * Возвращает массив установленных для отправки cookie
     * или одну cookie, если передано её имя.
     *
     * @param string имя cookie
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
     * Устанавливает cookie во внутреннее представление класса.
     * API - аналог PHP-функции cookie.
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
     * Отправляет все установленные cookie.
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
     * Очищает заголовки ответа.
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