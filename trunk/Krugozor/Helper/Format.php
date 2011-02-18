<?php
/**
 * �����-������ ��� �������������� ������, ��������� � �������.
 * ���������� ����� �� �������������� ���� ��������:
 *
 * $format = Helper_Format::getInstance();
 * echo $format->run('string [b]bold "\'string[/b] string', 'bb2html');
 * echo $format->run('    string <a href="javascript:while(1) alert(1)">xss</a> string   ');
 * echo $format->run("copy;\n&amp;\n\n\n\n\n\n\n\n\n\n", 'entDec', 'nl2br', 'trim');
 *
 * print_r($format->run(array('test [b]bold[/b] text', array('test [b]bold[/b] text', 'test [b]bold[/b] text')), 'bb2html'));
 *
 * echo Helper_Format::correctHttpUrl('www.yandex.ru');
 * echo Helper_Format::triumviratForm(15, array('�����', '�����', '������'));
 */
class Helper_Format
{
    private static $instance;

    /**
     * ��������������� ������ ������� � �� ������������������� � ����������.
     *
     * ���� � ����� run() ��������� ������ ���� �������� - ������ ��� ��������������,
     * �� ������ �������������� ��������������� ��������, ����������� � ���� �������.
     * ���� � ����� run(), ����� ������, ���������� ��� � �����
     * ����������� �������/������� PHP, �������� ����� ���������� ������,
     * �� ��� ��� �� ���������� �������� ������������������,
     * ����������� � �������, ��� �����������
     * �� ����, � ����� ������������������ ��� ���������� � ������ ������ run().
     *
     * @access protected
     * @var array
     * @todo: entDec - ����� �� �� ��� ������������ ����� ��� ����� ��������������?
     */
    protected static $default_methods = array('trim', 'entDec', 'hsc', 'nl2br');

    public static function getInstance()
    {
        if (!self::$instance)
        {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * �������� ����� ������, ������� ������������ ���������� ����������� �������� ������.
     * ��������� ���������� (������ ��� ������),
     * ������� ���������� ���������� � �� 0 �� N ���������� -
     * ����� ������� ��� �������, �������� ����� ���������� ����������.
     *
     * ������ �������������:
     *
     *    $var = $myDB->run($var, "hsc");              - ��������� ������ ����� hsc � ���������� var.
     *    $var = $myDB->run($var, "hsc", "entDec");    - ��������� � ���������� var ������ hsc � entDec.
     *    $var = $myDB->run($var);                     - ��������� � ���������� var ��� �������� ������
     *                                                   ������������� � ������� self::$default_methods.
     *    $var = $myDB->run($var, "nl2br");            - ��������� *�����������* ������� PHP nl2br
     *                                                   � ���������� var.
     * @access public
     * @param mixed �������������� ���������� � ���� ������ ��� ������� � ����� ������� ��������������
     * @return mixed
     */
    public function run()
    {
        $c = func_num_args();

        // ���� ���-�� ���������, ���������� � �������, ����� 1,
        // ������, ������������ � ������� self::$default_methods, � ������,
        // �������� ����������, ���������� ���� �������.
        if (1 === $c)
        {
            $arg_list = self::$default_methods;
            $temp = func_get_args();
            array_unshift($arg_list, $temp[0]);
            $c = count($arg_list);
        }
        else
        {
            $arg_list = func_get_args();
        }

        // ��������, ������� ���������� ����������.
        $in = array_shift($arg_list);

        // ���� ���������� ����� ��� ��������� BB-����� self::bb2html(),
        // �� ����� ������� � hsc, ���� ��� ����!
        if (in_array('bb2html', $arg_list) && !in_array('hsc', $arg_list))
        {
            array_unshift($arg_list, 'hsc');
            $c++;
        }

        // �������� ����� �������, ����� ����� format ������� � ���������� ������������������� ��� ����������,
        // ������� �������� ������������ � ������� ������������������ ���������� � ������� self::$default_methods.
        // ��������, ���: $out->run($string, 'hsc', 'entDec') - �����, �� ����,
        // ��� �������� ������ ��������� entDec() ����� hsc().
        // ��� ����� � ���������� ����������� ���: �� ��������� ������ ���������� � ��� �������,
        // � ������� ��� ���������� � ������� self::$default_methods.
        // ������, ���� ����� ����� ������ � ������������������� ('hsc', 'entDec'),
        // �� ��� ��� ����������� � ���������� �������, �.�. ���: ('entDec', 'hsc', ...).
        // ������, �� ������������� � self::$default_methods ��������� ������ � �����
        // �������.

        foreach (self::$default_methods as $key => $method)
        {
            if (($index = array_search($method, $arg_list)) !== FALSE && $index !== $key)
            {
                    for ($j=0; $j<count($arg_list); $j++)
                    {
                        if (in_array($arg_list[$j], self::$default_methods))
                        {
                            $temp = $arg_list[$j];
                            $arg_list[$j] = $arg_list[$index];
                            $arg_list[$index] = $temp;
                        }
                    }
            }
        }

        /* todo: ������ ���� ���?
        $i = 0;

        foreach (self::$default_methods as $v)
        {
            // ���� ����� ����� ������� � ������ ���������� $arg_list, �.�. �� ����������, ��
            // ��������� ���� ����� � ������, � �� ������������������, � ������� �� ������ ����.
            // �.�. ���� � $arg_list ����� hsc ��� ����� bb2html, �� ��� ��������� ��������,
            // ������ ��� ��� ������ �������.
            if (($index = array_search($v, $arg_list)) !== FALSE)
            {
                $temp = $arg_list[$i];
                $arg_list[$i] = $arg_list[$index];
                $arg_list[$index] = $temp;
                $i++;
            }
        }*/

        for ($i=0; $i<$c-1; $i++)
        {
            $in = $this->gotoArray($in, $arg_list[$i]);
        }

        return $in;
    }

    /**
     * ����������� ���������� ������, ������� ����
     * �������� �� ����������������� �����.
     * �������� html-����, ����� ���� ��������� ������� $this->run()
     * � ����������� �� ���������.
     *
     * @param mixed
     * @return mixed
     */
    public function userDataOutput($value)
    {
        return $this->run(strip_tags($value));
    }

    /**
     * ������ html_entity_decode.
     * ����������� HTML �������� � ������ $string � ��������������� �������.
     *
     * @access public
     * @param string
     * @return string
     * @static
     */
    public static function entDec($string)
    {
        return html_entity_decode($string, ENT_QUOTES);
    }

    /**
     * ����������� BB ���� � HTML.
     *
     * @param string
     * @return string
     * @static
     */
    public static function bb2html($in)
    {
        $in  = preg_replace("#\\[B\\](.+?)\\[/B\\]#ims", "<strong>$1</strong>", $in);
        $in  = preg_replace("#\\[I\\](.+?)\\[/I\\]#ims", "<em>$1</em>", $in);

        return $in;
    }

    /**
     * �������� ��� ���������� BB-����, ������� ������ �� ���������� bb-����
     * �� ������� ����� ������.
     *
     * @param string
     * @return string
     * @static
     */
    public static function bb2html2strip($in)
    {
        $in  = preg_replace("#\\[B\\]([^\\[\\]]+?)$#ims", "<strong>$1</strong>", $in);
        $in  = preg_replace("#\\[I\\]([^\\[\\]]+?)$#ims", "<em>$1</em>", $in);

        return $in;
    }

    /**
     * ����� ���������� ��������� ������
     * ������� htmlspecialchars.
     *
     * @access public
     * @param string
     * @return string
     * @static
     */
    public static function hsc($in)
    {
        return htmlspecialchars($in, ENT_QUOTES);
    }

    /**
     * �������� ������� ����� ������ ������ ������
     * �� ���� ������ ����� ������, ������ ���������
     * � ������ \r.
     * ����� ����������� ��� �������������� ��������� HTML.
     *
     * @access public
     * @param string
     * @return string
     * @static
     */
    public static function cleanWhitespace($in)
    {
        $in = preg_replace("/(\r?\n)+/", "\n", $in);
        $in = preg_replace("/(\t)+/", '', $in);
        $in = preg_replace("/ +/", ' ', $in);
        return $in;
    }

    /**
     * ������������ URL - �������� �������� http://, ���� �� �������
     * ����� ���� ��������� ��������. ������ �������� ����� ��� ����, ��� ��
     * ��������� ���������� URL �������������,
     * �������� � ���� ��� ��������� ��� � ��������� www.
     *
     * @access public
     * @param string
     * @return string
     * @static
     */
    public static function correctHttpUrl($in)
    {
        return 'http://'.str_ireplace('http://', '', $in);
    }

    /**
     * ����� ��������� ��������� ��������
     * ��� confirm-������ � JavaScript.
     *
     * @access public
     * @param string
     * @return string
     * @static
     */
    public static function confirm($in)
    {
        $in = str_replace("\n", chr(10), $in);
        $in = str_replace("\r", '', $in);
        $in = str_replace("\t", chr(9), $in);

        $in = htmlspecialchars($in, ENT_COMPAT);
        $in = addslashes($in);

        $in = str_replace(chr(10), "\\n", $in);
        $in = str_replace(chr(9), "\\t", $in);

        return $in;
    }

    /**
     * ������� "��������" ����� �� �������� URL-������. ��������, ����� ��������� ������
     * <pre>http://test/admin/user/edit/?id=38&referer=http%3A%2F%2Ftest%2Fadmin%2Fuser%2F</pre>
     * ����� �������� ������ ���� <pre>http://test/admin/article/edit/?id=...%26sep%3D1</pre>
     *
     * @param string $url �����������
     * @param string $simbol ������- ��� ������- �����������
     * @param string $repeat ���������� ���������� $simbol
     * @param int $ml_url_width_prefix ���������� ��������, ����������� �������
     * @param int $ml_url_width_postfix ���������� ��������, ����������� ������
     */
    public static function niceUrlAnchor($url, $width_prefix = 45, $width_postfix = 10, $repeat = 3, $simbol = '.')
    {
        if (strlen($url) > $width_prefix + $width_postfix)
        {
            return substr($url, 0, $width_prefix).str_repeat($simbol, $repeat).substr($url, -$width_postfix);
        }

        return $url;
    }

    /**
     * ����������� ���������� ������ � ���������������� �������
     * ��������� ����������.
     *
     * @param int ���������� ������
     * @param string
     */
    public static function createPhpFormatBytes($val)
    {
        $store = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        for ($i=0, $j = 1024; $val >= $j; $val = $val/$j, $i++);

        return sprintf('%.1f', round($val,2)).(isset($store[$i]) ? $store[$i] : '');
    }

    /**
     * ������� "�������" �������� ������ $str �� �������� $num ��������,
     * ���� ��� ������ ����� $num � ��������� ������ $postfix
     * � ����� ������. ��������� ������ ���� ����� ���������� �������
     * $char � ������.
     *
     * @param string $str �������������� ������
     * @param int $num ������������ ���������� ��������
     * @param string $postfix ������, ������������ � ���������� ������
     * @return string
     * @static
     */
    public static function getPreviewStr($str, $num = 300, $postfix = '&hellip;', $char = ' ')
    {
    	if (strlen($str) > $num)
    	{
    		$str = substr($str, 0, $num);
    		$str = substr($str, 0, strrpos($str, $char));
    		$str .= $postfix;
    	}

    	return $str;
    }

    /**
     * ������� �������� �� ������ URL �������� &notif=...
     * ���� �� ����������� ������� �������.
     *
     * @param string
     * @return string
     * @static
     */
    public static function stripNotifQS($in)
    {
        return preg_replace('/(&|%26|\?|%3F)notif(=|%3D)[0-9]+/', '', $in);
    }

    /**
     * ��������� ��������������� � �������������.
     * ������� ��������� ����� $n � ��� ������ -
     * ������ ����� ������������ ��������� ��������.
     * ����������� �������� ����� ����������.
     * ��������: triumviratForm(100, "�����", "�����", "������")
     * ����� "������".
     *
     * @access public
     * @param int ��������
     * @param array|Cover_Array
     * @return string
     * @static
     */
    public static function triumviratForm($value, $triumvirat_forms)
    {
        $value = abs($value) % 100;
        $value1 = $value % 10;

        if ($value > 10 && $value < 20) {
            return $triumvirat_forms[2];
        }

        if ($value1 > 1 && $value1 < 5) {
            return $triumvirat_forms[1];
        }

        if ($value1 == 1) {
            return $triumvirat_forms[0];
        }

        return $triumvirat_forms[2];
    }

    /**
     * ����� ������������ ���������� $in �������� $fun.
     * ���������� $in ����� ���� ����������� ��������
     * ������ ������ �����������.
     * ��������� ����� ������� ������.
     *
     * @access private
     * @param mixed ���������� ��� ������
     * @param string ��� �������
     * @return mixed
     */
    protected function gotoArray($in, $fun)
    {
        if (!is_array($in))
        {
            if (!function_exists($fun) && method_exists($this, $fun))
            {
                $in = call_user_func(array($this, $fun), $in);
            }
            else if (function_exists($fun))
            {
                $in = $fun($in);
            }
            else
            {
                trigger_error('����� '.$fun.' �� �������� ������� ������ '.__CLASS__.' � �� �������� ��������.', E_USER_WARNING);
            }
        }
        else
        {
            foreach ($in as $k => $v)
            {
                $in[$k] = $this->gotoArray($v, $fun);
            }
        }

        return $in;
    }

    private function __construct(){}
}
?>