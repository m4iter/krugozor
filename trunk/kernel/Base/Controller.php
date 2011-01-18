<?php
/**
 * Базовый контроллер.
 *
 * @abstract
 */
abstract class Base_Controller
{
    /**
     * Объект представления.
     *
     * @var Base_View
     */
    private $view;

    /**
     * Объект запроса.
     *
     * @var Http_Request
     */
    private $request;

    /**
     * Объект ответа.
     *
     * @var Http_Response
     */
    private $response;

    /**
     * Коллекция инстанцированных мэпперов.
     *
     * @access private
     * @var array
     * @static
     */
    protected static $mappers = array();

    /**
     * Имя класса представления по умолчанию.
     * Если необходимо задать иное имя класса представления,
     * то оно задается вторым аргументом метода $this->getView().
     *
     * @access protected
     * @var string
     */
    protected $default_view_class_name = 'Base_View';

    /**
     * Массив допустимых расширений файлов шаблонов.
     *
     * @access protected
     * @var array
     * @static
     */
    protected static $template_file_exts = array('.phtml', '.mail');

    /**
     * Вывод отладочной информации
     *
     * @access protected
     * @var bool
     */
    protected $view_bebug_info = true;

    /**
     * Основной рабочий метод любого конкретного класа контроллера.
     *
     * @access public
     * @param void
     * @return mixed
     */
    abstract public function run();

    /**
     * Инициализация основных постоянных значений для View.
     * Предопределяемый метод. Вызывается в $this->getView()
     * при создании объекта View.
     *
     * @access protected
     * @param void
     * @return void
     */
    abstract protected function initViewVars();

    /**
     * @param Http_Request $request
     * @param Http_Response $response
     */
    public function __construct(Http_Request $request, Http_Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * @access public
     * @param void
     * @return void
     * @todo: что с сессиями?
     */
    public function __destruct()
    {
        /*if (isset($this->session) && $this->session instanceof Session)
        {
            $this->session->save();
        }*/
    }

    /**
     * Возвращает объект запроса.
     * Метод для внутреннего пользования.
     *
     * @access protected
     * @param void
     * @return Http_Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Возвращает объект ответа.
     * Метод для внутреннего пользования.
     *
     * @access protected
     * @param void
     * @return Http_Response
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * Метод принимает строку вида `ModuleName/ModelMapperName`,
     * и возвращает объект мэппера, экземпляр класса
     * Module_ModuleName_Mapper_ModelMapperName
     *
     * @param string $path
     * @return Base_Mapper
     */
    protected static function getMapper($path)
    {
        if (isset(self::$mappers[$path]))
        {
            return self::$mappers[$path];
        }

        list($module, $model) = explode('/', $path);

        $mapper_path = 'Module_'.$module.'_Mapper_'.$model;

        if (class_exists($mapper_path))
        {
            return self::$mappers[$path] = new $mapper_path();
        }
    }

    /**
     * Возвращает объект представления.
     * Если представление ещё не создано, оно создается на основе двух параметров -
     * имени файла шаблона и имени класса представления.
     *
     * @access protected
     * @param null|sting имя файла шаблона или null если использовать шаблон по имени контроллера
     * @param null|sting имя файла view или null если использовать класс вида по умолчанию
     * @return Base_View
     */
    protected function getView($template=null, $view_class_name=null)
    {
        if ($this->view === null)
        {
            if (!$path_to_template = $this->getTemplateFilePath($template))
	        {
	            throw new Exception(
	                get_class($this) . ': Не найден шаблон вида ' . $template .
	                ' для контроллера ' .
	                $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle()
	            );
	        }

	        try
	        {
		        if ($view_class_name)
	            {
	                // Дергаем автолоад
	                class_exists($view_class_name);
	            }
	        }
	        catch (Exception $e)
	        {
	            throw new Exception(
	                get_class($this) . ': Не найден класс вида ' . $view_class_name .
                    ' для контроллера ' .
	                $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle()
	            );
	        }

	        $view_class_name = $view_class_name ?: $this->default_view_class_name;

	        $this->view = new $view_class_name($path_to_template);

	        $this->initViewVars();
        }

        return $this->view;
    }

    /**
     * Определяет полный путь к файлу шаблона (почтовому в т.ч.).
     * Если парметр $template не определен, то файл шаблона ищется под именем
     * /Module/Module_Name/Template/Controller_Name.*
     * Если параметр $template определен, то файл шаблона ищется под именем
     * /Module/Module_Name/Template/$template.*
     *
     * @access protected
     * @param null|sting имя файла шаблона или NULL если использовать шаблон по умолчанию
     * @return string|null
     */
    protected function getTemplateFilePath($template=null)
    {
        if ($template === null)
        {
            $template_file = implode(DIRECTORY_SEPARATOR,
                                     array(dirname(__DIR__),
                                           'Module',
                                           $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle(),
                                           'Template',
                                           $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle()
                                          )
                                    );
        }
        else
        {
            $template_file = implode(DIRECTORY_SEPARATOR,
                                     array(dirname(__DIR__),
                                           'Module',
                                           $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle(),
                                           'Template',
                                           $template
                                          )
                                    );
        }

        foreach (self::$template_file_exts as $ext)
        {
            $file = $template_file.$ext;

            if (file_exists($file))
            {
                return $file;
            }
        }

        return null;
    }

    /**
     * Вывод отладочной информации внизу страницы.
     */
    public function getDebugInformation()
    {
        if (!$this->view_bebug_info) return;

        $content = '<noindex><div style="line-height:1.5em">';

        $content .= '<strong>Модуль</strong>: '.$this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle().'<br>';
        $content .= '<strong>Контроллер</strong>: '.$this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle().'<br>';
        $content .= '<strong>URI</strong>: '.$this->getRequest()->getRequest()->getUri().'<br>';

        foreach ($this->getRequest()->getRequest() as $key => $value)
        {
            $content .= '<strong>'.$key.'</strong>: '.$value.'<br>';
        }

        $value = getmicrotime() - $GLOBALS['time_start'];

        $content .= '<strong>Время</strong>: '.$value.'<br>';

        foreach (Db_Mysql_Base::getInstance()->getQueries() as $key => $value)
        {
            $content .= '<p style="margin:10px 0 0 0"><strong>'.$key.'</strong>: '.$value.'<br></p>';
        }

        return $content .= '</div></noindex>';
    }
}