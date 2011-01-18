<?php
/**
 * Основной класс валидатора.
 *
 * Принцип действия данного валидатора:
 * Валидатор инстанцируется с помощью конструкции
 * <pre>
 * $validator = new Validator_Chain('module_name/lang_file' [, 'module_name/other_lang_file']);
 * </pre>
 * где 'module1/lang_file' и 'module2/other_lang_file' являются путями,
 * указывающими на текстовые файлы описания ошибок валидации,
 * которые хранятся в Modules/module_name/i18n/validator/lang_file
 * Текстовые файлы имеют вид обычных PHP-файлов, возвращающих с помощью конструкции return
 * массивы вида 'КЛЮЧ_ОШИБКИ' => 'Описание ошибки', например:
 * <pre>
 * <?php
 * return array (
 *     'INCORRECT_AUTH_DATA' => 'Некорректные данные авторизации',
 * )?>
 * </pre>
 * Также, текстовые файлы могут иметь вид XML файлов. (дописать про XML)
 *
 * После того, как валидатор инстанцирован, с помощью метода add добавляются
 * валидаторы следующим образом:
 * <pre>
 * $validator->add('key', new Module_Common_Validator_VarEmpty($var));
 * </pre>
 * где 'key' - ключ, под которым будет возвращаться массив ошибок.
 * Module_Common_Validator_VarEmpty - конкретный валидатор
 * $var - проверяемое значение
 * после чего валидация всех валидаторов осуществляется с помощью метода validate().
 * Метод getErrors() возвращает массив ошибок вида
 * key => array('текст ошибки' [, 'текст ошибки' ...])
 *
 * Кроме того, валидатор с помощью $this->addModelErrors
 * может принимать ошибки из модели, которые являются многомерными
 * массивами вида
 *
 * [url] => Array (
 *      [0] => Array (
 *              [0] => INVALID_INT_RANGE
 *              [1] => Array([min] => 10000
 *                           [max] => 2147483647
 *                          )
 *          )
 * )
 */
final class Validator_Chain
{
    /**
     * Массив валидаторов
     *
     * @access private
     * @var array
     */
    private $list = array();

    /**
     * Массив, заполняющийся в конструкторе
     * содержимым файлов описания ошибок.
     *
     * @access private
     * @var array
     */
    private $i18n_error_messages = array();

    /**
     * Временный массив для хранения информации о получаемых
     * ошибках из валидаторов.
     *
     * @access private
     * @var unknown_type
     */
    private $err;

    /**
     * Допустимые расширения файлов описания ошибок.
     *
     * @access private
     * @var array
     */
    private static $i18n_files_extensions = array('php', 'xml');

    /**
     * Принимает неограниченное количество параметров - строк
     * которые являются путями, указывающими на текстовые
     * файлы описания ошибок валидации. Транслирует в переменную
     * $this->i18n_error_messages содержимое файлов ошибок.
     *
     * @param string
     * @return void
     * @todo: сделать методы для определения пути к директории с файлами ошибок
     */
    public function __construct()
    {
        $args = func_get_args();
        $error_message_files = array();

        foreach ($args as $arg)
        {
            list($module, $file) = explode('/', $arg);

            foreach (self::$i18n_files_extensions as $ext)
            {
                $path = implode(DIRECTORY_SEPARATOR,
                                array(dirname(__DIR__),
                                      'Module',
                                      ucfirst($module),
                                      'i18n',
                                      Base_Registry::getInstance()->config->lang,
                                      'validator',
                                      $file
                                     )
                               ).'.'.$ext;

                if (file_exists($path))
                {
                    $error_message_files[$arg] = array($ext, $path);
                    break;
                }
            }

            if (!isset($error_message_files[$arg]))
            {
                trigger_error('Не найден указанный языковой файл валидатора '.$file.
                              ' для модуля '.$module.' указанный по адресу '.$arg);
            }
        }

        foreach ($error_message_files as $key => $error_file_data)
        {
            $messages = null;

            switch ($error_file_data[0])
            {
                case 'php':
                    $messages = $this->getPhpFileData($error_file_data[1]);
                    break;
                case 'xml':
                    $messages = $this->getXmlFileData($error_file_data[1]);
                    break;
                // default:
            }

            if ($messages)
            {
                $this->i18n_error_messages = array_merge_recursive($this->i18n_error_messages, $messages);
            }
        }
    }

    /**
     * Добавляет валидатор $rule под ключом $key в коллецию валидаторов.
     *
     * @access public
     * @param string $key ключ валидатора, соответствующий имени проверяемого поля
     * @param object $rule конкретный валидатор
     * @return void
     */
    public function add($key, $rule)
    {
        if (isset($this->list[$key]) && is_array($this->list[$key]))
        {
            $this->list[$key][] = $rule;
        }
        else
        {
            $this->list[$key] = array($rule);
        }
    }

    /**
    * Проходит по всем валидаторам, добавленным в данный класс,
    * поочерёдно производя валидацию каждого из них.
    * Если валидатор не проходит валидацию, т.е. есть ошибки,
    * метод помещает в массив $this->err новую пару ключ => значение,
    * где ключ - ключ валидатора, а значение - масив информации об ошибках.
    *
    * @access public
    * @param void
    * @return void
    */
    public function validate()
    {
        foreach ($this->list as $key => $rules)
        {
            foreach ($rules as $rule)
            {
                if (!$rule->validate())
                {
                    $this->err[$key][] = $rule->getError();

                    // не продолжать дальше разбор этого значения
                    // на наличие ошибок.
                    if ($rule->getBreak())
                    {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Добавляет ошибку (код ошибки) в колецкию,
     * определённую вне классов валидаторов.
     *
     * @access public
     * @param string $user_key ключ возвращаемого значения
     * @param string $ERROR_KEY ключ ошибки из файлов описания ошибок
     * @param array $placeholders массив меток-заполнителей вида ('placeholder' => 'значение')
     * @return void
     */
    public function addError($user_key, $ERROR_KEY, $placeholders=array())
    {
        $this->err[$user_key][] = array($ERROR_KEY, $placeholders);
    }

    /**
     * Добавляет ошибки, возвращенные моделью.
     *
     * @access public
     * @param array $errors
     * @return void
     */
    public function addModelErrors(array $errors=array())
    {
        foreach ($errors as $key => $data)
        {
            foreach($data as $params)
            {
                $this->addError($key, $params[0], $params[1]);
            }
        }
    }

    /**
     * Возвращает конечный массив ошибок.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getErrors()
    {
        $output = array();

        if ($this->err)
        {
            foreach ($this->err as $key => $value)
            {
                $output[$key] = $this->makeErrorMessage($value);
            }
        }

        return $output;
    }

    /**
     * Получает массив описания ошибок из текстового
     * файла PHP.
     *
     * @access private
     * @param string $file путь до файла PHP
     * @return array
     */
    private function getPhpFileData($file)
    {
        return (array)include $file;
    }

    /**
     * Получает массив описания ошибок из текстового
     * файла XML.
     *
     * @access private
     * @param string $file путь до файла XML
     * @return array
     */
    private function getXmlFileData($file)
    {
        $data = array();

        $xml = simplexml_load_file($file);

        foreach ($xml->children() as $val)
        {
            $data[strtoupper($val->getName())] = trim((string)$val);
        }

        return $data;
    }

    /**
    * Добавляет в класс сообщение об ошибке $error_message
    * с ключом доступа $error_key.
    * Данный метод нужен для частных случаев предопределений
    * существующих сообщений об ошибках.
    *
    * @param string $error_key имя ключа
    * @param string $error_message сообщение об ошибке
    * @return void

    public function setErrorMessage($error_key, $error_message)
    {
        $this->i18n_error_messages[$error_key] = $error_message;
    }*/

    /**
     * Принимает массивы, сформированные валидаторами и возвращает массивы
     * с человекопонятными сообщениями об ошибках.
     *
     * @access private
     * @param array
     * @return array
     */
    private function makeErrorMessage($in)
    {
        $out = array();

        // key - числовой ИД
        // values - ключ ошибки[0] и параметры[1]
        foreach ($in as $id => $params)
        {
            if (is_array($params[0]))
            {
                $out[$id] = $this->makeErrorMessage($in[$id]);
            }
            else
            {
                $error_message = $this->i18n_error_messages[$params[0]];

                foreach ($params[1] as $k => $v)
                {
                    $error_message = str_replace('{'.$k.'}', htmlspecialchars($v, 3), $error_message);
                }

                $out[$id] = $error_message;
            }
        }

        return $out;
    }
}
?>