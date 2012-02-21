<?php
/**
 * User: uhon
 * Date: 2012/02/22
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Form_Decorator_TabList extends Zend_Form_Decorator_Abstract {
    protected $_subforms = array();

    /**
     * Render a description
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $tabList = '<ul class="tabList">';
        foreach($this->_subforms as $subform) {
            /* @var Tp_Form_Subform $subform */
            $tabList .= '<li><a href="#tab_' . $subform->getName() . '">' . $subform->getName() . '</a><span class="ui-icon ui-icon-close">Remove Tab</span></li>';
        }
        $tabList .= "</ul>";
        return $tabList . $content;
    }

    public function setSubforms(array $subforms)
    {
        $this->_subforms = $subforms;
    }


}
