<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Form_Poi extends Tp_Form
{
    //private $_initPicture = null;

    private $_longitude = null;

    private $_latitude = null;

    private $_svgCoordinates = null;

    private $_pictures = null;

    private $_pictureSubforms = array();

    private $_subformCounter = 0;

    private $_id = null;

    public function init() {
        $this->addElement('text', 'title', array(
            'required' => true,
            'label' => 'Title',
            'description' => 'Also shown in Slideshow'
        ));

        $this->addElement('text', 'latitude', array(
            'value' => $this->_latitude,
            'required' => true,
            'label' => 'Latitude'
        ));

        $this->addElement('text', 'longitude', array(
            'value' => $this->_longitude,
            'required' => true,
            'label' => 'Longitude'
        ));

        $this->addElement('text', 'svgCoordinates', array(
            'value' => $this->_longitude,
            'required' => true,
            'label' => 'SVG coords.'
        ));

        $country = new \Tp\Entity\Country();
        $this->addElement('select', 'country', array(
            'required' => true,
            'multiOptions' => $country->getCountryNameArray()
        ));

        $this->addElement('textarea', 'description', array(
            'label' => 'Description'
        ));

        /*$category = new \Tp\Entity\PoiCategory();
        $this->addElement('multiCheckbox', 'categories', array(
            'value' => $this->_categories,
            'required' => true,
            'label' => 'Categories',
            'multiOptions' => array('1' => 'A', '2' ="B")
        ));*/

        $pictureSubform = new Tp_Form_TabbedSubform();
        $pictureSubform->addSubForms($this->_pictureSubforms);

        $this->addSubForm($pictureSubform, 'pictures');

        $this->addElement('submit', 'save');
    }

    /*public function setInitPicture(\Tp\Entity\Picture $p = null)
    {
        if($p !== null) {
            $gpsProvider = new Tp_Provider_Gps();
            $exif = exif_read_data(APPLICATION_PATH . '/../public/media/' . $p->filename . '_orig.jpg');
            $this->_latitude = $exif['GPSLatitudeRef'] . $gpsProvider->getGpsDigitFormat($exif['GPSLatitude']);
            $this->_longitude = $exif['GPSLongitudeRef'] . $gpsProvider->getGpsDigitFormat($exif['GPSLongitude']);

            $this->addPicture($p);
        }
    }*/

    public function setPictures(\Doctrine\ORM\PersistentCollection $pictures = null)
    {
        if($pictures !== null) {
            foreach($pictures->toArray() as $p) {
                $this->addPicture($p);
            }
        }
    }

    /**
     * @return string
     */
    public function getDefaultRedirectUrl()
    {
        return '/admin/poi/index';
    }

    private function addPicture(\Tp\Entity\Picture $picture) {
        $subForm = new Tp_Form_Subform();

        $subForm->addElement('hidden', 'pId', array(
            'value' => $picture->id
        ));

        $subForm->addElement('plainHtml', 'removeLink', array(
            'value' => '<a class="removeLink" href="' . $this->getView()->url(array('action' => 'remove-picture', 'picture' => $picture->id)) . '">remove</a>',
            'label' => 'Remove Picture from POI?',
        ));

        $subForm->addElement('text', 'dateTime', array(
            'label' => 'Date & Time (of picture)',
            'required' => true,
            'value' => $picture->datetime->format('Y-m-d H:m:s')
        ));

        $subForm->addElement('textarea', 'description', array(
            'label' => 'Description',
            'value' => $picture->description,
            'required' => true
        ));

        $subForm->addElement('plainHtml', 'preview', array(
            'value' => '<img class="thumbPic" src="/media/' . $picture->filename . '_small.jpg" alt="" />',
            'label' => 'Preview'
        ));

        $this->_pictureSubforms[((string)$this->_subformCounter+1)] = $subForm;
        $this->_subformCounter++;
    }
}
