<?php
/**
 * User: uhon
 * Date: 2012/02/15
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Form_Decorator_Label extends Zend_Form_Decorator_Label {
    private $_label;

    public function getLabel()
    {
        if(!isset($this->_label)) {
            return parent::getLabel();
        }
        return $this->_label;
    }

    public function setLabel($label)
    {
        $this->_label = $label;
    }
}
