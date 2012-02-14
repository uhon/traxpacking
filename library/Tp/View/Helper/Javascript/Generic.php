<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


abstract class Tp_View_Helper_Javascript_Generic extends Zend_View_Helper_Abstract {
	/**
	 * Add JQuery code (Ajax save)
	 */
	protected function prepareScript($javascript, $onDocumentReady, $jquerySelector = null, $event = null) {
        $javascript = trim($javascript);
        $javascript = $this->debugInfos($jquerySelector, $event) . $javascript . "\n";

        if($onDocumentReady) {
            $javascript = "$(function() {\n"
                . $javascript
                . "}\n";
        }

        /// normal Request (published trough Layout)
        if (!Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $this->view->inlineScript($javascript);
        /// Ajax Request
        } else {
            echo '<script type="text/javascript">' . "\n" . $javascript . "});</script>\n";
        }
	}

    private function debugInfos($jquerySelector, $event) {
        if(APPLICATION_ENV !== 'production') {

            $method = 'Javascript added';
            $messageTail = "'";
            if($jquerySelector !== null && $event !== null) {
                $method = 'Javascript bound' . $event . '-event ';
                $messageTail = " to the Element:', $('{$jquerySelector}')";
            }

            $trace = debug_backtrace();
            $token = uniqid();
            $debug = "C.log('{$method} by {$trace[3]['class']} :: {$trace[3]['function']}, token: {$token}{$messageTail});\n";


            return $debug;
        }
        return "";
    }


}
