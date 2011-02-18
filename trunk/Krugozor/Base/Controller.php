<?php
/**
 * ������� ����������.
 *
 * @abstract
 */
abstract class Base_Controller
{
    /**
     * ��� ������ ������������� �� ���������.
     * ���� ���������� ������ ���� ��� ������ �������������,
     * �� ��� �������� ������ ���������� ������ $this->getView().
     *
     * @var string
     */
    protected $default_view_class_name = 'Base_View';

    /**
     * ������ ���������� ���������� ������ ��������.
     *
     * @var array
     * @static
     */
    protected static $template_file_exts = array('.phtml', '.mail');

    /**
     * ����� ���������� ����������
     *
     * @var bool
     */
    protected $view_bebug_info = true;

    /**
     * ������� ������-���������, ���������� ��� �������� ������� �������.
     *
     * @var Base_Context
     */
    private $context;

    /**
     * ������ �������������.
     *
     * @var Base_View
     */
    private $view;

    /**
     * �������� ��������.
     * ���������� - ���������-���������� ���������
     * ���������������� mapper-��������.
     *
     * @var Mapper_Manager
     */
    private $mapperManager;

    /**
     * �������� ������� ����� ������ ����������� ����c� �����������.
     *
     * @abstract
     * @param void
     * @return mixed
     */
    abstract public function run();

    /**
     * ������������� �������� ���������� �������� ��� View.
     * ���������� � $this->getView() ��� �������� ������� View.
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
     * ���������� ������ �������.
     *
     * @param void
     * @return Http_Request
     */
    protected function getRequest()
    {
        return $this->context->getRequest();
    }

    /**
     * ���������� ������ ������.
     *
     * @param void
     * @return Http_Response
     */
    protected function getResponse()
    {
        return $this->context->getResponse();
    }

    /**
     * ���������� ������ ���� ������.
     *
     * @param void
     * @return Db_Mysql_Base
     * @todo: ����� ����, ��� ���������� � Base_Access - ������� ���� �����?
     *        -��, ���� �� ������ ����� �� �����������
     */
    protected function getDb()
    {
        return $this->context->getDb();
    }

    /**
     * ������� ����� ������ ���������� �����������.
     *
     * @param void
     * @return Base_Redirect
     */
    protected function createNotification()
    {
        return new Base_Redirect($this->context->getDb());
    }

    /**
     * ����� ��������� ������ ���� `ModuleName/ModelMapperName`,
     * � ���������� ������ �������, ��������� ������
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
     * ���������� ������ �������������.
     * ���� ������������� ��� �� �������, ��� ��������� �� ������ ���� ���������� -
     * ����� ����� ������� � ����� ������ �������������.
     *
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
            $file = $template_file . $ext;

            if (file_exists($file))
            {
                return $file;
            }
        }

        return null;
    }

    /**
     * ����� ���������� ���������� ����� ��������.
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

        $content .= '<strong>������</strong>: ' .
                    $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle() .
                    '<br>';
        $content .= '<strong>����������</strong>: ' .
                    $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle() .
                    '<br>';
        $content .= '<strong>URI</strong>: ' .
                    $this->getRequest()->getRequest()->getUri() .
                    '<br>';

        foreach ($this->getRequest()->getRequest() as $key => $value)
        {
            $content .= '<strong>' . $key . '</strong>: ' . $value . '<br>';
        }

        $content .= '<strong>�����</strong>: ' . (microtime(true) - TIME_START) . '<br>';

        foreach ($this->context->getDb()->getQueries() as $key => $value)
        {
            $content .= '<p style="margin:10px 0 0 0"><strong>' . $key . '</strong>: ' . $value . '<br /></p>';
        }

        return $content .= '</div><!--/noindex-->';
    }
}