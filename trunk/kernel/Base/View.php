<?php
class Base_View
{
    /**
     * ��������� ������, ������������ ������������
     * ����� ���������� ������ __set � __get.
     *
     * @access protected
     * @var Cover_Array
     */
    protected $data;

    /**
     * ���� �� ����� �������.
     *
     * @access protected
     * @var string
     */
    protected $template;

    /**
     * ��������������� HTML.
     *
     * @access protected
     * @var string
     */
    protected $out;

    /**
     * ������ ��������-��������, ���������� � view.
     *
     * @access protected
     * @var array
     */
    protected $helpers = array();

    /**
     * ��������� ������� ������ ���� title
     *
     * @access protected
     * @var string
     */
    protected $html_title_separator = ' | ';

    /**
     * �����������.
     *
     * @access public
     * @param string ���� �� ����� �������
     * @return void
     */
    public function __construct($template)
    {
        $this->template = $template;

        if (!$this->template || !file_exists($this->template))
        {
            throw new Exception(__CLASS__.': �� ������ ������ '.$this->template);
        }

        $this->data = new Cover_Array();
    }

    /**
     * ���������� ������� view.
     *
     * @access public
     * @param void
     * @return mixed
     */
    public function __get($key)
    {
        return $this->data->$key;
    }

    /**
     * ����������� view ����� �������.
     *
     * @access public
     * @param $key ���� ��������
     * @param $value ��������
     * @return void
     */
    public function __set($key, $value)
    {
        $this->data->$key = $value;
    }

    /**
     * ������� �� ������ ���� �� JS-����� �������� �����������.
     *
     * @access public
     * @param void
     * @return void
     */
    public function includeJsModule()
    {
        echo '<script type="text/javascript" src="'.$this->data['path']['js'].'modules/'.
             $this->data['_module_name'].'/'.$this->data['_controller_name'].'.js"></script>';
    }

    /**
     * ������� ��������� � ������������� view �
     * ������ lang ��������� ������ ������ � �������� �����,
     * ������������ �� ������ $path, ������� ������ ������������ ��
     * ���� ������ ���� ���_������/����/���_�����_�_���������_�������
     *
     * @access public
     * @param string $string1, string $string2, [, string $...]
     * @todo: ���� ��������� ����������� ������ ��� ������ ������ �������,
              �� �������� ���������� ��� � ������� ������� array_merge_recursive
              � ��������� ������ Cover_Array.
     * @return void
     */
    public function loadI18n()
    {
        $args = func_get_args();
        $data = array();

        foreach ($args as $arg)
        {
            list($module, $file) = explode('/', $arg);

            $path = implode(DIRECTORY_SEPARATOR,
                            array(dirname(__DIR__),
                                  'Module',
                                  ucfirst($module),
                                  'i18n',
                                  Base_Registry::getInstance()->config['lang'],
                                  'controller',
                                  $file
                                 )
                            ).'.php';

            if (!file_exists($path))
            {
                throw new Exception('�� ������ ��������� �������� ���� <strong>'.$file.'</strong> ��� ������ <strong>'.$module.'</strong> �� ������ <strong>'.$path.'</strong>');
            }

            $data = array_merge_recursive($data, (array) include($path));
        }

        $this->data['lang'] = new Cover_Array($data);
    }

    /**
     * ���������� �������� ���� � ������� $path.
     *
     * @access protected
     * @param string $path ���� �� ����� ������� ����
     * ���_������/���_�����_�������
     * @return string ������ ���� �� ����� �������
     */
    protected function getRealTemplatePath($path)
    {
        list($module, $file) = explode('/', $path);

        $path = dirname(__DIR__).DIRECTORY_SEPARATOR.'Module'.DIRECTORY_SEPARATOR.
                                $module.DIRECTORY_SEPARATOR.
                                'Template'.DIRECTORY_SEPARATOR.
                                $file.'.phtml';

        if (!file_exists($path))
        {
            throw new Exception('�� ������ ������������ ���� ��������������� �������: '.$path);
        }

        return $path;
    }

    /**
     * ���������� ������-������ $helper_name.
     *
     * @access public
     * @param string $helper_name ��� ������-�������
     * @return object
     */
    public function getHelper()
    {
        if (!func_num_args())
        {
            throw new InvalidArgumentException('������� ������ ������ '.__METHOD__.
                                               ' ��� �������� ������');
        }

        $args = func_get_args();

        $helper_name = array_shift($args);

        switch ($helper_name)
        {
            case 'Html_Title':

                if (!isset($this->helpers[$helper_name]) ||
                    !$this->helpers[$helper_name] instanceof Html_Title)
                {
                    $this->helpers[$helper_name] = Html_Title::getInstance($this->html_title_separator);
                }

                return $this->helpers[$helper_name]; break;


            case 'Helper_Form':

                if (!isset($this->helpers[$helper_name]) ||
                    !$this->helpers[$helper_name] instanceof Helper_Form)
                {
                    $this->helpers[$helper_name] = Helper_Form::getInstance();
                    $this->helpers[$helper_name]->setFieldErrorTemplate( $this->getRealTemplatePath('Common/FieldError') );
                }

                return $this->helpers[$helper_name]; break;


            default:

                if (!class_exists($helper_name))
                {
                    throw new InvalidArgumentException('������� ������� ����������� helper � ��������� View');
                }
                else
                {
                    if (!isset($this->helpers[$helper_name]) ||
                        !$this->helpers[$helper_name] instanceof $helper_name)
                    {
                        $cls = new ReflectionClass($helper_name);

                        // ���� ������ Singelton, �� ��������� ��� � ���������
                        // ����� - ������ ������������, ���������� � "��������" � ���.
                        if ($cls->hasMethod('getInstance'))
                        {
                            $method = $cls->getMethod('getInstance');

                            if ($method->isStatic())
                            {
                                $this->helpers[$helper_name] = call_user_func_array(array($cls->getName(), 'getInstance'), $args);
                            }
                        }
                        else
                        {
                            return $cls->newInstanceArgs($args);
                        }
                    }

                    return $this->helpers[$helper_name];
                }

                break;
        }
    }

    /**
     * ������ HTML-��� �������� �������
     * �� ��������� ������ �������������� � ������� �������������.
     *
     * @access public
     * @param void
     * @return string
     */
    public function run()
    {
        ob_start();
        include($this->template);
        $this->out = ob_get_contents();
        ob_end_clean();
    }

    /**
     * ���������� ��������������� html-���.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getOut()
    {
        return $this->getHelper('Helper_Format')->cleanWhitespace($this->out);
    }

    /**
     * ��������� ������ ���������, �������� ����������� ��������
     * � ����������� �� �� ���������� ������������� view.
     *
     * @access public
     * @param object Base_Redirect
     * @return void
     */
    public function setRedirect(Base_Redirect $redirect)
    {
        $this->data['notification'] = new Cover_Array();

        $this->data['notification']['message'] = self::makeMessageFromParams($this->data['lang']['notification'][$redirect->getMessage()],
                                                                             $redirect->getParams()
                                                                            );
        $this->data['notification']['hidden'] = $redirect->getHidden();

        $this->data['notification']['type'] = $redirect->getType();

        if ($redirect->getHeader())
        {
            $this->data['notification']['header'] = $this->data['lang']['notification'][$redirect->getHeader()];
        }
        else
        {
            switch ($redirect->getType())
            {
                case 'alert':
                    $this->data['notification']['header'] = $this->data['lang']['notification']['action_failed'];
                    break;
                case 'warning':
                    $this->data['notification']['header'] = $this->data['lang']['notification']['action_warning'];
                    break;
                default:
                    $this->data['notification']['header'] = $this->data['lang']['notification']['action_complete'];
            }
        }
    }

    /**
     * ������� ������-��������� ��� ������ ������������.
     * ��������� �������� ������ $str � ������ ����������
     * ���� 'key' => 'value' � �������� � ������� ��� ������� ����
     * {var_name} �� �������� �� ������� ���������� � ���������������� �������.
     *
     * @access protected
     * @param string ������ � ������� ���� {var}
     * @param array ������ ���������� ���� array('var' => 'value', [...])
     * @return string
     */
    protected static function makeMessageFromParams($str, $args)
    {
        foreach ($args as $key => $value)
        {
            $str = str_replace('{'.$key.'}', $value, $str);
        }

        return $str;
    }
}
?>