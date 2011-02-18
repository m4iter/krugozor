<?php
abstract class Html_Element extends Cover_Abstract_Simple
{
    /**
     * Массив данных вида имя_аттрибута => значение.
     * Данные аттрибуты будут представлены
     * в полученном элементе управления.
     *
     * @access protected
     * @var array
     */
    protected $data = array();

    /**
     * Массив допустимых аттрибутов конкретного элемента управления.
     *
     * @access protected
     * @var array
     */
    protected $attrs = array();

    /**
     * Массив допустимых аттрибутов типа coreattrs
     * и их default значения согласно спецификации.
     *
     * @access protected
     * @var array
     */
    protected $coreattrs = array();

    /**
     * Массив допустимых аттрибутов типа i18n
     * и их default значения согласно спецификации.
     *
     * @access protected
     * @var array
     */
    protected $i18n = array();

    /**
     * Массив допустимых аттрибутов типа events
     * и их default значения согласно спецификации.
     *
     * @access protected
     * @var array
     */
    protected $events = array();

    /**
    * Массив всех допустимых аттрибутов и их default значений
    * согласно спецификации.
    *
    * @access protected
    * @var array
    */
    protected $all_attrs = array();

    /**
     * Объект типа DOMDocument.
     *
     * @access protected
     * @var object
     */
    protected $doc;

    /**
     * Массив настроек класса.
     *
     * @access protected
     * @var array
     */
    protected $configs = array();

    /**
     * Конструктор инициализирует все массивы
     * базовых аттрибутов {@link $coreattrs}, {@link $i18n}, {@link $events},
     * а так же устанавливает некоторые настройки класса {@link $configs}
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

        // Строгая проверка на присаивание тегам аттрибутов HTML.
        $this->configs['strict_mode'] = TRUE;
    }

    /**
     * Устанавливает аттрибут $key со значением $value
     * для текущега элемента.
     * Расширение метода __set базового класса Cover_Abstract.
     *
     * @access public
     * @param string $key имя аттрибута элемента HTML
     * @param string $value string значение аттрибута элемента HTML
     * @return void
     */
    public function __set($key, $value)
    {
        //$attrs = array_merge($this->attrs, $this->coreattrs, $this->i18n, $this->events);

        // Если значение аттрибута представленно в виде :name:,
        // то это значит, что значение данного аттрибута должно быть
        // эквивалентно значению аттрибута под именем name, который _должен_
        // быть передан _перед_ ним.
        // Например, код: $object->setData(array('id' => 'myinput', 'name'=>':id:'));
        // даст результат: <input name="myinput" id="myinput" ... />
        if (preg_match('~:([a-z]+):~', $value, $matches))
        {
            $this->data[$key] =& $this->data[$matches[1]];

            return;
        }

        if ($this->configs['strict_mode'])
        {
            if (!isset($this->all_attrs[$key]))
            {
                throw new Exception('Попытка присвоить неизвестный аттрибут '.$key.' тегу '.__CLASS__.'::'.$this->type);
            }

            if (is_array($this->all_attrs[$key]))
            {
                if (!in_array($value, $this->all_attrs[$key]))
                {
                    throw new Exception('Попытка создать неизвестный элемент ('.$key.'='.$value.')');
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
                        throw new Exception('Попытка присвоить недопустимое значение «'.$value.'» аттрибуту '.$key.' (ожидает один символ)');
                    }
                    break;

                case 'ID':
                case 'IDREF':
                    if ($value === '' || !preg_match("~^[a-z][a-z0-9-_:.]*$~i", $value))
                    {
                        throw new Exception('Попытка присвоить недопустимое значение «'.$value.'» аттрибуту '.$key.' (корректный ID)');
                    }
                    break;

                case 'Number':
                    if (!strlen($value) || preg_match("~^[^0-9]$~", $value))
                    {
                        throw new Exception('Попытка присвоить недопустимое значение «'.$value.'» аттрибуту '.$key.' (ожидается цифра)');
                    }
                    break;
            }
        }

        $this->data[$key] = $value;
    }
    // end protected interface Cover_Abstract

    /**
     * В данном методе должны быть реализованы основные действия по формированию
     * объекта $this->doc являющегося экземпляром класса DOMDocument и содержащего
     * нужный элемент управления HTML.
     *
     * @access public
     * @param void
     * @return string
     */
    abstract protected function createDocObject();

    /**
     * Конвертирует строку из кодировки win в кодировку UTF-8.
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
     * Преобразует строку $xml, являющуюся результатом работы метода saveXml()
     * класса DOMDocument в валидгный HTML-код, путём устранения декларации XML.
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
     * Возвращает html-код элемента управления.
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
            throw new Exception('Ошибка в классе '.__CLASS__.'. Объект $this->doc должен быть экземпляром класса DOMDocument');
        }

        return $this->xml2html( $this->doc->saveXML() );
    }


    /**
     * Меняет установки конфигурации.
     *
     * @access public
     * @param string $key имя ключа параметра конфигурации
     * @param mixed value новое значение
     * @return void
     */
    public function configSet($key, $value)
    {
        if (!isset($this->configs[$key]))
        {
            trigger_error('Ошибка в классе '.__CLASS__.'. Попытка изменить неизвестное свойство массива конфигурации.');
        }

        $this->configs[$key] = $value;
    }
}
?>