<?php
class Module_Category_Helper_Select extends Helper_Abstract
{
    private $tree;

    public function __construct(Cover_Array $tree)
    {
        $this->tree = $tree;
    }

    public function getHtml()
    {
        return $this->createSelect($this->tree);
    }

    protected function createSelect(Cover_Array $tree)
    {
        $categories = new Cover_Array();

        foreach ($tree as $category)
        {
            if ($category->getPid() == 0)
            {
                $optgroup = Helper_Form::inputOptgroup($category->getName());

                if ($category->getTree() && $category->getTree()->count())
                {
                    foreach ($this->createSelect($category->getTree()) as $element)
                    {
                        $optgroup->addOption($element);
                    }
                }

                $categories->append($optgroup);
            }
            else
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
        }

        return $categories;
    }
}
?>