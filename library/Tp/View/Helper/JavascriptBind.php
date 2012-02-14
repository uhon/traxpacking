<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_JavascriptBind extends Tp_View_Helper_Javascript_Generic
{
    // Available Events to bind on
    const BLUR	    = "blur";
    const CHANGE	= "change";
    const CLICK	    = "click";
    const DBLCLICK	= "dblclick";
    const FOCUS	    = "focus";
    const KEYDOWN	= "keydown";
    const KEYUP     = "keyup";
    const KEYPRESS	= "keypress";
    const MOUSEDOWN = "mousedown";
    const MOUSEUP	= "mouseup";
    const MOUSEOVER	= "mouseover";
    const MOUSEMOVE	= "mousemove";
    const MOUSEOUT	= "mouseout";
    const RESET	    = "reset";
    const SUBMIT	= "submit";
    const SELECT	= "select";


    public function javascriptBind($javascript, $jquerySelectorOrElement, $eventType) {
        $javascript = trim($javascript);

        if($jquerySelectorOrElement instanceof Zend_Form_Element_Multi && $eventType == self::CLICK) {
            // If the Elements is a MultiOption-Element we have to bind the function to each and every option.
            // realized trough a unique Class-name for each Option
            $uniqueClassname = $jquerySelectorOrElement->getId() . uniqid();
            $jquerySelectorOrElement->setAttrib("class", trim($jquerySelectorOrElement->getAttrib("class") . " " . $uniqueClassname));
            $jquerySelectorOrElement = "." . $uniqueClassname;
        } else if($jquerySelectorOrElement instanceof Zend_Form_Element) {
            $jquerySelectorOrElement = "#" . $jquerySelectorOrElement->getId();
        }

        $bindScript = "$('" . $jquerySelectorOrElement . "').bind('" . $eventType . "', function(e) {
                          " . $javascript . "
                       });";

        return parent::prepareScript($javascript, true, $jquerySelectorOrElement, $eventType);
    }
}
