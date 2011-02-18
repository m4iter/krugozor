<?php
/**
 * ������-�������� ����� ������ ��� �����������.
 */
class Http_UriPartEntity
{
    /**
     * ��� ������ ��� ����������� � URI-�����, �.�. � ����, �����
     * ������ ����� �������� ����� �����. ��������:
     * "frontend-registration", "backend-user-edit", "user" � �.�.
     *
     * @var string
     */
    private $uri_style;

    /**
     * ��� ������ ��� ����������� � CamelCase-�����, �.�. � ����, �����
     * ������ ����� �������� ������, � ������ ����� ����� (������� ������)
     * �������� � ������� �����. ��������:
     * "FrontendRegistration", "BackendUserEdit", "User" � �.�.
     *
     * @var string
     */
    private $camel_case_style;

    /**
     * @param string ������ � URI-�����.
     * @return void
     */
    public function __construct($uri_style)
    {
        $this->uri_style = $uri_style;
    }

    /**
     * ���������� ��� ������ ��� ����������� � CamelCase-�����.
     *
     * @param void
     * @return string
     */
    public function getCamelCaseStyle()
    {
        if ($this->camel_case_style === null)
        {
            $this->camel_case_style = self::formatToCamelCaseStyle($this->uri_style);
        }

        return $this->camel_case_style;
    }

    /**
     * ���������� ��� ������ ��� ����������� � URI-�����.
     *
     * @param void
     * @return string
     */
    public function getUriStyle()
    {
        return $this->uri_style;
    }

    /**
     * ����������� ������ $string � CamelCase-�����,
     * ������� ������ ������ ������� �����.
     *
     * @static
     * @param string
     * @return string
     */
    public static function formatToCamelCaseStyle($string)
    {
        $parts = preg_split('~-|_~', $string);

        if (count($parts) <= 1)
        {
            return ucfirst($string);
        }

        $str = '';

        foreach ($parts as $part)
        {
            $str .= ucfirst($part);
        }

        return $str;
    }
}