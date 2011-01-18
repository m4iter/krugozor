<?php
class Html_ElementOptgroup extends Html_Element
{
    private $options = array();

    public function __construct()
    {
        parent::__construct();

        $this->attrs = array
        (
            'disabled' => array('disabled'),
            'label' => 'Text',
        );

        $this->all_attrs = array_merge($this->attrs, $this->coreattrs, $this->i18n, $this->events);
    }

    /*
    * ���������� ����� option � ���������.
    *
    * @access public
    * @param object Html_ElementOption
    * @return void
    */
    public function addOption(Html_ElementOption $option)
    {
        $this->options[] = $option;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOptionById($id)
    {
        return isset($this->options[$id]) ? $this->options[$id] : null;
    }

    protected function createDocObject()
    {
        $class = __CLASS__;

        if (is_object($this->doc) && $this->doc instanceof $class)
        {
            return;
        }

        $this->doc = new DOMDocument('1.0', 'windows-1251');

        $optgroup = $this->doc->createElement('optgroup');

        foreach ($this->data as $key => $value)
        {
            $optgroup->setAttribute($key, $value);
        }

        foreach ($this->options as $option)
        {
            $node = $this->doc->importNode($option->exportNode(), true);

            $optgroup->appendChild($node);
        }

        $this->doc->appendChild($optgroup);
    }

    public function exportNode()
    {
        $this->createDocObject();

        return $this->doc->firstChild;
    }
}
?>