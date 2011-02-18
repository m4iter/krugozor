<?php
class Html_ElementOption extends Html_Element
{
    // Значение option.
    private $text;

    public function __construct()
    {
        parent::__construct();

        $this->attrs = array
        (
            'selected' => array('selected'),
            'disabled' => array('disabled'),
            'label' => 'Text',
            'value' => 'CDATA'
        );

        $this->all_attrs = array_merge($this->attrs, $this->coreattrs, $this->i18n, $this->events);
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    protected function createDocObject()
    {
        $class = __CLASS__;

        if (is_object($this->doc) && $this->doc instanceof $class) return;

        $this->doc = new DOMDocument('1.0', 'windows-1251');
    	$option = $this->doc->createElement('option');
        $text = $this->doc->createTextNode(parent::convertEncoding($this->text));
        $option->appendChild($text);

        foreach ($this->data as $key => $value)
        {
            $option->setAttribute($key, $value);
        }

        $this->doc->appendChild($option);
    }

    public function exportNode()
    {
        $this->createDocObject();

        return $this->doc->firstChild;
    }
}
?>