<?php
/**
 * User: uhon
 * Date: 2012/02/20
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Form_TabbedSubform extends Tp_Form_Subform
{
    public function __construct($options = null) {
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
                'tabList',
                array(array('subformTabbed' => 'HtmlTag'), array('tag' => 'div', 'class' => 'tp_subform_tabbed')),
                array(array('subformContainer' => 'HtmlTag'), array('tag' => 'td', 'class' => 'subform_container', 'colspan' => "3")),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        }
        return $this;
    }

    public function render(Zend_View_Interface $view = null)
    {
        /* @var Zend_Form_Decorator_HtmlTag $tabbedDecorator */
        $tabbedDecorator = $this->getDecorator('tabList');
        $tabbedDecorator->setSubforms($this->getSubForms());
        foreach($this->getSubForms() as $form) {
            if($form instanceof Tp_Form_Subform) {
                /* @var Tp_Form_Subform $form */
                $form->removeDecorator('row');
                $form->removeDecorator('cell');
                $form->addDecorator(
                    array('tab' => 'HtmlTag'), array('tag' => 'div', 'class' => 'tp_subform_tabbed', 'id' => 'tab_' . $form->getName())
                );
            }
        }
        Tp_Shortcut::getView()->javascript("UI.activateTabs()");
        return parent::render($view);
    }
}
