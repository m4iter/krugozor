<?php
/**
 * ������� ����������.
 *
 * @abstract
 */
abstract class Base_Controller
{
    /**
     * ������ �������������.
     *
     * @var Base_View
     */
    private $view;

    /**
     * ������ �������.
     *
     * @var Http_Request
     */
    private $request;

    /**
     * ������ ������.
     *
     * @var Http_Response
     */
    private $response;

    /**
     * ��������� ���������������� ��������.
     *
     * @access private
     * @var array
     * @static
     */
    protected static $mappers = array();

    /**
     * ��� ������ ������������� �� ���������.
     * ���� ���������� ������ ���� ��� ������ �������������,
     * �� ��� �������� ������ ���������� ������ $this->getView().
     *
     * @access protected
     * @var string
     */
    protected $default_view_class_name = 'Base_View';

    /**
     * ������ ���������� ���������� ������ ��������.
     *
     * @access protected
     * @var array
     * @static
     */
    protected static $template_file_exts = array('.phtml', '.mail');

    /**
     * ����� ���������� ����������
     *
     * @access protected
     * @var bool
     */
    protected $view_bebug_info = true;

    /**
     * �������� ������� ����� ������ ����������� ����� �����������.
     *
     * @access public
     * @param void
     * @return mixed
     */
    abstract public function run();

    /**
     * ������������� �������� ���������� �������� ��� View.
     * ���������������� �����. ���������� � $this->getView()
     * ��� �������� ������� View.
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
     * @todo: ��� � ��������?
     */
    public function __destruct()
    {
        /*if (isset($this->session) && $this->session instanceof Session)
        {
            $this->session->save();
        }*/
    }

    /**
     * ���������� ������ �������.
     * ����� ��� ����������� �����������.
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
     * ���������� ������ ������.
     * ����� ��� ����������� �����������.
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
     * ����� ��������� ������ ���� `ModuleName/ModelMapperName`,
     * � ���������� ������ �������, ��������� ������
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
     * ���������� ������ �������������.
     * ���� ������������� ��� �� �������, ��� ��������� �� ������ ���� ���������� -
     * ����� ����� ������� � ����� ������ �������������.
     *
     * @access protected
     * @param null|sting ��� ����� ������� ��� null ���� ������������ ������ �� ����� �����������
     * @param null|sting ��� ����� view ��� null ���� ������������ ����� ���� �� ���������
     * @return Base_View
     */
    protected function getView($template=null, $view_class_name=null)
    {
        if ($this->view === null)
        {
            if (!$path_to_template = $this->getTemplateFilePath($template))
	        {
	            throw new Exception(
	                get_class($this) . ': �� ������ ������ ���� ' . $template .
	                ' ��� ����������� ' .
	                $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle()
	            );
	        }

	        try
	        {
		        if ($view_class_name)
	            {
	                // ������� ��������
	                class_exists($view_class_name);
	            }
	        }
	        catch (Exception $e)
	        {
	            throw new Exception(
	                get_class($this) . ': �� ������ ����� ���� ' . $view_class_name .
                    ' ��� ����������� ' .
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
     * ���������� ������ ���� � ����� ������� (��������� � �.�.).
     * ���� ������� $template �� ���������, �� ���� ������� ������ ��� ������
     * /Module/Module_Name/Template/Controller_Name.*
     * ���� �������� $template ���������, �� ���� ������� ������ ��� ������
     * /Module/Module_Name/Template/$template.*
     *
     * @access protected
     * @param null|sting ��� ����� ������� ��� NULL ���� ������������ ������ �� ���������
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
     * ����� ���������� ���������� ����� ��������.
     */
    public function getDebugInformation()
    {
        if (!$this->view_bebug_info) return;

        $content = '<noindex><div style="line-height:1.5em">';

        $content .= '<strong>������</strong>: '.$this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle().'<br>';
        $content .= '<strong>����������</strong>: '.$this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle().'<br>';
        $content .= '<strong>URI</strong>: '.$this->getRequest()->getRequest()->getUri().'<br>';

        foreach ($this->getRequest()->getRequest() as $key => $value)
        {
            $content .= '<strong>'.$key.'</strong>: '.$value.'<br>';
        }

        $value = getmicrotime() - $GLOBALS['time_start'];

        $content .= '<strong>�����</strong>: '.$value.'<br>';

        foreach (Db_Mysql_Base::getInstance()->getQueries() as $key => $value)
        {
            $content .= '<p style="margin:10px 0 0 0"><strong>'.$key.'</strong>: '.$value.'<br></p>';
        }

        return $content .= '</div></noindex>';
    }
}