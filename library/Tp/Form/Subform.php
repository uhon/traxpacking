<?php
/**
 * User: uhon
 * Date: 2012/02/20
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Form_Subform extends Zend_Form_SubForm {
    public function __construct($options = null) {
        $this->getView()->doctype('XHTML1_STRICT');

        $this->addPrefixPath('Tp_Form', 'Tp/Form');
        $this->addPrefixPath('Tp_Form_Decorator', 'Tp/Form/Decorator', 'decorator');

        parent::__construct($options);
    }

    /**
     * Load the default decorators
     *
     * @return Zend_Form
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                'FormElements',
                array(array('subformTable' => 'HtmlTag'), array('tag' => 'table', 'class' => 'tp_subform')),
                array(array('cell' => 'HtmlTag'), array('tag' => 'td', 'class' => 'subform_container', 'colspan' => "3")),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        }
        return $this;
    }

    public function render(Zend_View_Interface $view = null)
    {
        $counter = 0;
        foreach($this->getElements() as $element) {
            if(!$element instanceof Zend_Form_Element_Hidden) {
                /* @var Zend_Form_Element $element */
                $evenOdd = 'even';
                if($counter % 2 === 0) {
                    $evenOdd = 'odd';
                }
                $dRow = $element->getDecorator('row');
                if($dRow !== false) {
                    $dRow->setOption('class', trim($dRow->getOption('class') . ' ' . $evenOdd));
                }

                $counter++;
            }

        }
        $content = parent::render($view);
        return $content;
    }

    public function infoMessage($message)
    {
        Tp_Shortcut::infoMessage($message);
    }

    public function errorMessage($message)
    {
        Tp_Shortcut::errorMessage($message);
    }
}
