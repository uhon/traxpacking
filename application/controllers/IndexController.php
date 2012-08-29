<?php
use Tp\Entity;
class IndexController extends Tp_Controller_Action
{

    public function init()
    {
		$uri = $this->getRequest()->getPathInfo();
		$activenav = $this->view->navigation()->findByUri($uri);
		$activenav->active = true;
		
    }

    public function indexAction()
    {
		/*$this->infoMessage("i'm info");
		$this->infoMessage("i'm second info");
        $this->errorMessage("i'm error");
		$this->errorMessage("i'm second error");*/
    }

    public function sitemapAction()
    {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
    	echo $this->view->navigation()->sitemap();
    }

    public function countriesAction()
    {
        $poi = new Tp\Entity\Poi();
        $this->view->type = $this->_getParam("type", "default");
        if($this->view->type === "photo") {
            $this->view->jsonPoiArray = $poi->getPoisAsJsonArray(true);
        } else {
            $this->view->jsonPoiArray = $poi->getPoisAsJsonArray();
        }
    }

    public function poiAction()
    {
        $this->view->url = base64_decode($this->_getParam('url'));
        $result = $this->_em->getRepository('Tp\Entity\Poi')->findBy(array('url' => $this->view->url));
        $this->view->poi = null;
        if(!empty($result)) {
            $this->view->poi = array_pop($result);
        }
    }

    public function photoAction()
    {
        $poiId = $this->_getParam('poi', null);
        if($poiId == null) {
            $poi = new \Tp\Entity\Poi();
            $poiId = $poi->getLatestPoiWithPicturesAndUrl()->id;
        }

        $this->view->poi = $this->_em->find('Tp\Entity\Poi', $poiId);
        $this->_helper->layout->disableLayout();
    }

    public function countryAction()
    {
        $this->view->country = array_pop($this->_em->getRepository('Tp\Entity\Country')
                ->findBy(array('name' => $this->_getParam('c', 0))));
        $this->view->pois = $this->view->country->pois;

        $poi = new \Tp\Entity\Poi();
        $this->view->jsonPoiArray = $poi->getPoisAsJsonArray(true);

    }

}













