<?php
/**
* Класс "Навигация".
*
* Класс предназначен для получения переменных, которые используются для
* формирования HTML-шаблона строки навигации при постраничном выводе
* однородной структуры данных (записи из таблицы БД),
* а так же для получения двух переменных - $start_Limit и $stop_Limit, которые
* влияют на результат выборки.
* Данные переменные нужно использовать в SQL-запросе, в операторе LIMIT:
* mysql_query("SELECT ... FROM ... LIMIT $start_Limit, $stop_Limit ...");
*
* Строка навигации может иметь такую структуру:
*  <<< << <  10 11 12 13 14 15  > >> >>>
* где:
*         '<<<' и '>>>'                          - ссылки перехода на начало и конец данных вывода.
*         '<<' и '>>'                            - ссылки перехода на предыдущий и следующий блок
*                                                  страниц (блок ссылок на страницы).
*         '<' и '>'                              - ссылки перехода на предыдущую и следующую страницу.
*         '10 11 12 13 14 15'                    - блок страниц (блок ссылок на страницы).
*         '10', '11', '12', '13', '14' и '15'    - (ссылки на) страницы.
*
* Тип вывода записей, в возрастающем или же в убывающем порядке (ASC и DESC),
* должен определяется непосредственно в SQL-запросе на выборку данных.
* Данный класс не влияет на тип выборки, он лишь формирует числовое начало
* ($start_Limit) и конец ($stop_Limit).
*
* Выводящиеся записи могут иметь визуальную нумерацию как в убывающем, так и в возрастающем порядке.
* Для этого используется переменная autodecrement_num и autoincrement_num.
* autodecrement_num в цикле вывода записей нужно автодекрементировать,
* а autoincrement_num соответственно автоинкрементировать, после чего,
* выводить как порядковый номер записи.
* Пример:
*         for ($i=0, $t=$autodecrement_num; $i < $count; $i++, $t--) {
*             echo "Запись № $t ...";    // нумерация в убывающем порядке
*         }
*
* При инициализации класс приинимает
*      2 обязательных параметра:
*           $in                 - Максимальное количество записей,
*                                 которые необходимо выводить на ОДНОЙ СТРАНИЦЕ.
*           $num_blocks         - На сколько БЛОКОВ нужно разделить ВСЕ ДАННЫЕ.
*                                 Количество СТРАНИЦ В БЛОКЕ будет равно $num_blocks / $in.
*
*       3 необязательнх параметра:
*           $var_page_name      - Имя ключа переменной, из глобального ассоциативного
*                                 массива $array_name (см. далее),
*                                 указывающей СТРАНИЦУ, т.е. набор записей от N до N+$in,
*                                 который выводится в одном выводе.
*                                 Например, записи из набора данных, начиная с 11 и кончая 20.
*                                 По умолчанию имя этого ключа - "page".
*           $var_separator_name - Имя ключа переменной из глобального ассоциативного массива
*                                 $array_name (см. далее), указывающая БЛОК СТРАНИЦ,
*                                 т.е. набор записей от N1 до N2, которые являются
*                                 абстрактной сущьностью -
*                                 НАБОРОМ СТРАНИЦ (набором ссылок на страницы).
*                                 По умолчанию имя этого ключа - "sep".
*           $array_name         - Имя глобального массива - либо "REQUEST" для $_REQUEST,
*                                 либо "GET" для $_GET, либо "POST" для $_POST.
*                                 По умолчанию - "REQUEST".
*
* После инициализации и получаения переменных $start_limit и $stop_limit необходимо
* установить значение $this->all_count с помощью метода setCount(), передав
* в него значение - количество записей в базе.
*/
class Base_Navigation
{
    /**
	* Максимальное количество записей, которые необходимо выводить на ОДНОЙ СТРАНИЦЕ.
	* Один из аргументов конструктора.
	* @var int
	* @access private
    */
	private $limit;

	/**
	* Номер текущей страницы.
	* @var int
	* @access private
	*/
    private $currentPage;

    /**
	* Текущий сепаратор.
	* @var int
	* @access private
	*/
    private $currentSep;

    /**
	* Начальное значение для SQL-оператора LIMIT.
	* @var int
	* @access private
	*/
    private $startLimit;

    /**
	* Конечное значение для SQL-оператора LIMIT.
	* @var int
	* @access private
	*/
    private $stopLimit;

    /**
	* На сколько БЛОКОВ нужно разделить ВСЕ ДАННЫЕ.
	* Один из аргументов конструктора.
	* @var int
	* @access private
	*/
    private $plimit;

    /**
	* Общее количество записей в таблице БД.
	* @var int
	* @access private
	*/
    private $all_count;

    /**
	* Количество страниц (ссылок на страницы), на которые БУДЕТ разделён 1 блок.
	* @var int
	* @access private
	*/
    private $pages;

    /**
	* Количество страниц вообще.
	* @var int
	* @access private
	*/
    private $all_pages;

    /**
	* Количество блоков, на которые БУДЕТ разделена БД.
	* @var int
	* @access private
	*/
	private $blocks;

	/**
	* Имя GET-переменной URI-запроса, которая будет указывать страницу.
	* @var int
	* @access private
	*/
	private $var_page_name;

	/**
	* Имя GET-переменной URI-запроса, которая будет указывать блок страниц.
	* @var int
	* @access private
	*/
	private $var_separator_name;

    /**
	* Конструктор класса.
	* Инициализирует все необходимые переменные, а также
	* указывает значения переменных для подстановки в SQL-оператор LIMIT.
	* @access public
	* @param int максимальное количество записей, которые необходимо выводить на ОДНОЙ СТРАНИЦЕ.
	* @param int на сколько БЛОКОВ нужно разделить ВСЕ ДАННЫЕ.
	* @param string имя глобального ассоциативного массива ("GET", "POST" или "REQUEST").
	* @param string имя ключа переменной, из глобального ассоциативного массива,
	* указывающей СТРАНИЦУ.
	* @param string имя ключа переменной из глобального ассоциативного массива, указывающей БЛОК СТРАНИЦ
	* @return void
	*/
    public function __construct($limit, $num_blocks, $var_page_name = "page", $var_separator_name = "sep", $array_name = "REQUEST")
    {
        // Максимальное количество записей,
        // которые необходимо выводить на ОДНОЙ СТРАНИЦЕ
        $this->limit = intval($limit);

        // На сколько блоков НУЖНО разделить БД.
        $this->plimit = intval($num_blocks);

        // Количество страниц, на которые БУДЕТ разделён 1 блок.
        $this->pages = ceil($this->plimit/$this->limit);

		$this->var_page_name = $var_page_name;
		$this->var_separator_name = $var_separator_name;

        $array_name = "_".ltrim($array_name, "_");
        $array_name = eval("return \$$array_name;");

        // Определяем текущий сепаратор.
        $this->currentSep = ( isset($array_name[$var_separator_name]) && is_numeric($array_name[$var_separator_name]) )
                              ? intval($array_name[$var_separator_name])
                              : 1;

        // Определяем текущую страницу $_REQUEST[page]
        $this->currentPage = !isset($array_name[$var_page_name])
                             ?
                             ($this->currentSep-1) * $this->pages + 1
                             :
                             intval($array_name[$var_page_name]);

        $this->startLimit = ($this->currentPage-1) * $this->limit;        //0, 10, 20
        $this->stopLimit  = $this->limit;                                 //10, 10, 10
    }

    /**
    * Возвращает начальное значение для SQL-оператора LIMIT.
    * @access public
    * @param void
    * @return int начальное значение для SQL-оператора LIMIT
    */
    public function getStartLimit()
    {
        return $this->startLimit;
    }

    /**
    * Возвращает конечное значение для SQL-оператора LIMIT.
    * @access public
    * @param void
    * @return int конечное значение для SQL-оператора LIMIT
    */
    public function getStopLimit()
    {
        return $this->stopLimit;
    }

    /**
     * Возвращает общее количество записей.
     * @access public
     * @param void
     * @return int
     */
    public function getCount()
    {
        return $this->all_count;
    }

    /*
    * Принимает числовое значение - общее количество записей в базе,
    * а также вычисляет все необходимые переменные для
    * формирования строки навигации.
    * @access int количество записей в базе
    * @param void
    * @return void
    */
    public function setCount($all_count)
    {
        // Общее кол-во записей в БД на текущий момент.
        $this->all_count = intval($all_count);

        // Количество страниц вообще.
        $this->all_pages = ceil($this->all_count/$this->limit);
        // Количество блоков, на которые БУДЕТ разделена БД.
        $this->blocks = ceil($this->all_pages/$this->pages);
        // Если количество блоков больше всех страниц, то
        // за количество блоков берём количество всех страниц.
        $this->blocks = ($this->blocks > $this->all_pages) ? $this->all_pages : $this->blocks;

        // Сколько записей ДОЛЖНО быть при данном количестве $this->blocks, что бы они полность заполнили станицы.
        // Т.е. теоретически, для двух блоков, выводящих по 3 записи, общее количество записей должно быть равно 6.
        $this->teoretic_max_count = $this->limit * $this->all_pages;

        // Основной массив значений для вывода в шаблоне.
        $this->table = array();
        // номер текущего блока
        $k = ($this->currentSep-1) * $this->pages + 1;

            for ($j=$k, $i=$k; $i<$this->pages+$j && $i<=$this->all_pages; $i++)
            {
                $temp = ( $this->all_count - (($i-1) * $this->limit) );
                $temp2 = ($temp - $this->limit > 0) ? $temp - $this->limit + 1 : 1;

                $temp3 = $this->startLimit + 1;
                $temp4 = ($this->teoretic_max_count > $this->all_count && $k==$this->blocks)
                          ? $this->all_count
                          : $this->stopLimit * $j;

                $this->table[] = array
                (
                    'page' => $i,
                    'separator' => $this->currentSep,
                    // Это якори, которые можно использовать при убывающем и возростающем визуальном выводе
                    "decrement_anhor" => ($temp == $temp2 ? $temp : $temp." - ".$temp2),
                    "increment_anhor" => ($temp3 == $temp4 ? $temp3 : $temp3." - ".$temp4)
                );
            }

        return $this;
    }

    /**
    * Возвращает переменную, которая в цикле вывода, при при автодекременте,
    * будет отображать числовой id записи в структуре массива данных.
    * @access public
    * @param void
    * @return int переменная автодекремента
    */
    public function getAutodecrementNum()
    {
        return $this->all_count - $this->startLimit;
    }

    /**
    * Возвращает переменную, которая в цикле вывода, при при автоинкременте,
    * будет отображать числовой id записи в структуре массива данных.
    * @access public
    * @param void
    * @return int переменная автоинкремента
    */
    public function getAutoincrementNum()
    {
        return $this->limit * ($this->currentPage-1) + 1;
    }

    /**
    * Возвращает номер сепаратора "sep" для формирования ссылки перехода на предыдущий блок страниц (<<).
    * @access public
    * @param void
    * @return int номер сепаратора предыдущего блока страниц
    */
    public function getLastBlockSeparator()
    {
        $a = $this->currentSep - 1;
        return $a ? $a : 0;
    }

    /**
    * Возвращает номер сепаратора "sep" для формирования ссылки перехода на следующий блок страниц (>>).
    * @access public
    * @param void
    * @return int номер сепаратора следующего блока страниц
    */
    public function getNextBlockSeparator()
    {
        if ($this->currentSep < $this->blocks) {
            return $this->currentSep + 1;
        }

        return 0;
    }

    /**
    * Возвращает номер сепаратора "sep" для формирования ссылки перехода на последнюю страницу (>>>).
    * @access public
    * @param void
    * @return int номер сепаратора последнего блока страниц
    */
    public function getLastSeparator()
    {
        return $this->blocks;
    }

    /**
    * Возвращает номер страницы "page" для формирования ссылки перехода на последнюю страницу (>>>).
    * @access public
    * @param void
    * @return int номер последней страницы
    */
    public function getLastPage()
    {
        return $this->all_pages;
    }

    /**
    * Возвращает многомерный массив для цикла вывода в шаблоне (см. шаблон).
    * В данном массиве хранятся перемнные, используемые для формироания
    * числовой навигации.
    * Под ключом "page" N-ного элемента массива содержится числовой номер страницы.
    * Под ключом "separator" хранится величина, одинаковая для всех N-ных элементов
    * массиа - текущий сепаратор.
    * Ключ "decrement_anhor" это строковое представление, обозначающее величину
    * якоря гиперссылки при формировани циклом автодекрементного значения, например: "40 - 30", "30 - 20".
    * Ключ "increment_anhor" это строковое представление, обозначающее величину
    * якоря гиперссылки при формировани циклом автоинкрементного значения, например: "10 - 20", "20 - 30".
    * @access public
    * @param void
    * @return array
    */
    public function getPagesArray()
    {
        return $this->table;
    }

    /**
    * Возвращает сценарию текущую страницу.
    * @access public
    * @param void
    * @return int номер текщей страницы
    */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
    * Возвращает сценарию текущий сепаратор.
    * @access public
    * @param void
    * @return int номер текущего сепаратора
    */
    public function getCurrentSeparator()
    {
        return $this->currentSep;
    }

    /**
    * Возвращает номер сепаратора "sep" для формирования ссылки перехода на предыдущую страницу (<).
    * @access public
    * @param void
    * @return int номер сепаратора предыдущей страницы
    */
    public function getLastPageSeparator()
    {
        //    Текущий сепаратор, определённый програмно
        $cs = ceil($this->currentPage/$this->pages);
        //    Определяем сепаратор страницы currentPage - 1
        $cs2 = ceil(($this->currentPage-1)/$this->pages);

        //    Если сепаратор страницы currentPage - 1 меньше текущего сепаратора,
        //    значит страница currentPage - 1 относится к следующему блоку с сепаратором $cs2
        if ($cs2 < $cs) {
            return $cs2;
        }

        return $cs;
    }

    /**
    * Возвращает номер сепаратора "sep" для формирования ссылки перехода на следующую страницу (>).
    * @access public
    * @param void
    * @return int номер сепаратора следующей страницы
    */
    public function getNextPageSeparator()
    {
        //    Текущий сепаратор, определённый програмно.
        $cs = ceil($this->currentPage/$this->pages);
        //    Определяемсепаратор страницы currentPage + 1.
        $cs2 = ceil(($this->currentPage+1)/$this->pages);

        //    Если сепаратор страницы currentPage + 1 больше текущего сепаратора,
        //    значит страница currentPage + 1 относится к следующему блоку с сепаратором $cs2.
        if ($cs2 > $cs) {
            return $cs2;
        }

        return $cs;
    }

    /**
    * Возвращает номер страницы "page" для формирования ссылки перехода на предыдущую страницу (<).
    * @access public
    * @param void
    * @return int номер предыдущей страницы
    */
    public function getLastPagePage()
    {
        $a = $this->currentPage - 1;
        return $a ? $a : 0;
    }

    /**
    * Возвращает номер страницы "page" для формирования ссылки перехода на следующую страницу (>).
    * @access public
    * @param void
    * @return int номер следующей страницы
    */
    public function getNextPagePage()
    {
        return $this->currentPage < $this->all_pages ? $this->currentPage+1 : 0;
    }

    /**
    * Возвращает имя сепаратора.
    * @access public
    * @param void
    * @return string
    */
    public function getSeparatorName()
    {
        return $this->var_separator_name;
    }

    /**
    * Возвращает имя страницы.
    * @access public
    * @param void
    * @return string
    */
    public function getPageName()
    {
        return $this->var_page_name;
    }
}
?>