<?php
/**
 * �����-������� ��� ��������� ��� ������ �� ��������.
 */
class Base_String
{
    /**
     * ��������� �������� �� "�������".
     * ���������� true, ���� �������� ����� - �������� ������ ������,
     * false ��� null. ����������� ��� �����������, ��� ��������
     * ������ �� REQUEST.
     *
     * @param string
     * @return boolean
     */
    public static function isEmpty($string)
    {
        if (!is_numeric($string))
        {
            return empty($string);
        }

        return false;
    }

    /**
     * ���������� �������� ������������� ������,
     * ������� �������� ����� �� �������� �������������
     * ������� ������ � PHP.
     *
     * @param string
     * @return int
     */
	public static function getBytes($val)
	{
        $val = str_replace(' ', '', $val);

        if ($val === '0')
        {
            return 0;
        }

        $last = strtolower($val[strlen($val)-1]);

        switch ($last)
        {
	       case 'g':
	           $val *= 1024;

	       case 'm':
		      $val *= 1024;

	       case 'k':
		      $val *= 1024;
        }

		return $val;
	}

    /**
     * ���������� ���������� ������ ������� 32 �������.
     *
     * @param void
     * @return string
     */
    public static function getUnique($length=null)
    {
        $length = intval($length);

        if (!$length || $length > 32)
        {
            $length = 32;
        }

        return substr(md5(microtime().rand(1, 10000000)), 0, $length);
    }
}