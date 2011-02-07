<?php
class Http_Request
{
    private static $instance;

    /**
     * �������� $_REQUEST
     *
     * @access private
     * @var Cover_Request
     */
    private $request_data;

    /**
     * �������� $_GET
     *
     * @access private
     * @var Cover_Array
     */
    private $get_data;

    /**
     * �������� $_POST
     *
     * @access private
     * @var Cover_Array
     */
    private $post_data;

    /**
     * �������� $_COOKIE
     *
     * @access private
     * @var Cover_Array
     */
    private $cookie_data;

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->request_data = new Http_Cover_Request(self::clearRequest($_REQUEST));
        $this->post_data    = new Cover_Array(self::clearRequest($_POST));
        $this->get_data     = new Cover_Array(self::clearRequest($_GET));
        $this->cookie_data  = new Cover_Array(self::clearRequest($_COOKIE));
    }

    /**
     * ������ ����� �������� ������������� ������ ������������ ��������
     * ������� ��������, ����� ����� ������� ��������� ������ ��
     * �������������� ������� �������� GPCR.
     *
     * @access public
     */
    public function __set($key, $value)
    {
        throw new Exception('������� ��������� �������� ������� '.__CLASS__);
    }

    /**
     * �������� ������ �� ��������� GET $this->get_data
     *
     * @access public
     * @param void
     * @return object
     */
    public function getGet($key=null, $type=null)
    {
        if ($key !== null)
        {
            return $type === null ? $this->get_data->$key : self::sanitizeValue($this->get_data->$key, $type);
        }

        return $this->get_data;
    }

    /**
     * �������� ������ �� ��������� POST $this->post_data
     *
     * @access public
     * @param void
     * @return object
     */
    public function getPost($key=null, $type=null)
    {
        if ($key !== null)
        {
            return $type === null ? $this->post_data->$key : self::sanitizeValue($this->post_data->$key, $type);
        }

        return $this->post_data;
    }

    /**
     * �������� ������ �� ��������� COOKIE $this->cookie_data
     *
     * @access public
     * @param void
     * @return object
     */
    public function getCookie($key=null, $type=null)
    {
        if ($key !== null)
        {
            return $type === null ? $this->cookie_data->$key : self::sanitizeValue($this->cookie_data->$key, $type);
        }

        return $this->cookie_data;
    }

    /**
     * �������� ������ �� ��������� REQUEST $this->request_data
     *
     * @access public
     * @param void
     * @return object
     */
    public function getRequest($key=null, $type=null)
    {
        if ($key !== null)
        {
            return $type === null ? $this->request_data->$key : self::sanitizeValue($this->request_data->$key, $type);
        }

        return $this->request_data;
    }

    /**
     * ���������� TRUE, ���� ������� ������ POST,
     * FALSE � ��������� ������.
     *
     * @access public
     * @param void
     * @return bool
     * @static
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * ���������� TRUE, ���� ������� ������ GET,
     * FALSE � ��������� ������.
     *
     * @access public
     * @param void
     * @return boolean
     * @static
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * ������� ������ $in �� �������� � ������.
     *
     * @access private
     * @param array
     * @return array
     * @static
     */
    private static function clearRequest(&$in)
    {
        if ($in && is_array($in))
        {
            foreach ($in as $key => $value)
            {
                if (is_array($value))
                {
                    self::clearRequest($in[$key]);
                }
                else
                {
                    $value = trim($value);

                    if (get_magic_quotes_gpc())
                    {
                        $value = stripslashes($value);
                    }

        			$in[$key] = $value;
                }
            }
        }

        return $in;
    }

    /**
     * ���������� � ���� $type �������� $value.
     *
     * @access private
     * @param mixed $value ��������
     * @param string $type ���, � �������� ����� ��������� ��������
     * @return mixed
     * @static
     */
    private static function sanitizeValue($value, $type)
    {
        if (!is_null($value) && $type)
        {
            switch ($type)
            {
                case 'decimal':
                    return preg_replace('/\D/', '', $value);
                    break;
                case 'string':
                    return strval($value);
                    break;
                case 'bool':
                case 'boolean':
                    return (bool) $value;
                default:
                    trigger_error(__METHOD__.': ������������ ��� '.$type);
                    break;
            }
        }

        return $value;
    }
}
?>