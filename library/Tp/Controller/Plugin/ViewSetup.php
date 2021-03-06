<?php
/**
 * User: uhon
 * Date: 2012/04/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */

class Tp_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract
{

    private static $headScripts = array(
        '/js/jquery/3rd_party/jquery-1.7.1.js',
        '/js/jquery/3rd_party/jquery-ui-1.8.21.custom.min.js',
        '/js/jquery/3rd_party/jquery.form.js',
        '/js/jquery/3rd_party/jquery.tablesorter.js',
        '/js/jquery/3rd_party/jquery.zend.jsonrpc.js',
        '/js/jquery/3rd_party/jquery.maxZIndex.js',
        '/js/jquery/3rd_party/jquery.svg.js',
        '/js/jquery/3rd_party/jquery.svgdom.js',
        '/js/jquery/3rd_party/jquery.spinners.min.js',
        '/js/jquery/3rd_party/jquery.thumbnailScroller.js',
        '/js/jquery/3rd_party/jquery.lightview_adapted.js',
        '/js/jquery/3rd_party/json2.js',
        '/js/jquery/3rd_party/jquery.tinyTips.js',
        '/js/jquery/3rd_party/supersized/slideshow/js/jquery.easing.min.js',
        '/js/jquery/3rd_party/jquery.waitforimages.js',
        // TODO: jquery.mb.bgndGallery breaks js-compression - moved import statement directely into layout (without compression)        
        //'/js/3rd_party/jquery.mb.bgndGallery/inc/mb.bgndGallery.js',
        //'/js/jquery/3rd_party/supersized/slideshow/js/supersized.3.2.7.js',
        //'/js/jquery/3rd_party/supersized/slideshow/theme/supersized.shutter.js',
        '/js/jquery/3rd_party/jquery.svgpan.js',
        '/js/jquery/jquery.waitForIt.js',
        '/js/site.js',
    );


    private static $headStyles = array(
        '/css/main.css',
        '/css/jquery/trontastic/jquery-ui-1.8.20.custom.css',
        '/css/jquery/imgzoom.css',
        '/css/jquery/jquery.thumbnailScroller.css',
        '/css/jquery/lightview/lightview.css',
        '/css/jquery/lightview/lightview.css',
    );

    public static function getHeadScripts() {
        return self::$headScripts;
    }

    public static function getHeadStyles() {
        return self::$headStyles;
    }


    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if($request->isXmlHttpRequest()) {
            return false;
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();

        $this->_view = $viewRenderer->view;

        // set up common variables for the view
        $this->_view->module = $request->getModuleName();
        $this->_view->controller = $request->getControllerName();
        $this->_view->action = $request->getActionName();



        // Add all Scripts and Styles to <head>.
        // In Development Env. Files are added one by one.
        // Production-Env uses the compressed form under /public/conpressed/...
        // Files have to be copressed by executing scripts/minify.sh
        if(APPLICATION_ENV === "production")  {
            $this->_view->headScript()->appendFile(
                '/compressed/traxpacking-' . Tp_Shortcut::getVersion() . '.min.js',
                'text/javascript'
            );
            $this->_view->headLink()->appendStylesheet(
                '/compressed/traxpacking-' . Tp_Shortcut::getVersion() . '.min.css', 'all'
            );

        } else {
            foreach(self::getHeadScripts() as $script) {
                $this->_view->headScript()->appendFile(
                    $script, 'text/javascript'
                );
            }

            foreach(self::getHeadStyles() as $styleFile) {
                $this->_view->headLink()->appendStylesheet($styleFile, 'all');
            }

        }
    }
}
