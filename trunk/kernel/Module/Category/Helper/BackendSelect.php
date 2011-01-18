<?php
class Module_Category_Helper_BackendSelect extends Module_Category_Helper_Select
{
    protected function createSelect(Cover_Array $tree)
    {
        $categories = new Cover_Array();

        foreach ($tree as $category)
        {
            $categories->append(Helper_Form::inputOption($category->getId(), $category->getName(), array('style' => 'padding-left:'.($category->getIndent()*10).'px')));

            if ($category->getTree() && $category->getTree()->count())
            {
                foreach ($this->createSelect($category->getTree()) as $element)
                {
                    $categories->append($element);
                }
            }
        }

        return $categories;
    }
}
?>