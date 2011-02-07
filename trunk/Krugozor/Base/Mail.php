<?php
class Base_Mail
{
    /**
     * ������ �������.
     *
     * @var array
     */
    private $data = array();

    /**
     * ��� ������.
     * text ��� html, �� ��������� text
     *
     * @var string
     */
    private $type;

    /**
     * mime-����
     *
     * @var unknown_type
     */
    private static $types = array(
            'text' => 'text/plain',
            'html' => 'text/html',
        );

    private $to;
    private $from;
    private $reply_to;

    /**
     * ���� �� ����� ��������� �������.
     *
     * @var string
     */
    private $tpl_file;

    /**
     * HTTP-���������
     *
     * @var array
     */
    private $headers;

    /**
     * ���� ������ ����� ���������.
     *
     * @var string
     */
    private $message;

    public function __construct()
    {
        $this->type = 'text';
        $this->lang = 'ru';
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function setReplyTo($reply_to)
    {
        $this->reply_to = $reply_to;
    }

    public function setTemplate($tpl_file)
    {
        if (!file_exists($tpl_file))
        {
            new Exception('�� ������ �������� ������ '.$tpl_file);
        }

        $this->tpl_file = $tpl_file;
    }

    public function setHeader($header)
    {
        $this->header = '=?koi8-r?B?'.base64_encode(convert_cyr_string($header, "w", "k")).'?=';
    }

    public function send()
    {
        $this->headers = 'Content-type: '.self::$types[$this->type].'; charset=windows-1251'."\n".
        "Content-language: ".$this->lang."\n".
        'From: '.$this->from."\n".
        'X-Mailer: PHP/'.phpversion()."\n".
        'Date: '.date("r")."\n".
        'Reply-To: '.($this->reply_to ? $this->reply_to : $this->from);

        ob_start();
        include($this->tpl_file);
        $this->message = ob_get_contents();
        ob_end_clean();

        return mail($this->to, $this->header, $this->message, $this->headers);
    }
}
?>