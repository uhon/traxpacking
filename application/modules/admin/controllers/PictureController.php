<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */

use \Tp\Entity\Pictures as Pictures;

class Admin_PictureController extends Tp_Controller_Action
{
    public function indexAction()
    {
        $pictures = glob(APPLICATION_PATH . "/../public/upload/*.jpg");
        $this->view->newPictures = array();
        foreach($pictures as $p) {
            $exif=exif_read_data($p);
            if($exif === false) {
                throw new Exception('exif-Data for picture ' . basename($p) . ' could not be read! please solve the Problem manually (exiftool)!');
            }

            $this->view->newPictures[] = array(
                'name' => basename($p),
                'size' => round(filesize($p) / 1048576, 2) . 'MB',
                'datetime' => $exif['DateTimeOriginal'],
                'initializeLink' => '<a href="' . $this->view->url(array('action' => 'convert', 'pic' => basename($p))) . '">initialize</a>'
            );
        }
        $this->view->unassignedPictures = array();
        $unassigned = $this->_em->getRepository('Tp\Entity\Pictures')->findBy(array('poi' => null));
        foreach($unassigned as $p) {
            /* @var \Pictures $p */
            $this->view->unassignedPictures[] = array(
                'name' => $p->filename,
                'datetime' => $p->datetime->format('Y-m-d H:m:s'),
                'editLink' => $this->view->linkiconEdit($p->getAssignUrl(), 'assign to poi'),
            );
        }
    }

    public function convertAction() {
        if(($pic = $this->_getParam('pic')) !== null) {
            $uploadedPicture = APPLICATION_PATH . "/../public/upload/" . $pic;
            $exif=exif_read_data($uploadedPicture);

            $genericFilename = APPLICATION_PATH . "/../public/media/" . uniqid('tp_');


            if(rename($uploadedPicture, $genericFilename . "_orig.jpg") === false) {
                throw new Exception("failed to move $uploadedPicture to media directory (is directory writable?)\n");
            }

            require_once APPLICATION_PATH . '/../library/phpThumb/src/ThumbLib.inc.php';

            $thumb = PhpThumbFactory::create($genericFilename . "_orig.jpg");
            $thumb->resize(Tp_Shortcut::PIC_MEDIUM_X, Tp_Shortcut::PIC_MEDIUM_Y);
            $thumb->save($genericFilename . '_medium.jpg');

            $thumb = PhpThumbFactory::create($genericFilename . "_orig.jpg");
            $thumb->resize(Tp_Shortcut::PIC_SMALL_X, PIC_SMALL_Y);
            $thumb->save($genericFilename . '_small.jpg');

            $dateTimeOfPicture = new Zend_Date($exif['DateTimeOriginal'], "yyyy:MM:dd HH:mm:ss");

            $picture = new Pictures();
            $picture->filename = basename($genericFilename);
            $picture->datetime = new \DateTime($dateTimeOfPicture->get('yyyy-MM-dd HH:mm:ss'));

            $this->_em->persist($picture);
            $this->_em->flush();

            $this->infoMessage("Picture ${$uploadedPicture} initialized");
            $this->_redirect('/admin/picture/index');
        }
    }

}
