<?php
/**
 * Класс-хэлпер для форматирования данных, выводимых в шаблоне.
 * Вызывается любым из представленных ниже способов:
 *
 * $format = Helper_Format::getInstance();
 * echo $format->run('string [b]bold "\'string[/b] string', 'bb2html');
 * echo $format->run('    string <a href="javascript:while(1) alert(1)">xss</a> string   ');
 * echo $format->run("copy;\n&amp;\n\n\n\n\n\n\n\n\n\n", 'entDec', 'nl2br', 'trim');
 *
 * print_r($format->run(array('test [b]bold[/b] text', array('test [b]bold[/b] text', 'test [b]bold[/b] text')), 'bb2html'));
 *
 * echo Helper_Format::correctHttpUrl('www.yandex.ru');
 * echo Helper_Format::triumviratForm(15, array('рубль', 'рубля', 'рублей'));
 */
class Helper_Format
{
    private static $instance;

    /**
     * Предопределённый массив методов и их последовательностей в выполнении.
     *
     * Если в метод run() передаётся только один аргумент - строка для форматирования,
     * то строка обрабатывается последовательно методами, упомянутыми в этом массиве.
     * Если в метод run(), кроме строки, передаются ещё и имена
     * определённых методов/функций PHP, которыми нужно обработать строку,
     * то они так же вызываются согласно последовательности,
     * определённой в массиве, вне зависимости
     * от того, в какой последовательности они определены в вызове метода run().
     *
     * @access protected
     * @var array
     * @todo: entDec - нужен ли он как обязательный метод при общем форматировании?
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
     * Основной метод класса, умеющий обрабатывать переменную несколькими методами подряд.
     * Принимает переменную (строку или массив),
     * которую необходимо обработать и от 0 до N параметров -
     * имена методов или функций, которыми нужно обработать переменную.
     *
     * Пример использования:
     *
     *    $var = $myDB->run($var, "hsc");              - применяет только метод hsc к переменной var.
     *    $var = $myDB->run($var, "hsc", "entDec");    - применяет к переменной var методы hsc и entDec.
     *    $var = $myDB->run($var);                     - применяет к переменной var все основные методы
     *                                                   перечисленные в массиве self::$default_methods.
     *    $var = $myDB->run($var, "nl2br");            - применяет *стандартную* функцию PHP nl2br
     *                                                   к переменной var.
     * @access public
     * @param mixed обрабатываемая переменная в виде строки или массива и имена методов форматирования
     * @return mixed
     */
    public function run()
    {
        $c = func_num_args();

        // Если кол-во аргуметов, переданных в функцию, равно 1,
        // значит, присоеденяем к массиву self::$default_methods, в начало,
        // значение переменной, переданной этой функции.
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

        // Значение, которое необходимо обработать.
        $in = array_shift($arg_list);

        // Если вызывается метод для обработки BB-тегов self::bb2html(),
        // то нужно вызвать и hsc, наче это дыра!
        if (in_array('bb2html', $arg_list) && !in_array('hsc', $arg_list))
        {
            array_unshift($arg_list, 'hsc');
            $c++;
        }

        // Возможен такой вариант, когда метод format вызовут с нелогичной последовательностью тех аргументов,
        // которые являются определёнными в строгой последовательности выполнения в массиве self::$default_methods.
        // Например, так: $out->run($string, 'hsc', 'entDec') - здесь, по сути,
        // нет никакого смысла выполнять entDec() после hsc().
        // Для этого и существует нижестоящий код: он сортирует список аргументов в том порядке,
        // в котором они определены в массиве self::$default_methods.
        // Теперь, если метод будет вызван с последовательностью ('hsc', 'entDec'),
        // то код его отсортирует в нормальном порядке, т.е. так: ('entDec', 'hsc', ...).
        // Методы, не перечисленные в self::$default_methods останутся стоять в своей
        // позиции.

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

        /* todo: убрать этот код?
        $i = 0;

        foreach (self::$default_methods as $v)
        {
            // Если даный метод имеется в списке аргументов $arg_list, т.е. он вызывается, то
            // вставляем этот метод в массив, в ту последовательность, в которой он должен быть.
            // Т.е. если в $arg_list метод hsc идёт после bb2html, то это ошибочная ситуация,
            // меняем эти два метода местами.
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
     * Стандартный обработчик данных, которые были
     * получены от пользовательского воода.
     * Вырезает html-теги, после чего применяет функцию $this->run()
     * с параметрами по умолчанию.
     *
     * @param mixed
     * @return mixed
     */
    public function userDataOutput($value)
    {
        return $this->run(strip_tags($value));
    }

    /**
     * Аналог html_entity_decode.
     * Преобразует HTML сущности в строке $string в соответствующие символы.
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
     * Преобразует BB коды в HTML.
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
     * Заменяем все незакрытые BB-теги, заменяя строку от первичного bb-тега
     * до символа конца строки.
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
     * Метод возвращает результат работы
     * функции htmlspecialchars.
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
     * Заменяет символы новой строки идущие подряд
     * на один символ новой строки, удаляе табуляцию
     * и символ \r.
     * Метод применяется для форматирования конечного HTML.
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
     * Корректирует URL - вырезает протокол http://, если он имеется
     * после чего добавляет протокол. Данная операция нужна для того, что бы
     * корректно отображать URL пользователей,
     * введённых в поля без протокола или с префиксом www.
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
     * Метод формирует строковое значение
     * для confirm-метода в JavaScript.
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
     * Создает "красивый" якорь из длинного URL-адреса. Например, после обработки строки
     * <pre>http://test/admin/user/edit/?id=38&referer=http%3A%2F%2Ftest%2Fadmin%2Fuser%2F</pre>
     * будет получена строка вида <pre>http://test/admin/article/edit/?id=...%26sep%3D1</pre>
     *
     * @param string $url гиперссылка
     * @param string $simbol символ- или строка- заполнитель
     * @param string $repeat количество повторений $simbol
     * @param int $ml_url_width_prefix количество символов, оставляемых спереди
     * @param int $ml_url_width_postfix количество символов, оставляемых позади
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
     * Форматирует количество байтов в человекопонятные единицы
     * измерения информации.
     *
     * @param int количество байтов
     * @param string
     */
    public static function createPhpFormatBytes($val)
    {
        $store = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        for ($i=0, $j = 1024; $val >= $j; $val = $val/$j, $i++);

        return sprintf('%.1f', round($val,2)).(isset($store[$i]) ? $store[$i] : '');
    }

    /**
     * Функция "красиво" обрезает строку $str до максимум $num символов,
     * если она больше числа $num и добавляет строку $postfix
     * в конец строки. Обрезание строки идет после последнего символа
     * $char в строке.
     *
     * @param string $str обрабатываемая строка
     * @param int $num максимальное количество символов
     * @param string $postfix строка, дописываемая к обрезанной строке
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
     * Функция вырезает из строки URL параметр &notif=...
     * Одна из специфичных функций системы.
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
     * Склонение существительных с числительными.
     * Функция принимает число $n и три строки -
     * разные формы произношения измерения величины.
     * Необходимая величина будет возвращена.
     * Например: triumviratForm(100, "рубль", "рубля", "рублей")
     * вернёт "рублей".
     *
     * @access public
     * @param int величина
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
     * Метод обрабатывает переменную $in функцией $fun.
     * Переменная $in может быть многомерным массивом
     * любого уровня вложенности.
     * Служебный метод данного класса.
     *
     * @access private
     * @param mixed переменная или массив
     * @param string имя функции
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
                trigger_error('Метод '.$fun.' не является методом класса '.__CLASS__.' и не является функцией.', E_USER_WARNING);
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