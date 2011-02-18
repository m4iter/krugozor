<?php
/**
 * Базовый контроллер.
 *
 * @abstract
 */
abstract class Base_Controller
{
    /**
     * Имя класса представления по умолчанию.
     * Если необходимо задать иное имя класса представления,
     * то оно задается вторым аргументом метода $this->getView().
     *
     * @var string
     */
    protected $default_view_class_name = 'Base_View';

    /**
     * Массив допустимых расширений файлов шаблонов.
     *
     * @var array
     * @static
     */
    protected static $template_file_exts = array('.phtml', '.mail');

    /**
     * Вывод отладочной информации
     *
     * @var bool
     */
    protected $view_bebug_info = true;

    /**
     * Звёздный объект-хранилище, содержащий все основные объекты системы.
     *
     * @var Base_Context
     */
    private $context;

    /**
     * Объект представления.
     *
     * @var Base_View
     */
    private $view;

    /**
     * Менеджер Мэпперов.
     * Фактически - хранилище-переносчик различных
     * инстанцированных mapper-объектов.
     *
     * @var Mapper_Manager
     */
    private $mapperManager;

    /**
     * Основной рабочий метод любого конкретного класcа контроллера.
     *
     * @abstract
     * @param void
     * @return mixed
     */
    abstract public function run();

    /**
     * Инициализация основных постоянных значений для View.
     * Вызывается в $this->getView() при создании объекта View.
     *
     * @abstract
     * @param void
     * @return void
     */
    abstract protected function initViewVars();

    /**
     * @param Base_Context
     */
    public function __construct(Base_Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param void
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
     *
     * @param void
     * @return Http_Request
     */
    protected function getRequest()
    {
        return $this->context->getRequest();
    }

    /**
     * Возвращает объект ответа.
     *
     * @param void
     * @return Http_Response
     */
    protected function getResponse()
    {
        return $this->context->getResponse();
    }

    /**
     * Возвращает объект базы данных.
     *
     * @param void
     * @return Db_Mysql_Base
     * @todo: после того, как разберемся с Base_Access - удалить этот метод?
     *        -да, если он больше нигде не понадобится
     */
    protected function getDb()
    {
        return $this->context->getDb();
    }

    /**
     * Создает новый объект системного уведомления.
     *
     * @param void
     * @return Base_Redirect
     */
    protected function createNotification()
    {
        return new Base_Redirect($this->context->getDb());
    }

    /**
     * Метод принимает строку вида `ModuleName/ModelMapperName`,
     * и возвращает объект мэппера, экземпляр класса
     * Module_ModuleName_Mapper_ModelMapperName.
     *
     * @param string
     * @return Mapper_Abstract
     */
    protected function getMapper($path)
    {
        if ($this->mapperManager === null)
        {
            $this->mapperManager = new Mapper_Manager($this->context->getDb());
        }

        return $this->mapperManager->getMapper($path);
    }

    /**
     * Возвращает объект представления.
     * Если представление ещё не создано, оно создается на основе двух параметров -
     * имени файла шаблона и имени класса представления.
     *
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
            $file = $template_file . $ext;

            if (file_exists($file))
            {
                return $file;
            }
        }

        return null;
    }

    /**
     * Вывод отладочной информации внизу страницы.
     *
     * @param void
     * @return string
     */
    public function getDebugInformation()
    {
        if (!$this->view_bebug_info)
        {
            return;
        }

        $content = '<!--noindex--><div style="line-height:1.5em">';

        $content .= '<strong>Модуль</strong>: ' .
                    $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle() .
                    '<br>';
        $content .= '<strong>Контроллер</strong>: ' .
                    $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle() .
                    '<br>';
        $content .= '<strong>URI</strong>: ' .
                    $this->getRequest()->getRequest()->getUri() .
                    '<br>';

        foreach ($this->getRequest()->getRequest() as $key => $value)
        {
            $content .= '<strong>' . $key . '</strong>: ' . $value . '<br>';
        }

        $content .= '<strong>Время</strong>: ' . (microtime(true) - TIME_START) . '<br>';

        foreach ($this->context->getDb()->getQueries() as $key => $value)
        {
            $content .= '<p style="margin:10px 0 0 0"><strong>' . $key . '</strong>: ' . $value . '<br /></p>';
        }

        return $content .= '</div><!--/noindex-->';
    }
}