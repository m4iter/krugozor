<?php
/**
* ����� "���������".
*
* ����� ������������ ��� ��������� ����������, ������� ������������ ���
* ������������ HTML-������� ������ ��������� ��� ������������ ������
* ���������� ��������� ������ (������ �� ������� ��),
* � ��� �� ��� ��������� ���� ���������� - $start_Limit � $stop_Limit, �������
* ������ �� ��������� �������.
* ������ ���������� ����� ������������ � SQL-�������, � ��������� LIMIT:
* mysql_query("SELECT ... FROM ... LIMIT $start_Limit, $stop_Limit ...");
*
* ������ ��������� ����� ����� ����� ���������:
*  <<< << <  10 11 12 13 14 15  > >> >>>
* ���:
*         '<<<' � '>>>'                          - ������ �������� �� ������ � ����� ������ ������.
*         '<<' � '>>'                            - ������ �������� �� ���������� � ��������� ����
*                                                  ������� (���� ������ �� ��������).
*         '<' � '>'                              - ������ �������� �� ���������� � ��������� ��������.
*         '10 11 12 13 14 15'                    - ���� ������� (���� ������ �� ��������).
*         '10', '11', '12', '13', '14' � '15'    - (������ ��) ��������.
*
* ��� ������ �������, � ������������ ��� �� � ��������� ������� (ASC � DESC),
* ������ ������������ ��������������� � SQL-������� �� ������� ������.
* ������ ����� �� ������ �� ��� �������, �� ���� ��������� �������� ������
* ($start_Limit) � ����� ($stop_Limit).
*
* ����������� ������ ����� ����� ���������� ��������� ��� � ���������, ��� � � ������������ �������.
* ��� ����� ������������ ���������� autodecrement_num � autoincrement_num.
* autodecrement_num � ����� ������ ������� ����� ��������������������,
* � autoincrement_num �������������� ��������������������, ����� ����,
* �������� ��� ���������� ����� ������.
* ������:
*         for ($i=0, $t=$autodecrement_num; $i < $count; $i++, $t--) {
*             echo "������ � $t ...";    // ��������� � ��������� �������
*         }
*
* ��� ������������� ����� ����������
*      2 ������������ ���������:
*           $in                 - ������������ ���������� �������,
*                                 ������� ���������� �������� �� ����� ��������.
*           $num_blocks         - �� ������� ������ ����� ��������� ��� ������.
*                                 ���������� ������� � ����� ����� ����� $num_blocks / $in.
*
*       3 ������������� ���������:
*           $var_page_name      - ��� ����� ����������, �� ����������� ��������������
*                                 ������� $array_name (��. �����),
*                                 ����������� ��������, �.�. ����� ������� �� N �� N+$in,
*                                 ������� ��������� � ����� ������.
*                                 ��������, ������ �� ������ ������, ������� � 11 � ������ 20.
*                                 �� ��������� ��� ����� ����� - "page".
*           $var_separator_name - ��� ����� ���������� �� ����������� �������������� �������
*                                 $array_name (��. �����), ����������� ���� �������,
*                                 �.�. ����� ������� �� N1 �� N2, ������� ��������
*                                 ����������� ���������� -
*                                 ������� ������� (������� ������ �� ��������).
*                                 �� ��������� ��� ����� ����� - "sep".
*           $array_name         - ��� ����������� ������� - ���� "REQUEST" ��� $_REQUEST,
*                                 ���� "GET" ��� $_GET, ���� "POST" ��� $_POST.
*                                 �� ��������� - "REQUEST".
*
* ����� ������������� � ���������� ���������� $start_limit � $stop_limit ����������
* ���������� �������� $this->all_count � ������� ������ setCount(), �������
* � ���� �������� - ���������� ������� � ����.
*/
class Base_Navigation
{
    /**
	* ������������ ���������� �������, ������� ���������� �������� �� ����� ��������.
	* ���� �� ���������� ������������.
	* @var int
	* @access private
    */
	private $limit;

	/**
	* ����� ������� ��������.
	* @var int
	* @access private
	*/
    private $currentPage;

    /**
	* ������� ���������.
	* @var int
	* @access private
	*/
    private $currentSep;

    /**
	* ��������� �������� ��� SQL-��������� LIMIT.
	* @var int
	* @access private
	*/
    private $startLimit;

    /**
	* �������� �������� ��� SQL-��������� LIMIT.
	* @var int
	* @access private
	*/
    private $stopLimit;

    /**
	* �� ������� ������ ����� ��������� ��� ������.
	* ���� �� ���������� ������������.
	* @var int
	* @access private
	*/
    private $plimit;

    /**
	* ����� ���������� ������� � ������� ��.
	* @var int
	* @access private
	*/
    private $all_count;

    /**
	* ���������� ������� (������ �� ��������), �� ������� ����� ������� 1 ����.
	* @var int
	* @access private
	*/
    private $pages;

    /**
	* ���������� ������� ������.
	* @var int
	* @access private
	*/
    private $all_pages;

    /**
	* ���������� ������, �� ������� ����� ��������� ��.
	* @var int
	* @access private
	*/
	private $blocks;

	/**
	* ��� GET-���������� URI-�������, ������� ����� ��������� ��������.
	* @var int
	* @access private
	*/
	private $var_page_name;

	/**
	* ��� GET-���������� URI-�������, ������� ����� ��������� ���� �������.
	* @var int
	* @access private
	*/
	private $var_separator_name;

    /**
	* ����������� ������.
	* �������������� ��� ����������� ����������, � �����
	* ��������� �������� ���������� ��� ����������� � SQL-�������� LIMIT.
	* @access public
	* @param int ������������ ���������� �������, ������� ���������� �������� �� ����� ��������.
	* @param int �� ������� ������ ����� ��������� ��� ������.
	* @param string ��� ����������� �������������� ������� ("GET", "POST" ��� "REQUEST").
	* @param string ��� ����� ����������, �� ����������� �������������� �������,
	* ����������� ��������.
	* @param string ��� ����� ���������� �� ����������� �������������� �������, ����������� ���� �������
	* @return void
	*/
    public function __construct($limit, $num_blocks, $var_page_name = "page", $var_separator_name = "sep", $array_name = "REQUEST")
    {
        // ������������ ���������� �������,
        // ������� ���������� �������� �� ����� ��������
        $this->limit = intval($limit);

        // �� ������� ������ ����� ��������� ��.
        $this->plimit = intval($num_blocks);

        // ���������� �������, �� ������� ����� ������� 1 ����.
        $this->pages = ceil($this->plimit/$this->limit);

		$this->var_page_name = $var_page_name;
		$this->var_separator_name = $var_separator_name;

        $array_name = "_".ltrim($array_name, "_");
        $array_name = eval("return \$$array_name;");

        // ���������� ������� ���������.
        $this->currentSep = ( isset($array_name[$var_separator_name]) && is_numeric($array_name[$var_separator_name]) )
                              ? intval($array_name[$var_separator_name])
                              : 1;

        // ���������� ������� �������� $_REQUEST[page]
        $this->currentPage = !isset($array_name[$var_page_name])
                             ?
                             ($this->currentSep-1) * $this->pages + 1
                             :
                             intval($array_name[$var_page_name]);

        $this->startLimit = ($this->currentPage-1) * $this->limit;        //0, 10, 20
        $this->stopLimit  = $this->limit;                                 //10, 10, 10
    }

    /**
    * ���������� ��������� �������� ��� SQL-��������� LIMIT.
    * @access public
    * @param void
    * @return int ��������� �������� ��� SQL-��������� LIMIT
    */
    public function getStartLimit()
    {
        return $this->startLimit;
    }

    /**
    * ���������� �������� �������� ��� SQL-��������� LIMIT.
    * @access public
    * @param void
    * @return int �������� �������� ��� SQL-��������� LIMIT
    */
    public function getStopLimit()
    {
        return $this->stopLimit;
    }

    /**
     * ���������� ����� ���������� �������.
     * @access public
     * @param void
     * @return int
     */
    public function getCount()
    {
        return $this->all_count;
    }

    /*
    * ��������� �������� �������� - ����� ���������� ������� � ����,
    * � ����� ��������� ��� ����������� ���������� ���
    * ������������ ������ ���������.
    * @access int ���������� ������� � ����
    * @param void
    * @return void
    */
    public function setCount($all_count)
    {
        // ����� ���-�� ������� � �� �� ������� ������.
        $this->all_count = intval($all_count);

        // ���������� ������� ������.
        $this->all_pages = ceil($this->all_count/$this->limit);
        // ���������� ������, �� ������� ����� ��������� ��.
        $this->blocks = ceil($this->all_pages/$this->pages);
        // ���� ���������� ������ ������ ���� �������, ��
        // �� ���������� ������ ���� ���������� ���� �������.
        $this->blocks = ($this->blocks > $this->all_pages) ? $this->all_pages : $this->blocks;

        // ������� ������� ������ ���� ��� ������ ���������� $this->blocks, ��� �� ��� �������� ��������� �������.
        // �.�. ������������, ��� ���� ������, ��������� �� 3 ������, ����� ���������� ������� ������ ���� ����� 6.
        $this->teoretic_max_count = $this->limit * $this->all_pages;

        // �������� ������ �������� ��� ������ � �������.
        $this->table = array();
        // ����� �������� �����
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
                    // ��� �����, ������� ����� ������������ ��� ��������� � ������������ ���������� ������
                    "decrement_anhor" => ($temp == $temp2 ? $temp : $temp." - ".$temp2),
                    "increment_anhor" => ($temp3 == $temp4 ? $temp3 : $temp3." - ".$temp4)
                );
            }

        return $this;
    }

    /**
    * ���������� ����������, ������� � ����� ������, ��� ��� ��������������,
    * ����� ���������� �������� id ������ � ��������� ������� ������.
    * @access public
    * @param void
    * @return int ���������� ��������������
    */
    public function getAutodecrementNum()
    {
        return $this->all_count - $this->startLimit;
    }

    /**
    * ���������� ����������, ������� � ����� ������, ��� ��� ��������������,
    * ����� ���������� �������� id ������ � ��������� ������� ������.
    * @access public
    * @param void
    * @return int ���������� ��������������
    */
    public function getAutoincrementNum()
    {
        return $this->limit * ($this->currentPage-1) + 1;
    }

    /**
    * ���������� ����� ���������� "sep" ��� ������������ ������ �������� �� ���������� ���� ������� (<<).
    * @access public
    * @param void
    * @return int ����� ���������� ����������� ����� �������
    */
    public function getLastBlockSeparator()
    {
        $a = $this->currentSep - 1;
        return $a ? $a : 0;
    }

    /**
    * ���������� ����� ���������� "sep" ��� ������������ ������ �������� �� ��������� ���� ������� (>>).
    * @access public
    * @param void
    * @return int ����� ���������� ���������� ����� �������
    */
    public function getNextBlockSeparator()
    {
        if ($this->currentSep < $this->blocks) {
            return $this->currentSep + 1;
        }

        return 0;
    }

    /**
    * ���������� ����� ���������� "sep" ��� ������������ ������ �������� �� ��������� �������� (>>>).
    * @access public
    * @param void
    * @return int ����� ���������� ���������� ����� �������
    */
    public function getLastSeparator()
    {
        return $this->blocks;
    }

    /**
    * ���������� ����� �������� "page" ��� ������������ ������ �������� �� ��������� �������� (>>>).
    * @access public
    * @param void
    * @return int ����� ��������� ��������
    */
    public function getLastPage()
    {
        return $this->all_pages;
    }

    /**
    * ���������� ����������� ������ ��� ����� ������ � ������� (��. ������).
    * � ������ ������� �������� ���������, ������������ ��� �����������
    * �������� ���������.
    * ��� ������ "page" N-���� �������� ������� ���������� �������� ����� ��������.
    * ��� ������ "separator" �������� ��������, ���������� ��� ���� N-��� ���������
    * ������ - ������� ���������.
    * ���� "decrement_anhor" ��� ��������� �������������, ������������ ��������
    * ����� ����������� ��� ����������� ������ ����������������� ��������, ��������: "40 - 30", "30 - 20".
    * ���� "increment_anhor" ��� ��������� �������������, ������������ ��������
    * ����� ����������� ��� ����������� ������ ����������������� ��������, ��������: "10 - 20", "20 - 30".
    * @access public
    * @param void
    * @return array
    */
    public function getPagesArray()
    {
        return $this->table;
    }

    /**
    * ���������� �������� ������� ��������.
    * @access public
    * @param void
    * @return int ����� ������ ��������
    */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
    * ���������� �������� ������� ���������.
    * @access public
    * @param void
    * @return int ����� �������� ����������
    */
    public function getCurrentSeparator()
    {
        return $this->currentSep;
    }

    /**
    * ���������� ����� ���������� "sep" ��� ������������ ������ �������� �� ���������� �������� (<).
    * @access public
    * @param void
    * @return int ����� ���������� ���������� ��������
    */
    public function getLastPageSeparator()
    {
        //    ������� ���������, ����������� ���������
        $cs = ceil($this->currentPage/$this->pages);
        //    ���������� ��������� �������� currentPage - 1
        $cs2 = ceil(($this->currentPage-1)/$this->pages);

        //    ���� ��������� �������� currentPage - 1 ������ �������� ����������,
        //    ������ �������� currentPage - 1 ��������� � ���������� ����� � ����������� $cs2
        if ($cs2 < $cs) {
            return $cs2;
        }

        return $cs;
    }

    /**
    * ���������� ����� ���������� "sep" ��� ������������ ������ �������� �� ��������� �������� (>).
    * @access public
    * @param void
    * @return int ����� ���������� ��������� ��������
    */
    public function getNextPageSeparator()
    {
        //    ������� ���������, ����������� ���������.
        $cs = ceil($this->currentPage/$this->pages);
        //    ������������������� �������� currentPage + 1.
        $cs2 = ceil(($this->currentPage+1)/$this->pages);

        //    ���� ��������� �������� currentPage + 1 ������ �������� ����������,
        //    ������ �������� currentPage + 1 ��������� � ���������� ����� � ����������� $cs2.
        if ($cs2 > $cs) {
            return $cs2;
        }

        return $cs;
    }

    /**
    * ���������� ����� �������� "page" ��� ������������ ������ �������� �� ���������� �������� (<).
    * @access public
    * @param void
    * @return int ����� ���������� ��������
    */
    public function getLastPagePage()
    {
        $a = $this->currentPage - 1;
        return $a ? $a : 0;
    }

    /**
    * ���������� ����� �������� "page" ��� ������������ ������ �������� �� ��������� �������� (>).
    * @access public
    * @param void
    * @return int ����� ��������� ��������
    */
    public function getNextPagePage()
    {
        return $this->currentPage < $this->all_pages ? $this->currentPage+1 : 0;
    }

    /**
    * ���������� ��� ����������.
    * @access public
    * @param void
    * @return string
    */
    public function getSeparatorName()
    {
        return $this->var_separator_name;
    }

    /**
    * ���������� ��� ��������.
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