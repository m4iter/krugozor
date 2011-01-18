<?php
class Base_View
{
    /**
     * Хранилище данных, передаваемых контроллером
     * через магические методы __set и __get.
     *
     * @access protected
     * @var Cover_Array
     */
    protected $data;

    /**
     * Путь до файла шаблона.
     *
     * @access protected
     * @var string
     */
    protected $template;

    /**
     * Сгенерированный HTML.
     *
     * @access protected
     * @var string
     */
    protected $out;

    /**
     * Массив объектов-хелперов, работающих с view.
     *
     * @access protected
     * @var array
     */
    protected $helpers = array();

    /**
     * Сепаратор хлебных крошек тега title
     *
     * @access protected
     * @var string
     */
    protected $html_title_separator = ' | ';

    /**
     * Конструктор.
     *
     * @access public
     * @param string путь до файла шаблона
     * @return void
     */
    public function __construct($template)
    {
        $this->template = $template;

        if (!$this->template || !file_exists($this->template))
        {
            throw new Exception(__CLASS__.': не найден шаблон '.$this->template);
        }

        $this->data = new Cover_Array();
    }

    /**
     * Возвращает элемент view.
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
     * Присваивает view новый элемент.
     *
     * @access public
     * @param $key ключ значения
     * @param $value значение
     * @return void
     */
    public function __set($key, $value)
    {
        $this->data->$key = $value;
    }

    /**
     * Выводит на печать путь до JS-файла текущего контроллера.
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
     * Функция загружает в представление view в
     * индекс lang хранилища данных данные в языковом файле,
     * содержащемся по адресу $path, который должен представлять из
     * себя строку вида имя_модуля/язык/имя_файла_с_языковыми_данными
     *
     * @access public
     * @param string $string1, string $string2, [, string $...]
     * @todo: если возникнет потребность делать два вызова данной функции,
              то придется переписать код и создать функцию array_merge_recursive
              в контексте класса Cover_Array.
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
                throw new Exception('Не найден указанный языковой файл <strong>'.$file.'</strong> для модуля <strong>'.$module.'</strong> по адресу <strong>'.$path.'</strong>');
            }

            $data = array_merge_recursive($data, (array) include($path));
        }

        $this->data['lang'] = new Cover_Array($data);
    }

    /**
     * Возвращает реальный путь к шаблону $path.
     *
     * @access protected
     * @param string $path путь до файла шаблона вида
     * имя_модуля/имя_файла_шаблона
     * @return string полный путь до файла шаблона
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
            throw new Exception('Не найден подключаемый файл второстепенного шаблона: '.$path);
        }

        return $path;
    }

    /**
     * Возвращает объект-хэлпер $helper_name.
     *
     * @access public
     * @param string $helper_name имя класса-хэлпера
     * @return object
     */
    public function getHelper()
    {
        if (!func_num_args())
        {
            throw new InvalidArgumentException('Попытка вызова метода '.__METHOD__.
                                               ' без указания класса');
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
                    throw new InvalidArgumentException('Попытка вызвать неизвестный helper в контексте View');
                }
                else
                {
                    if (!isset($this->helpers[$helper_name]) ||
                        !$this->helpers[$helper_name] instanceof $helper_name)
                    {
                        $cls = new ReflectionClass($helper_name);

                        // Если хэлпер Singelton, то сохраняем его в хранилище
                        // иначе - просто инстанцируем, возвращаем и "забываем" о нем.
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
     * Создаёт HTML-код текущего шаблона
     * на основании данных присутствующих в текущем представлении.
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
     * Возвращает сгенерированный html-код.
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
     * Принимает объект редиректа, получает необходимые значения
     * и транслирует их во внутреннее представление view.
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
     * Создает строку-сообщение для вывода пользователю.
     * Принимает языковой шаблон $str и массив аргументов
     * вида 'key' => 'value' и заменяет в шаблоне все вставки типа
     * {var_name} на значения из массива аргументов с соответствующими ключами.
     *
     * @access protected
     * @param string строка с метками типа {var}
     * @param array массив аргументов вида array('var' => 'value', [...])
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