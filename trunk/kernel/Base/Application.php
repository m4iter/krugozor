<?php
class Base_Application
{
    /**
     * ��� ���������� �������, ���������� ����������� URI.
     * ������ ��� ��������� �������� ����������� REQUEST_URI, ���������� ��
     * � ������� ��������� mod_rewrite-a.
     *
     * @var string
     */
    const REQUEST_PATH = '_path';

    /**
     * ������-�������� �������.
     *
     * @var Http_Request
     */
    private $request;

    /**
     * ������-�������� ������.
     *
     * @var Http_Response
     */
    private $response;

    /**
     * ������ ���������� URL-������� �������.
     *
     * @var array
     */
    private $maps = array();

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
     * ��������� ������ ���������� ���� URL.
     *
     * @param array
     * @return Base_Application
     */
    public function setMaps(array $maps)
    {
        $this->maps = $maps;

        return $this;
    }

    /**
     * �������� ����� ����������.
     *
     * @param void
     * @return void
     */
    public function run()
    {
        try
        {
            $uri = $this->request->getRequest(self::REQUEST_PATH);

            if (!$this->compareRequestWithUriMaps($uri))
            {
                if (!$this->compareRequestWithStandartUriMap($uri))
                {
                    $this->response->clearHeaders();
                    $this->request->getRequest()->setModuleName(new Http_UriPartEntity('404'));
                    $this->request->getRequest()->setControllerName(new Http_UriPartEntity('404'));
                }
            }

            $controller_name = $this->getControllerClassName(
                                   $this->request->getRequest()->getModuleName()->getCamelCaseStyle(),
                                   $this->request->getRequest()->getControllerName()->getCamelCaseStyle()
                               );

            $controller = new $controller_name($this->request, $this->response);
            $result = $controller->run();

            if (!is_object($result))
            {
                throw new LogicException('��� ����������');
            }

            if ($result instanceof Base_View)
            {
	            // ���� � ������� ������������ notif, ������ ���������� �������� �� view
	            // ����������, ���������� � ���������� �������� � ������� � �� ������.
	            // @todo: ������� ��� � ����������, ���� �����
	            if ($this->request->getRequest('notif', 'decimal'))
	            {
	                $this->redirect = new Base_Redirect();

	                $this->redirect->findById($this->request->getRequest('notif', 'decimal'));

	                if ($this->redirect->getId())
	                {
	                    $result->setRedirect($this->redirect);
	                }
	            }

	            $result->run();

	            $this->response->sendHeaders();

                echo $result->getOut();

                if (Base_Registry::getInstance()->config['enabled_debug_info'] ||
	                isset($this->request->getRequest()->aaa))
	            {
	                echo $controller->getDebugInformation();
	            }
            }
            else if ($result instanceof Base_Redirect)
            {
                $this->response->setHeader('Location', $result->getRedirectUrl());

                $this->response->sendCookie();

                $this->response->sendHeaders();
            }
            else if ($result instanceof Module_Common_Model_ImagePng)
            {
                $this->response->sendHeaders();

                imagepng($result->getGdResource());
            }
        }
        catch (Exception $e)
        {
            $this->response->sendHeaders();

            echo '<div style="padding:10px"><h3>��������� ������:</h3><p>' . $e->getMessage() .
            '</p><p><pre>' . print_r($e->getTraceAsString(), 1) . '</pre></p></div>';
        }
    }

    /**
     * ��������� ������� URI-������, ������� ���������� � �������� ���������,
     * � ���������� ��� � ����� �� ��������� URL-����� $this->maps.
     * ���� ���������� �������, �� � ������-�������� Request ������������ ����������
     * �� ����, ����� ���
     * - ��� ������
     * - ��� ����������
     * - ���������� URI-�����
     * - ��������� �������.
     *
     * @param string URI-������
     * @return boolean true ���� ��� ������� $uri ������� ���������� � $this->maps
     *                 � false � ��������� ������.
     * @todo: ������� �������� �� ���������� ������� ����������� � ����� $this->maps �����������.
     */
    private function compareRequestWithUriMaps($uri)
    {
        foreach ($this->maps as $map)
        {
            if (preg_match($map['pattern'], $uri, $params))
            {
                array_shift($params);

                foreach ($params as $index => $value)
                {
                    $this->request->getRequest()->{$map['aliases'][$index]} = $value;
                }

                $this->request->getRequest()->setModuleName(new Http_UriPartEntity($map['module']));
                $this->request->getRequest()->setControllerName(new Http_UriPartEntity($map['controller']));
                $this->request->getRequest()->setUri($uri);

                if (isset($map['additional']))
                {
                    $this->request->getRequest()->setData($map['additional']);
                }

                $this->request->getRequest()->setFrontend(
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
                $this->request->getRequest()->setModuleName(new Http_UriPartEntity($module));
                $this->request->getRequest()->setControllerName(new Http_UriPartEntity($controller));
                $this->request->getRequest()->setUri($uri);
                $this->request->getRequest()->setData($params);

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
     * @param $module ��� ������
     * @param $controller ��� �����������
     * @return string
     */
    private function getControllerClassName($module, $controller)
    {
        return 'Module_'.ucfirst($module).'_Controller_'.ucfirst($controller);
    }
}