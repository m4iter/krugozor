<?php
/**
 * ����� ��� ���������� ����������� � ������� HTML, �������
 * ������������ � �������� ������������ �������� ����������
 * �� ����������� ���� �������.
 * ����� ���������� �������� � ��������� ���������:
 *
 * <td>
 * <?php
 * $linker = new Helper_SortLink()
 *           ->setFieldName('id')
 *           ->setAnchor($this->lang->id)
 *           ->setUrl('/admin/user/')
 *           ->setIconSrc($this->path['image']['system']['icon'])
 *           ->setCurrentFieldName($this->field_name)
 *           ->setCurrentSortOrder($this->sort_order)
 *           ->setQueryStringFromArray(array(
 *               'sep' => $this->navigation->getCurrentSeparator(),
 *               'page' => $this->navigation->getCurrentPage(),
 *               'search' => $this->search,
 *               ...
 *           ));
 *
 *           echo $linker->getHtml();
 * ?>
 * </td>
 */
class Helper_SortLink
{
    /**
     * URL-����� �����������.
     *
     * @var string
     */
    protected $url;

    /**
     * ����� �����������.
     *
     * @var string
     */
    protected $anchor;

    /**
     * ���� � ���������� � ��������.
     *
     * @var string
     */
    protected $icon_src;

    /**
     * ����� ������ ���� ���������� �� ���������.
     *
     * @var array
     */
    protected $icons_name = array('asc' => 'asc.png',
                                  'desc' => 'desc.png');

    /**
     * ��� ����.
     *
     * @var string
     */
    protected $field_name;

    /**
     * ������� ������� ���� �������,
     * �� �������� ���������� ����������.
     *
     * @var string
     */
    protected $current_field_name;

    /**
     * ������� ������� ����������,
     * �� �������� ���������� ����������.
     *
     * @var string
     */
    protected $current_sort_order;

    /**
     * ��������� QUERY_STRING � ���� �������������� �������
     * array('key' => 'value'), ������� ����� ��������� � �����������.
     *
     * @var array
     */
    protected $query_string = array();

    /**
     * @param array ������ ��������
     */
    public function __construct(){}

    /**
     * ������������� URL ������.
     *
     * @param $url
     * @return object Helper_SortLink
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;

        return $this;
    }

    /**
     * ������������� ����� ������.
     *
     * @param $anchor
     * @return object Helper_SortLink
     */
    public function setAnchor($anchor)
    {
        $this->anchor = (string) $anchor;

        return $this;
    }

    /**
     * ���� �� ���������� � �������������-��������.
     * � ������ ���������� ������ ������ ��� �����������
     * ������������ ������� ���������� ASC � DESC.
     *
     * @param string
     * @return object Helper_SortLink
     */
    public function setIconSrc($icon_src)
    {
        $this->icon_src = (string) $icon_src;

        return $this;
    }

    /**
     * ������������� ��� �������, �� �������� �����
     * ����������� ����������.
     *
     * @param string
     * @return object Helper_SortLink
     */
    public function setFieldName($field_name)
    {
        $this->field_name = (string) $field_name;

        return $this;
    }

    /**
     * ��� �������, �� �������� � ������ ������ ����������
     * ����������. ���������������, ��� $current_field_name
     * ������� �� �������.
     *
     * @param $current_field_name
     * @return object Helper_SortLink
     */
    public function setCurrentFieldName($current_field_name)
    {
        $this->current_field_name = (string) $current_field_name;

        return $this;
    }

    /**
     * ��� ���������� (ASC ��� DESC) � ������ ������.
     * ���������������, ��� ��� �������� ������� �� �������.
     *
     * @param $current_field_name
     * @return object Helper_SortLink
     */
    public function setCurrentSortOrder($current_sort_order)
    {
        $this->current_sort_order = strtoupper($current_sort_order);

        return $this;
    }

    /**
     * ��������� ������������� ������ ������ ������ �����������,
     * ������� ������������ ����� ����� ������ � ��������, ��
     * ������� ����� ����������� QUERY_STRING.
     *
     * @param array $data
     * @return object Helper_SortLink
     */
    public function setQueryStringFromArray(array $data)
    {
        $this->query_string = $data;

        return $this;
    }

    /**
     * ���������� HTML-��� ������.
     *
     * @param void
     * @return string
     */
    public function getHtml()
    {
        ob_start();
        ?>
        <a href="<?php echo $this->url?>?<?php echo $this->makeQueryString()?>"><?php echo $this->anchor?></a>
        <?php if ($this->current_field_name == $this->field_name): ?>
            &nbsp;<img alt="<?php echo $this->current_sort_order?>"
src="<?php echo $this->icon_src.$this->icons_name[strtolower($this->current_sort_order)]?>" /><?php endif; ?>
        <?php
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * ������� QUERY_STRING.
     *
     * @param void
     * @return string
     */
    protected function makeQueryString()
    {
        $data = $this->query_string;

        $data['sort_order'] = $this->current_sort_order == 'DESC' ? 'ASC' : 'DESC';

        $data['field_name'] = $this->field_name;

        return http_build_query($data);
    }
}
?>