<?php
class Module_Category_Helper_Subcategories extends Helper_Abstract
{
    private $tree;

    public function __construct(Cover_Array $tree, $prefix_url='')
    {
        $this->tree = $tree;
        $this->prefix_url = $prefix_url;
    }

    public function getHtml()
    {
        return $this->createSubcategories($this->tree);
    }

    private function createSubcategories(Cover_Array $tree)
    {
        if (!$tree->count())
        {
            return '';
        }

        $str = '<ul>';

        foreach ($tree as $category)
        {
            $str .= '<li><a href="'.$this->prefix_url.$category->getUrl().'">'.$category->getName().'</a>';
            $str .= $this->createSubcategories($category->getTree()).'</li>';
        }

        $str .= '</ul>';

        return $str;
    }
}
?>