<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Form_Picture extends Tp_Form
{
    private $_picture = null;

    public function init() {

        $this->addElement('text', 'datetime', array(
            'label' => 'Date & Time (of picture)',
            'value' => $this->_picture->datetime->format('Y-m-d H:i:s')
        ));

        $poi = new \Tp\Entity\Poi();
        $this->addElement('select', 'poi', array(
            'required' => true,
            'multiOptions' => $poi->getPoiTitleArray(),
            'value' => $poi->getPoiIdOfLastAssignedPicture()
        ));

        $this->addElement('Textarea', 'description', array(
            'label' => 'Description',
            'required' => true
        ));

        $this->addElement('plainHtml', 'preview', array(
            'value' => '<a href="/media/' . $this->_picture->filename . '_orig.jpg" target="_blank">
                            <img class="thumbPic" src="/media/' . $this->_picture->filename . '_small.jpg" alt="" />
                        </a>',
            'label' => 'Preview'
        ));


        $this->addElement('submit', 'save');

    }


    /**
     * @return string
     */
    public function getDefaultRedirectUrl()
    {
        return '/admin/picture/index';
    }

    public function setPicture($picture)
    {
        $this->_picture = $picture;
    }
}
