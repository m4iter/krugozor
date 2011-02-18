<?php
/**
 * �����-�������� ��� Request.
 */
class Http_Cover_Request extends Cover_Array
{
    /**
     * ������ ������� REQUEST_URI
     *
     * @var string
     */
    private $request_uri;

    /**
     * ������-������� ��� ������ ������.
     *
     * @var Http_UriPartEntity
     */
    private $module_name;

    /**
     * ������-������� ��� ������ �����������.
     *
     * @var Http_UriPartEntity
     */
    private $controller_name;

    /**
     * ����, ����������� �������� �� ������� ������
     * Frontend-��.
     *
     * @var bool
     * @todo: ������, ������ ������� - ����������� ���� ����� �� ����� �����������
     */
    private $is_frontend;

    public function getModuleName()
    {
        return $this->module_name;
    }

    public function setModuleName(Http_UriPartEntity $name)
    {
        if ($this->module_name === null)
        {
            $this->module_name = $name;
        }
    }

    public function getControllerName()
    {
        return $this->controller_name;
    }

    public function setControllerName(Http_UriPartEntity $name)
    {
        if ($this->controller_name === null)
        {
            $this->controller_name = $name;
        }
    }

    public function isFrontend()
    {
        return $this->is_frontend;
    }

    public function setFrontend($bool)
    {
        if ($this->is_frontend === null)
        {
            $this->is_frontend = (boolean)$bool;
        }
    }

    public function getUri()
    {
        return $this->request_uri;
    }

    public function setUri($request_uri)
    {
        if ($this->request_uri === null)
        {
            $this->request_uri = $request_uri;
        }
    }
}