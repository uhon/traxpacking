<?php

class Tp_Form_Element_Radio extends Zend_Form_Element_Radio
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
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array(new Tp_Form_Decorator_Description(), array('tag' => 'td', 'class' => 'description')),
                    array('Label', array('tag' => 'td', 'class' => 'label')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
                )
            );
        }
        return $this;
    }
}
