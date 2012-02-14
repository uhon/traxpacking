<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_LinkiconEdit extends Zend_View_Helper_Abstract
{
    public function linkiconEdit($editUrl, $editText = "edit") {
        return '<a href="' . $editUrl . '">' . $editText . '</a>';
    }
}
