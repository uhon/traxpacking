<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_LinkiconDelete extends Zend_View_Helper_Abstract
{
    public function linkiconDelete($formActionUrl, $confirm = true, $confirm = true, $confirmMessage = "Are you sure?") {
        if($confirm) {
            $confirmMessage = '<div class="confirm">' . $confirmMessage . '</div>';
        } else {
            $confirmMessage = "";
        }

        $output = '<form class="deleteForm " action="' . $formActionUrl . '" method="post">'
            . $confirmMessage
            . '<input type="hidden" name="doIt" value="doIt"/>'
            . '<input type="submit" value="delete" />'
            . '</form>';

        return $output;
    }
}
