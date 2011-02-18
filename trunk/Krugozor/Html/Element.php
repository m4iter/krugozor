<?php
abstract class Html_Element extends Cover_Abstract_Simple
{
    /**
     * ������ ������ ���� ���_��������� => ��������.
     * ������ ��������� ����� ������������
     * � ���������� �������� ����������.
     *
     * @access protected
     * @var array
     */
    protected $data = array();

    /**
     * ������ ���������� ���������� ����������� �������� ����������.
     *
     * @access protected
     * @var array
     */
    protected $attrs = array();

    /**
     * ������ ���������� ���������� ���� coreattrs
     * � �� default �������� �������� ������������.
     *
     * @access protected
     * @var array
     */
    protected $coreattrs = array();

    /**
     * ������ ���������� ���������� ���� i18n
     * � �� default �������� �������� ������������.
     *
     * @access protected
     * @var array
     */
    protected $i18n = array();

    /**
     * ������ ���������� ���������� ���� events
     * � �� default �������� �������� ������������.
     *
     * @access protected
     * @var array
     */
    protected $events = array();

    /**
    * ������ ���� ���������� ���������� � �� default ��������
    * �������� ������������.
    *
    * @access protected
    * @var array
    */
    protected $all_attrs = array();

    /**
     * ������ ���� DOMDocument.
     *
     * @access protected
     * @var object
     */
    protected $doc;

    /**
     * ������ �������� ������.
     *
     * @access protected
     * @var array
     */
    protected $configs = array();

    /**
     * ����������� �������������� ��� �������
     * ������� ���������� {@link $coreattrs}, {@link $i18n}, {@link $events},
     * � ��� �� ������������� ��������� ��������� ������ {@link $configs}
     *
     * @access public
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->coreattrs = array
        (
            'id' => 'ID',
            'class' => 'NMTOKENS',
            'style' => 'CDATA',
            'title' => 'CDATA'
        );

        $this->i18n = array
        (
            'lang' => 'CDATA',
            'dir' => array('ltr', 'rtl')
        );

        $this->events = array
        (
            'onclick' => 'Script',
            'ondblclick' => 'Script',
            'onmousedown' => 'Script',
            'onmouseup' => 'Script',
            'onmouseover' => 'Script',
            'onmousemove' => 'Script',
            'onmouseout' => 'Script',
            'onkeypress' => 'Script',
            'onkeydown' => 'Script',
            'onkeyup' => 'Script'
        );

        // ������� �������� �� ����������� ����� ���������� HTML.
        $this->configs['strict_mode'] = TRUE;
    }

    /**
     * ������������� �������� $key �� ��������� $value
     * ��� �������� ��������.
     * ���������� ������ __set �������� ������ Cover_Abstract.
     *
     * @access public
     * @param string $key ��� ��������� �������� HTML
     * @param string $value string �������� ��������� �������� HTML
     * @return void
     */
    public function __set($key, $value)
    {
        //$attrs = array_merge($this->attrs, $this->coreattrs, $this->i18n, $this->events);

        // ���� �������� ��������� ������������� � ���� :name:,
        // �� ��� ������, ��� �������� ������� ��������� ������ ����
        // ������������ �������� ��������� ��� ������ name, ������� _������_
        // ���� ������� _�����_ ���.
        // ��������, ���: $object->setData(array('id' => 'myinput', 'name'=>':id:'));
        // ���� ���������: <input name="myinput" id="myinput" ... />
        if (preg_match('~:([a-z]+):~', $value, $matches))
        {
            $this->data[$key] =& $this->data[$matches[1]];

            return;
        }

        if ($this->configs['strict_mode'])
        {
            if (!isset($this->all_attrs[$key]))
            {
                throw new Exception('������� ��������� ����������� �������� '.$key.' ���� '.__CLASS__.'::'.$this->type);
            }

            if (is_array($this->all_attrs[$key]))
            {
                if (!in_array($value, $this->all_attrs[$key]))
                {
                    throw new Exception('������� ������� ����������� ������� ('.$key.'='.$value.')');
                }
            }

            switch ($this->all_attrs[$key])
            {
                case 'Script':
                case 'ContentTypes':
                case 'URI':
                case 'NMTOKENS':
                    break;

                case 'CDATA':
                case 'Text':
                    $value = $this->convertEncoding($value);
                    break;

                case 'Character':
                    if (empty($value) || strlen($value) !== 1 || !preg_match("~^[a-z0-9]$~i", $value))
                    {
                        throw new Exception('������� ��������� ������������ �������� �'.$value.'� ��������� '.$key.' (������� ���� ������)');
                    }
                    break;

                case 'ID':
                case 'IDREF':
                    if ($value === '' || !preg_match("~^[a-z][a-z0-9-_:.]*$~i", $value))
                    {
                        throw new Exception('������� ��������� ������������ �������� �'.$value.'� ��������� '.$key.' (���������� ID)');
                    }
                    break;

                case 'Number':
                    if (!strlen($value) || preg_match("~^[^0-9]$~", $value))
                    {
                        throw new Exception('������� ��������� ������������ �������� �'.$value.'� ��������� '.$key.' (��������� �����)');
                    }
                    break;
            }
        }

        $this->data[$key] = $value;
    }
    // end protected interface Cover_Abstract

    /**
     * � ������ ������ ������ ���� ����������� �������� �������� �� ������������
     * ������� $this->doc ����������� ����������� ������ DOMDocument � �����������
     * ������ ������� ���������� HTML.
     *
     * @access public
     * @param void
     * @return string
     */
    abstract protected function createDocObject();

    /**
     * ������������ ������ �� ��������� win � ��������� UTF-8.
     *
     * @access public
     * @param string
     * @return string
     */
    protected function convertEncoding($value)
    {
        return iconv('Windows-1251', 'utf-8', $value);
    }

    /**
     * ����������� ������ $xml, ���������� ����������� ������ ������ saveXml()
     * ������ DOMDocument � ��������� HTML-���, ���� ���������� ���������� XML.
     *
     * @access public
     * @param string
     * @return string
     */
    protected function xml2html($xml)
    {
        return trim(str_replace('<?xml version="1.0" encoding="windows-1251"?>', '', $xml));
    }

    /**
     * ���������� html-��� �������� ����������.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getHtml()
    {
        $this->createDocObject();

        if (!$this->doc instanceof DOMDocument)
        {
            throw new Exception('������ � ������ '.__CLASS__.'. ������ $this->doc ������ ���� ����������� ������ DOMDocument');
        }

        return $this->xml2html( $this->doc->saveXML() );
    }


    /**
     * ������ ��������� ������������.
     *
     * @access public
     * @param string $key ��� ����� ��������� ������������
     * @param mixed value ����� ��������
     * @return void
     */
    public function configSet($key, $value)
    {
        if (!isset($this->configs[$key]))
        {
            trigger_error('������ � ������ '.__CLASS__.'. ������� �������� ����������� �������� ������� ������������.');
        }

        $this->configs[$key] = $value;
    }
}
?>