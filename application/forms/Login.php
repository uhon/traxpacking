<?php

class Form_Login extends Zend_Form
{
    public function init()
    {
        $this->addElement("text", "email", array(
            'validators' => array('notEmpty'),
            'required' => true,
        ));

        $this->addElement("password", "password", array(
            'validators' => array('notEmpty'),
            'required' => true,
        ));

        $this->addElement("submit", "login");
    }
}

