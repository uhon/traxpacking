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
        $isXHR = Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest();
        $javascript = trim($javascript);
        $javascript = $this->debugInfos($jquerySelector, $event, $isXHR) . $javascript . "\n";

        /// normal Request (published trough Layout)
        if (!$isXHR) {
            if($onDocumentReady) {
                $javascript = "$(function() {\n"
                    . $javascript
                    . "});\n";
            }

            $this->view->inlineScript($javascript);
        /// Ajax Request
        } else {
            echo '<script type="text/javascript">' . "\n" . $javascript . "</script>\n";
        }
	}

    private function debugInfos($jquerySelector, $event, $isXHR) {
        if(APPLICATION_ENV !== 'production') {

            $tellXHR = "";
            if($isXHR) {
                $tellXHR = " (XmlHttpRequest)";
            }
            $method = 'Javascript added';
            $messageTail = "'";
            if($jquerySelector !== null && $event !== null) {
                $method = 'Javascript bound' . $event . '-event ';
                $messageTail = " to the Element:', $('{$jquerySelector}')";
            }

            $trace = debug_backtrace();
            $token = uniqid();
            $debug = "C.log('{$method} by {$trace[6]['class']}::{$trace[6]['function']}{$tellXHR}, token: {$token}{$messageTail});\n";


            return $debug;
        }
        return "";
    }


}
