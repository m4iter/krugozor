<?php
class Html_ElementInput extends Html_Element
{
    /**
     * Сравниваемое значение для radio и checkbox-ов.
     *
     * @access protected
     * @var string
     */
    private $checked_value;

    public function __construct($type=null)
    {
        parent::__construct();

        $this->attrs = array
        (
            'type' => array('text', 'password', 'checkbox', 'radio', 'submit', 'reset', 'file', 'hidden', 'image', 'button'),
            'name' => 'CDATA',
            'value' => 'CDATA',
            'checked' => array('checked'),
            'disabled' => array('disabled'),
            'readonly' => array('readonly'),
            'size' => 'Number',
            'maxlength' => 'Number',
            'src' => 'URI',
            'alt' => 'Text',
            'usemap' => 'URI',
            'tabindex' => 'Number',
            'accesskey' => 'Character',
            'onfocus' => 'Script',
            'onblur' => 'Script',
            'onselect' => 'Script',
            'onchange' => 'Script',
            'accept' => 'ContentTypes',
            // HTML5
            'placeholder' => 'CDATA'
        );

        $this->all_attrs = array_merge($this->attrs, $this->coreattrs, $this->i18n, $this->events);

        $this->type = $type !== null ? $type : $this->attrs['type'][0];
    }

    /**
     * Устанавливает значение для checkbox и radio типов,
     * которое будет сравниваться с имеющимся значением $this->value
     * и в случае, если значения равны, к элементу будет
     * добавляться аттрибут checked.
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

    protected function createDocObject()
    {
        $class = __CLASS__;
        if (is_object($this->doc) && $this->doc instanceof $class) return;

        $this->doc = new DOMDocument('1.0', 'windows-1251');
    	$input = $this->doc->createElement('input');

        if ($this->checked_value !== null &&
            $this->checked_value == $this->value AND
            $this->type == 'checkbox' || $this->type == 'radio')
        {
            $this->checked = 'checked';
        }

        foreach ($this->data as $key => $value)
        {
            $input->setAttribute($key, $value);
        }

        $this->doc->appendChild($input);
    }
}
?>