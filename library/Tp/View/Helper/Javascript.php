<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_Javascript extends Tp_View_Helper_Javascript_Generic
{

    public function javascript($javascript, $onDocumentReady = true) {
        return parent::prepareScript($javascript, $onDocumentReady);
    }
}
