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
                $poi->country,
                $poi->description,
                $this->view->linkiconEdit($poi->getEditUrl()),
                $this->view->linkiconDelete($poi->getDeleteUrl())
            );
        }
    }

    public function editAction() {
        $add = true;
        $initPicture = $this->_em->find('Tp\Entity\Picture', $this->_getParam('picture', 0));
        $poi = $this->_em->find('Tp\Entity\Poi', $this->_getParam('poi', 0));
        $pictures = null;
        if($poi) {
            $add = false;
            if($poi->pictures) {
                $pictures = $poi->pictures;
            }
        } else {
            $poi = new Poi();
        }

        $form = new Form_Poi(
            array(
                'id' => 'poiForm',
                'pictures' => $pictures,
                'initPicture' => $initPicture,
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
                $cachedPicture = $form->getValue('cachedPicture');

                if($cachedPicture != null) {
                    $picVersions = glob(APPLICATION_PATH . '/../public/cache/' . $cachedPicture . "*");
                    foreach($picVersions as $pv) {

                    }
                    $removablePicture = APPLICATION_PATH . '/../public/upload/' . $form->getValue('removablePicture');
                    if(file_exists($removablePicture)) {
                        unlink($removablePicture);
                    }

                    $picture->filename = $cachedPicture;
                    $picture->datetime = new \DateTime($form->getValue('dateTime'));
                    $this->_em->persist($picture);
                } elseif($picture->filename !== null) {
                    $picture->datetime = new \DateTime($form->getValue('dateTime'));
                }

                $poi->title = $form->getValue('title');
                $poi->description = $form->getValue('description');
                $poi->latitude = $form->getValue('latitude');
                $poi->longitude = $form->getValue('longitude');
                $poi->country = new Tp\Entity\Country($form->getValue('country'));
                $poi->picture = $picture;
                $poi->type = 1;
                $this->_em->persist($poi);

                $this->_em->flush();

                $message = $add ? 'POI was successfully added' : 'POI was successfully updated';
                $this->infoMessage($message);
                $form->redirectOnSuccess();

            }
        } else {
            if($poi && !$add) {
                $data = $poi->toArray();
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
            $poi->remove($picture);
            $this->_em->flush();
            $this->infoMessage("Picture was successfully removed and is now uninitalized");
            $this->_redirect("/admin/poi/index");
        }
        $this->errorMessage('There was an error removing that Picture');
        $this->_redirect("/admin/poi/index");
    }
}
