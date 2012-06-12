<?php

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
        $this->view->jsonPoiArray = $poi->getPoisAsJsonArray();
    }

    public function poiAction()
    {
        $this->view->poi = new \Tp\Entity\Poi($this->_getParam('p'));
        $this->view->poi = $this->_em->find('Tp\Entity\Poi', $this->_getParam('p', 0));
    }

    public function countryAction()
    {
        $this->view->country = array_pop($this->_em->getRepository('Tp\Entity\Country')
                ->findBy(array('name' => $this->_getParam('c', 0))));
        $this->view->pois = $this->view->country->pois;

        $poi = new Tp\Entity\Poi();
        $this->view->jsonPoiArray = $poi->getPoisAsJsonArray();
    }

}













