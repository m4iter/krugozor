<?php
class Base_Application
{
    /**
     * ��� ���������� �������, ���������� ����������� URI.
     * ��� ��������� �������� ����������� REQUEST_URI, ���������� ��
     * � ������� ��������� mod_rewrite-a (��. .htaccess).
     *
     * @var string
     */
    const REQUEST_PATH = '_path';

    /**
     * ������-���������, ���������� ��� "�������" ������� �������.
     *
     * @var Base_Context
     */
    private $context;

    /**
     * ������ ���������� URL-������� ������� � ����
     * �������� ���������� ��������� (��. /config/routes.php).
     *
     * @var array
     */
    private $routes = array();

    /**
     * ���������, �������� �� ���������� ����������.
     * true - ��������, false - ���.
     *
     * @var boolean
     */
    private $enabled_debug_info = false;

    /**
     * @param Base_Context $context
     */
    public function __construct(Base_Context $context)
    {
        $this->context = $context;
    }

    /**
     * ��������� ������ ���������� ��������� URL.
     *
     * @param array
     * @return Base_Application
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * ��������� ���� � PHP-����� �������� ��������� URL.
     * ���� ������ � ������� ����������� return ���������� ������
     * ������ ������������� (��. /config/routes.php).
     *
     * @param string ���� �� ����� ������ �������������
     * @return Base_Application
     */
    public function getRoutesFromPhpFile($path)
    {
        if (!file_exists($path))
        {
            throw new RuntimeException('�� ������ ��������� ���� ���������');
        }

        $this->setRoutes((array) include $path);

        return $this;
    }

    /**
     * �������������, ����� �� �������� ���������� ����������.
     *
     * @param bool
     * @return Base_Application
     */
    public function enabledDebugInfo($enabled)
    {
        $this->enabled_debug_info = (bool)$enabled;

        return $this;
    }

    /**
     * �������� ����� ����������, ����������� ���������� �����������
     * � �������� � output ���������.
     *
     * @param void
     * @return void
     */
    public function run()
    {
        $uri = $this->context->getRequest()->getRequest(self::REQUEST_PATH);

        if ($uri === null)
        {
            $uri = '/';
        }

        if (!$this->compareRequestWithUriRoutes($uri))
        {
            if (!$this->compareRequestWithStandartUriMap($uri))
            {
                $this->context->getResponse()->clearHeaders();
                $this->context->getRequest()->getRequest()->setModuleName(new Http_UriPartEntity('404'));
                $this->context->getRequest()->getRequest()->setControllerName(new Http_UriPartEntity('404'));
            }
        }

        $controller_name = $this->getControllerClassName
        (
            $this->context->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle(),
            $this->context->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle()
        );

        $controller = new $controller_name($this->context);
        $result = $controller->run();

        if (!is_object($result))
        {
            throw new RuntimeException('��� ����������');
        }

        if ($result instanceof Base_View)
        {
            // ���� � ������� ������������ notif, ������ ���������� �������� �� view
            // ����������, ���������� � ���������� �������� � ������� � �� ������.
            // @todo: ������� ��� � ����������, ���� �����
            if ($this->context->getRequest()->getRequest('notif', 'decimal'))
            {
                $redirect = new Base_Redirect($this->context->getDb());

                $redirect->findById($this->context->getRequest()->getRequest('notif', 'decimal'));

                if ($redirect->getId())
                {
                    $result->setRedirect($redirect);
                }
            }

            $result->run();

            $this->context->getResponse()->sendCookie()
                                         ->sendHeaders();
            echo $result->getOut();

            if ($this->enabled_debug_info || isset($this->context->getRequest()->getRequest()->aaa))
            {
                echo $controller->getDebugInformation();
            }
        }
        else if ($result instanceof Base_Redirect)
        {
            $this->context->getResponse()->setHeader('Location', $result->getRedirectUrl())
                                         ->sendCookie()
                                         ->sendHeaders();
        }
        else if ($result instanceof Module_Common_Model_ImagePng)
        {
            $this->context->getResponse()->sendHeaders();

            imagepng($result->getGdResource());
        }
    }

    /**
     * ��������� ������� URI-������, ������� ���������� � �������� ���������,
     * � ���������� ��� � ����� �� ��������� URL-����� $this->routes.
     * ���� ���������� �������, �� � ������-�������� Request ������������ ����������
     * �� ����, ����� ���
     * - ��� ������
     * - ��� ����������
     * - ���������� URI-�����
     * - ��������� �������.
     *
     * @param string URI-������
     * @return boolean true ���� ��� ������� $uri ������� ���������� � $this->routes
     *                 � false � ��������� ������.
     * @todo: ������� �������� �� ���������� ������� ����������� � ����� $this->routes �����������.
     */
    private function compareRequestWithUriRoutes($uri)
    {
        foreach ($this->routes as $map)
        {
            if (preg_match($map['pattern'], $uri, $params))
            {
                array_shift($params);

                foreach ($params as $index => $value)
                {
                    $this->context->getRequest()->getRequest()->{$map['aliases'][$index]} = $value;
                }

                $this->context->getRequest()->getRequest()->setModuleName(new Http_UriPartEntity($map['module']));
                $this->context->getRequest()->getRequest()->setControllerName(new Http_UriPartEntity($map['controller']));
                $this->context->getRequest()->getRequest()->setUri($uri);

                if (isset($map['additional']))
                {
                    $this->context->getRequest()->getRequest()->setData($map['additional']);
                }

                $this->context->getRequest()->getRequest()->setFrontend(
                    isset($map['is_frontend']) ? (int) $map['is_frontend'] : 0
                );

                return true;
            }
        }

        return false;
    }

    /**
     * �� ������� "/" ��������� URI-������ $uri ����� �������,
     * ��� ������ ����� ������������ ��� ������� �������� �������� ����
     * ���� "��������" => "��������". ������ ���� ���������� � Request.
     * ������ ���� �������� ������ ������ � ������ �����������.
     * �������� URI-������ ����:
     *
     * /ajax/region/country/155
     *
     * ����� ��������� ����� �������, ��� ��� ������� ���������������� �����
     * � ������, � Request ����� �������� ���������� � ������� ������ Ajax,
     * ����������� Region � ���������� ������� country �� ��������� 155.
     *
     * @param string URI-������
     * @return boolean true ���� ��� ������� $uri ������ ����������
     *                 � false � ��������� ������.
     */
    private function compareRequestWithStandartUriMap($uri)
    {
        $uri_parts = explode('/', trim($uri, ' /'));

        $count_params = count($uri_parts);

        if ($count_params % 2)
        {
            return false;
        }

        for ($i=0; $i<$count_params; $i++)
        {
            $params[$uri_parts[$i]] = $uri_parts[++$i];
        }

        $first_element = Base_Array::array_kshift($params);

        list($module, $controller) = each($first_element);

        try
        {   // ������� ��������, ������� ����� ������� ������������ try-catch
            if (class_exists($this->getControllerClassName(
                Http_UriPartEntity::formatToCamelCaseStyle($module),
                Http_UriPartEntity::formatToCamelCaseStyle($controller)
               )))
            {
                $this->context->getRequest()->getRequest()->setModuleName(new Http_UriPartEntity($module));
                $this->context->getRequest()->getRequest()->setControllerName(new Http_UriPartEntity($controller));
                $this->context->getRequest()->getRequest()->setUri($uri);
                $this->context->getRequest()->getRequest()->setData($params);

                return true;
            }
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * ���������� ��� ������ �����������.
     *
     * @param string $module ��� ������
     * @param string $controller ��� �����������
     * @return string
     */
    private function getControllerClassName($module, $controller)
    {
        return 'Module_' . ucfirst($module) . '_Controller_' . ucfirst($controller);
    }
}