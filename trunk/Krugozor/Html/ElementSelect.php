<?php
class Html_ElementSelect extends Html_Element
{
    // ��������.
    private $checked_value;
    private $options = array();

    public function __construct()
    {
        parent::__construct();

        $this->attrs = array
        (
            'name' => 'CDATA',
            'size' => 'NUMBER',
            'multiple' => array('multiple'),
            'disabled' => array('disabled'),
            'tabindex' => 'NUMBER',
            'onfocus' => 'Script',
            'onblur' => 'Script',
            'onchange' => 'Script'
        );

        $this->all_attrs = array_merge($this->attrs, $this->coreattrs, $this->i18n, $this->events);
    }

    /**
    * ���������� ��������, ������� ����� ������������ � ���������� ����������
    * options � � ������ ���������� ����� ��������� selected ���������������� option.
    *
    * @access public
    * @param string|int $value
    * @return void
    */
    public function setCheckedValue($value)
    {
        $this->checked_value = $value;
        return $this;
    }

    /*
    * ���������� ����� option ��� optgroup � ���������.
    *
    * @access public
    * @param object Html_ElementOption
    * @return void
    */
    public function addOption($option)
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
    	$select = $this->doc->createElement('select');

        foreach ($this->data as $key => $value)
        {
            $select->setAttribute($key, $value);
        }

        $checked_value = !is_array($this->checked_value)
                         ? ($this->checked_value !== null ? array($this->checked_value) : array())
                         : $this->checked_value;

        foreach ($this->options as $option)
        {
            if ($option instanceof Html_ElementOptgroup)
            {
                // ������� ���������� �� options �������� optgroup
                foreach ($option->getOptions() as $key => $opt)
                {
                    if (in_array($opt->value, $checked_value))
		            {
		                $option->getOptionById($key)->selected = 'selected';
		            }
                }
            }
            else if ($option instanceof Html_ElementOption)
            {
                if (in_array($option->value, $checked_value))
                {
                    $option->selected = 'selected';
                }
            }

            $select->appendChild( $this->doc->importNode($option->exportNode(), true) );
        }

        $this->doc->appendChild($select);
    }
}
?>