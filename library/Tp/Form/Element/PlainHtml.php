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

    /**
     * @see Notnet_Form_Element_Interface::loadDefaultDecorators()
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        if (empty($decorators)) {
            $getId = create_function('$decorator', 'return $decorator->getElement()->getId() . "-element";');
            $this->addDecorator('ViewHelper')
                ->addDecorator('HtmlTag', array('tag' => 'dd', 'id'  => array('callback' => $getId)));
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
