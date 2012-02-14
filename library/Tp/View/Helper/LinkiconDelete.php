<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_LinkiconDelete extends Zend_View_Helper_Abstract
{
    public function linkiconDelete($formActionUrl, $confirm = true, $confirmMessage = "Are you sure?") {
        $onSubmit = "";
        if($confirm) {
            $onSubmit = 'if(confirm("' . $confirmMessage . '")) { return true } else { return false; }';
        }

        $output = '<form ' . $onSubmit . ' class="deleteForm" action="' . $formActionUrl . '" method="POST">'
            . '<input type="hidden" name="doIt" value="doIt"/>'
            . '<input type="submit" value="delete" />'
            . '</form>';

        return $output;
    }
}
