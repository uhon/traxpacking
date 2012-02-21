<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


abstract class Tp_Form extends Zend_Form
{
    protected $_redirectUrl = '/';

    public function __construct($options = null) {
        $this->getView()->doctype('XHTML1_STRICT');
        $this->addPrefixPath('Tp_Form', 'Tp/Form');
        $this->addPrefixPath('Tp_Form_Decorator', 'Tp/Form/Decorator', 'decorator');

/*        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array(new Tp_Form_Decorator_Description(), array('tag' => 'td', 'class' => 'description')),
            array('Label', array('tag' => 'td', 'class' => 'label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));*/
        parent::__construct($options);


        $this->_redirectUrl = Zend_Controller_Front::getInstance()->getRequest()->getParam('formOrigin', $this->_redirectUrl);
    }

    /**
     * Load the default decorators
     *
     * @return Zend_Form
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'table', 'class' => 'tp_form')),
                'Form'
            ));
        }
        return $this;
    }

    public function render(Zend_View_Interface $view = null)
    {
        $isXHR = Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest();
        if($view === null) {
            $view = $this->getView();
        }

        if($isXHR) {
            // Initialize Ajaxified Form (if Ajax-Request)
            $this->getView()->javascript('
                C.log("initialize AjaxForm: ' . $this->getId() . '");
                FORM.initForm(
                    "' . $this->getId() . '",
                    function() { // pre-submit callback
                        $("#popupForm").waitForIt();
                    },
                    function(responseText, statusText) { // post-submit callback remove the dialog
                        C.log("statusText: " + statusText);
                        if(responseText.indexOf("id=\"' . $this->getId() . '\"") !== -1) {
                            $("#popupForm").html(responseText);
                        } else {
                            $("#popupForm").dialog("close", function() { $(this).destroy(); });
                            $("#content").html(responseText);
                            INIT.onContentReady();
                        }
                        //$("#popupForm").waitForItStop();
                    }
                );
            ');
        }



        $counter = 0;
        foreach($this->getElements() as $element) {
            if($element instanceof Zend_Form_Element_Submit) {
                $dLabel = $element->getDecorator('Label');
                if($dLabel !== false) {
                    $dLabel->setLabel('');
                }
            }

            if(!$element instanceof Zend_Form_Element_Hidden) {
                /* @var Zend_Form_Element $element */
                $evenOdd = 'even';
                if($counter % 2 === 0) {
                    $evenOdd = 'odd';
                }
                $dRow = $element->getDecorator('row');
                if($dRow !== false) {
                    $dRow->setOption('class', trim($dRow->getOption('class') . ' ' . $evenOdd));
                }

                $counter++;
            }

        }
        $content = parent::render($view);

        if ($this->isErrors()) {
            $this->errorMessage('Something went wrong. Please check the form-data!');
            if($isXHR) {
                $fmProvider = new Tp_Provider_FlashMessage();
                $content = $fmProvider->provideMessages() . $content;
            }
        }

        return $content;

    }

    public function infoMessage($message)
    {
        Tp_Shortcut::infoMessage($message);
    }

    public function errorMessage($message)
    {
        Tp_Shortcut::errorMessage($message);
    }

    /**
     * @abstract
     * @return string
     */
    abstract public function getDefaultRedirectUrl();

    /**
     *
     */
    public function redirectOnSuccess()
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->gotoUrl($this->_redirectUrl);
    }
}
