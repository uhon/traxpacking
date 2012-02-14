<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Form_Poi extends Tp_Form
{
    private $_removablePicture = null;

    private $_cachedPicture = null;

    private $_pictureDateTime = null;

    private $_picturePreview = null;

    private $_longitude = null;

    private $_latitude = null;

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

        $this->addElement('textarea', 'description', array(
            'label' => 'Description'
        ));

        $this->addElement('hidden', 'removablePicture', array(
            'value' => $this->_removablePicture,
        ));

        $this->addElement('hidden', 'cachedPicture', array(
            'value' => $this->_cachedPicture,
        ));

        if($this->_pictureDateTime !== null) {
            $this->addElement('text', 'dateTime', array(
                'value' => $this->_pictureDateTime,
                'label' => 'Date & Time (of picture)'
            ));
        }

        if($this->_picturePreview !== null) {
            $this->addElement('plainHtml', 'preview', array(
                'value' => '<img src="' . $this->_picturePreview . '" alt="">'
            ));
        }

        $this->addElement('submit', 'save');
    }

    public function setPicture(\Tp\Entity\Pictures $p = null)
    {
        if($p !== null) {
            $this->_pictureDateTime = $p->datetime->format('Y-m-d H:i:s');
            $this->_picturePreview = "/media/{$p->filename}_small.jpg";
        }
    }

    public function setCachedPicture($cachedPicture)
    {
        if($cachedPicture !== null && $this->getValue('cachedPicture') == null) {
            $gpsProvider = new Tp_Provider_Gps();

            $exif = exif_read_data(APPLICATION_PATH . '/../public/cache/' . $cachedPicture . '_orig.jpg');
            $this->_cachedPicture = $cachedPicture;
            $this->_latitude = $exif['GPSLatitudeRef'] . $gpsProvider->getGpsDigitFormat($exif['GPSLatitude']);
            $this->_longitude = $exif['GPSLongitudeRef'] . $gpsProvider->getGpsDigitFormat($exif['GPSLongitude']);
            $this->_pictureDateTime = new Zend_Date($exif['DateTimeOriginal'], "yyyy:MM:dd HH:mm:ss");
            $this->_pictureDateTime = $this->_pictureDateTime->get('yyyy-MM-dd HH:mm:ss');
            $this->_picturePreview = '/cache/' . $cachedPicture . '_small.jpg';
        }
    }

    public function setremovablePicture($removablePicture)
    {
        if($removablePicture !== null) {
            $this->_removablePicture = $removablePicture;
        }
    }
}
