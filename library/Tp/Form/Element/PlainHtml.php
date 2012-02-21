<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Form_Element_PlainHtml extends Zend_Form_Element
{
    public $helper = 'formPlainHtml';

    private $_tag = 'p';

    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        if (empty($decorators)) {
            $getId = create_function('$decorator', 'return $decorator->getElement()->getId() . "-element";');
            $this->addDecorators(array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array(new Tp_Form_Decorator_Description(), array('tag' => 'td', 'class' => 'description')),
                array('Label', array('tag' => 'td', 'class' => 'label')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
            ));


        }

        return $this;
    }


    public function setTag($tag) {
        $this->_tag = $tag;
    }

    public function getTag() {
        return $this->_tag;
    }

    public function render(Zend_View_Interface $view = null)
    {
        $this->setAttrib('tag', $this->_tag);

        return parent::render($view);
    }


    public function isValid($value, $context = null)
    {
        return true;
    }
}
