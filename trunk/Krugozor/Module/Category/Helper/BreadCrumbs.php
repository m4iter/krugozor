<?php
class Module_Category_Helper_BreadCrumbs extends Helper_Abstract
{
    // ������
    private $tree;

    // ������� ������� ���� URL
    private $prefix_url;

    // ������ �� ��������� ������� BreadCrumbs �������
    private $last_link = false;

    // ����������� ��������� ������� ������
    private $separator;

    // ��������� �� ����� �������� �������� ������ $this->separator
    private $add_first_separator = true;

    public function __construct(Cover_Array $tree, $prefix_url='', $separator='&raquo;')
    {
        $this->tree = $tree;
        $this->prefix_url = $prefix_url;
        $this->separator = $separator;
    }

    public function getHtml()
    {
        return ($this->add_first_separator ? ' '.$this->separator.' ' : '').$this->createBreadCrumbs($this->tree);
    }

    /**
     * ���� �������� ���������� � TRUE, ����� �������� �������� ����� �������� ������ $this->separator
     *
     * @param boolean
     * @return this
     */
    public function addFirstSeparator($value)
    {
        $this->add_first_separator = (boolean)$value;

        return $this;
    }

    /**
     * ���� �������� ���������� � TRUE, ��������� ������� ������� ������ ����� �������.
     *
     * @param boolean
     * @return this
     */
    public function lastElementIsLink($value)
    {
        $this->last_link = (boolean)$value;

        return $this;
    }

    private function createBreadCrumbs(Cover_Array $tree)
    {
        if (!$tree->count())
        {
            return '';
        }

        $str = '';

        foreach ($tree as $category)
        {
            if ($category->getTree() && $category->getTree()->count())
            {
                $str .= '<a href="'.$this->prefix_url.$category->getUrl().'">'.$category->getName().'</a> '.$this->separator.' ';
                $str .= $this->createBreadCrumbs($category->getTree());


            }
            else
            {
                $str .= $this->last_link
                        ? '<a href="'.$this->prefix_url.$category->getUrl().'">'.$category->getName().'</a>'
                        : $category->getName();
            }
        }

        return $str;
    }
}
?>