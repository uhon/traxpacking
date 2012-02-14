<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_FormPlainHtml extends Zend_View_Helper_FormElement
{

    public function formPlainHtml($name, $value = null, $attribs = null, $options = null)
    {
        $info = $this->_getInfo($name, $value, $attribs, $options);
        extract($info); // name, value, attribs, $options

        $tag = $attribs['tag'];
        unset($attribs['tag']);

        $xhtml = '<' . $tag
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $value
                . '</' . $tag . '>';

        return $xhtml;
    }
}