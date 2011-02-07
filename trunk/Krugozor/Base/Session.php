<?php
/*
$this->session = Base_Session::getInstance('captcha');
$this->session->start();
$this->session->aaa = 333;
$this->session = Base_Session::getInstance('captcha');
$this->session->bbb = 444;
pr($this->session);
$this->session->destroy();
pr($this->session);
*/
class Base_Session extends Cover_Abstract_Array
{
    private static $instance;

    private static $has_instance;

    /**
     * ��� ������.
     *
     * @access private
     * @var string
     */
    private $session_name;

    /**
     * ID ������.
     *
     * @access private
     * @var string
     */
    private $session_id;

    public static function getInstance($session_name=null)
    {
        if (self::$instance === null)
        {
            self::$instance = new self($session_name);
        }

        return self::$instance;
    }

    /**
     * �������� ������. ������������� ��� �����
     * $session_name, ���� ��� ���������� � ����������� ���
     * PHPSESSID � �������� ������.
     *
     * @param $session_name
     * @return void
     */
    private function __construct($session_name=null)
    {
        $this->session_name = $session_name !== null
                              ? $session_name
                              : session_name();

        session_name($this->session_name);

        $this->start();
    }

    public function __destruct()
    {
        $this->save();
    }

    public function save()
    {
        $_SESSION = $this->data;
    }

    /**
     * ���������� TRUE, ���� ������ ��� ����������,
     * FALSE � ��������� ������.
     *
     * @access public
     * @param void
     * @return boolean
     */
    public function isStarted()
    {
        return !(session_id() === '');
    }

    public function start()
    {
        if (!$this->isStarted())
        {
            if (null !== $this->session_id)
            {
                session_id($this->session_id);
            }

            session_start();

            $this->session_id = session_id();

            if (!empty($_SESSION))
            {
                $this->data = $_SESSION;
            }
        }

        return $this;
    }

    public function destroy()
    {
        $this->data = $_SESSION = array();

        if (ini_get('session.use_cookies'))
        {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * ������������� ID ������.
     * ���������� �� ������ start()
     *
     * @access public
     * @param string $sid ������������� ������
     * @return void
     * @todo: ����� ���� �����?
     */
    public function setId($sid)
    {
        if (null !== $this->session_id)
        {
            throw new LogicException('������� ��������� ID �������� ������.');
        }

        $sid = trim($sid);

        if (preg_match('~[^a-z0-9_\-]+~', $sid))
        {
            throw new InvalidArgumentException('������� ��������� ������������ ID ������.');
        }

        $this->session_id = $sid;
    }

    public function getId()
    {
        return $this->session_id;
    }

    public function getName()
    {
        return $this->session_name;
    }
}
?>