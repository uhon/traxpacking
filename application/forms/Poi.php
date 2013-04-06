<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Form_Poi extends Tp_Form
{
    private $_longitude = null;

    private $_latitude = null;

    private $_svgCoordinates = null;

    private $_svgPrevCoordinates = null;

    private $_pictures = null;

    private $_pictureSubforms = array();

    private $_subformCounter = 0;

    private $_id = null;

    public function init() {
        $poi = new \Tp\Entity\PoiCategory();
        $this->addElement('select', 'category', array(
            'label' => 'Poi-Category',
            'multiOptions' => $poi->getTitleArray(),
            'value' => $this->_category
        ));
        $this->getView()->javascriptBind('FORM.togglePoiOrigin();', $this->getElement('category'), 'change');

        $this->addElement('text', 'title', array(
            'required' => true,
            'label' => 'Title',
            'description' => 'As shown in Slideshow'
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

        $this->addElement('plainHtml', 'choosePoint', array(
            'value' => '<input type="button" id="pickPointButton" value="show World!" /><div id="poiPicker" class="worldMapLarge"></div>',
            'label' => 'Set Location by Cursor'
        ));


        $this->addElement('text', 'svgCoordinates', array(
            'value' => $this->_svgCoordinates,
            'required' => true,
            'label' => 'SVG coords.'
        ));

        $country = new \Tp\Entity\Country();
        $this->addElement('select', 'country', array(
            'required' => true,
            'multiOptions' => $country->getCountryNameArray()
        ));

        $this->addElement('text', 'url', array(
            'label' => 'Links to (URL)'
        ));

        $this->addElement('checkbox', 'toggleOrigin', array(
            'value' => false,
            'required' => true,
            'label' => 'Has different Origin.',
        ));
        $this->getView()->javascriptBind('FORM.togglePoiOrigin();', $this->getElement('toggleOrigin'), 'click');
        $this->getView()->javascript('FORM.initPoiOriginToggle();');



        $svgPrevCoordinates = $this->createElement('text', 'svgPrevCoordinates', array(
            'value' => $this->_longitude,
            'required' => false,
            'label' => 'SVG Prev coords.',
            'description' => 'only if not last poi is previous poi'
        ));

        $this->addDisplayGroup(array($svgPrevCoordinates), 'optional');


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

    public function setPictures(array $pictures = null)
    {
        if($pictures !== null) {
            foreach($pictures as $p) {
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

        $subForm->addElement('text', 'datetime', array(
            'label' => 'Date & Time (of picture)',
            'required' => true,
            'value' => $picture->datetime->format('Y-m-d H:i:s')
        ));

        $subForm->addElement('textarea', 'description', array(
            'label' => 'Description',
            'value' => $picture->description,
            'required' => true
        ));

        $subForm->addElement('plainHtml', 'preview', array(
            'value' => '<a href="/media/' . $picture->filename . '_orig.jpg" target="_blank">
                            <img class="thumbPic" src="/media/' . $picture->filename . '_small.jpg" alt="" />
                        </a>',
            'label' => 'Preview'
        ));

        $this->_pictureSubforms[((string)$this->_subformCounter+1)] = $subForm;
        $this->_subformCounter++;
    }

    public function isValid($data) {
        if($data['toggleOrigin'] === "1" || $data['category'] === "5") {
            foreach($this->getDisplayGroup('optional')->getElements() as $element) {
                $element->setRequired(true);
            }
        }
        return parent::isValid($data);
    }

    public function render(Zend_View_Interface $view = null)
    {
        $poi = new Tp\Entity\Poi();


        $this->getView()->javascript(

        );

        $this->getView()->javascriptBind(
            "
            $('#poiPicker').css({ display: 'block', position: 'fixed', backgroundColor:'blue', top: 0, left: 0 });
            if($('g', $('#poiPicker')).length === 0) {
                SVG.createSvgWorldMap(
                    function() {
                        C.log('fetching all countries with Pictures....');
                        rpc.setAsyncSuccess(function(response) {
                            SVG.setupSvgWorldMap(response, 'poiPicker');
                            SVG.drawPois(" . $poi->getPoisAsJsonArray() . ", 'default', 'poiPicker');

                            $('g', $('#poiPicker')).bind('dblclick',  function (e) {
                                   $('#svgCoordinates').val(svgPanLastClick.x + ',' + svgPanLastClick.y);
                                   $('#poiPicker').hide();
                            });

                        });
                        rpc.getCountriesWithPictures();
                    },
                'poiPicker'
                );
            }
            ",
            "#pickPointButton",
            Tp_View_Helper_JavascriptBind::CLICK);





        return parent::render($view);
    }

}
