<?php
class Module_Category_Helper_BreadCrumbs extends Helper_Abstract
{
    // дерево
    private $tree;

    // Префикс каждого узла URL
    private $prefix_url;

    // Делать ли последний элемент BreadCrumbs ссылкой
    private $last_link = false;

    // разделитель элементов хлебных крошек
    private $separator;

    // добавлять ли перед хлебными крошками символ $this->separator
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
     * Если параметр установлен в TRUE, перед хлебными крошками будет добавлен символ $this->separator
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
     * Если параметр установлен в TRUE, последний элемент хлебных крошек будет ссылкой.
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