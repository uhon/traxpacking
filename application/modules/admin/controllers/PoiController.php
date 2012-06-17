<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */

use \Tp\Entity\Picture;
use \Tp\Entity\Poi;

class Admin_PoiController extends Tp_Controller_Action
{

    public function indexAction() {
        $repository = $this->_em->getRepository('Tp\Entity\Poi');
        $pois = $repository->findAll();

        $this->view->pois = array();
        foreach($pois as $poi) {
            /* @var \Tp\Entity\Poi $poi */
            $this->view->pois[] = array(
                $poi->title,
                $poi->latitude,
                $poi->longitude,
                $poi->svgCoordinates,
                $poi->country->name,
                $poi->url,
                $this->view->linkiconEdit($poi->getEditUrl()),
                $this->view->linkiconDelete($poi->getDeleteUrl())
            );
        }
    }

    public function editAction() {
        $add = true;
        $poi = $this->_em->find('Tp\Entity\Poi', $this->_getParam('poi', 0));
        $pictures = null;
        if($poi) {
            $add = false;
            if($poi->pictures) {
                $pictures = $poi->getPicturesOrderedByDate();
            }
        } else {
            $poi = new Poi();
        }

        $form = new Form_Poi(
            array(
                'id' => 'poiForm',
                'pictures' => $pictures,
                'action' => $this->view->url(array(
                    'module' => 'admin',
                    'controller' => 'poi',
                    'action' => 'edit',
                    'poi' => $this->_getParam('poi'))
                    , null, true)
            )
        );

        if($this->_request->isPost()) {
            if($form->isValid($this->_request->getPost())) {

                // Save Picture-Specific Information
                foreach($form->getValue('pictures') as $p) {
                    $picture = $this->_em->find('Tp\Entity\Picture', $p['pId']);
                    if($picture->filename !== null) {
                        $picture->datetime = new \DateTime($p['datetime']);
                        $picture->description = $p['description'];
                        $this->_em->persist($picture);
                        $this->_em->flush();
                    }
                }


                $poi->title = $form->getValue('title');
                $poi->url = $form->getValue('url');
                $poi->latitude = $form->getValue('latitude');
                $poi->longitude = $form->getValue('longitude');
                $poi->svgCoordinates = $form->getValue('svgCoordinates');
                //$poi->country = new Tp\Entity\Country($form->getValue('country'));
                $poi->country = $this->_em->find('Tp\Entity\Country', $form->getValue('country'));
                $poi->type = 1;
                //$this->_em->persist($poi->country);
                $this->_em->persist($poi);

                $this->_em->flush();

                $message = $add ? 'POI was successfully added' : 'POI was successfully updated';
                $this->infoMessage($message);
                $form->redirectOnSuccess();

            }
        } else {
            if($poi && !$add) {
                $data = $poi->toArray();
                $data['country'] = $data['country']->id;
                unset($data['pictures']);
                $form->populate($data);
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction() {
        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if(isset($post['doIt'])) {
                $poi = $this->_em->find('Tp\Entity\Poi', $this->_getParam('poi'));
                if($poi !== null) {
                    $this->_em->remove($poi);
                    $this->_em->flush();
                    $this->infoMessage("POI was successfully deleted");
                    $this->_redirect("/admin/poi/index");
                }
            }
            $this->errorMessage('There was an error deleting that POI');
            $this->_redirect("/admin/poi/index");
        }
    }

    public function removePictureAction() {
        $poi = $this->_em->find('\Tp\Entity\Poi', $this->_getParam('poi'));
        $picture = $this->_em->find('\Tp\Entity\Picture', $this->_getParam('picture'));

        if($picture !== null && $poi !== null) {
            $picture->poi = null;
            $this->_em->persist($picture);
            $this->_em->flush();
            $this->infoMessage("Picture was successfully removed and is now uninitalized");
        } else {
           $this->errorMessage('There was an error removing that Picture');
        }
        //$this->_redirect("/admin/poi/index");
    }
}
