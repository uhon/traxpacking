<?php

class Form_Login extends Tp_Form
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

    /**
     * @return string
     */
    public function getDefaultRedirectUrl()
    {
        return "/admin";
    }
}

