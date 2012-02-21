<?php

class Tp_Form_Element_Hidden extends Zend_Form_Element_Hidden
{
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorators(
                array(
                    'ViewHelper',
                    'Errors',
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element', "options" => array('style' => 'display:none', 'colspan' => "3"))),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
                )
            );
        }
        return $this;
    }
}
