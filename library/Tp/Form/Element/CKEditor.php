<?php


class Tp_Form_Element_CKEditor extends Tp_Form_Element_Textarea
{
    public function init()
    {
        parent::init();
        $this->setAttrib('class', 'ckeditor');
        $this->removeFilter('StripTags');
    }



    public function render(Zend_View_Interface $view = null)
    {
        $result = parent::render($view);
        Tp_Shortcut::getView()->javascript("$('#" . $this->getId() . "').ckeditor();");

        return $result;
    }
}