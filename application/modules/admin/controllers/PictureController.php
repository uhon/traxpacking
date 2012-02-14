<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Admin_PictureController extends Tp_Controller_Action
{
    public function indexAction()
    {
        $pictures = glob(APPLICATION_PATH . "/../public/upload/*.jpg");
        $this->view->pictures = array();
        foreach($pictures as $p) {
            $exif=exif_read_data($p);
            if($exif === false) {
                throw new Exception('exif-Data for picture ' . basename($p) . ' could not be read! please solve the Problem manually (exiftool)!');
            }

            $this->view->pictures[] = array(
                'name' => basename($p),
                'size' => round(filesize($p) / 1048576, 2) . 'MB',
                'datetime' => $exif['DateTimeOriginal'],
                'edit' => $this->view->linkiconEdit($this->view->url(array('action' => 'prepare', 'pic' => basename($p))), 'add')
            );
        }
    }

    public function prepareAction() {
        if(($pic = $this->_getParam('pic')) !== null) {
            $uploadedPicture = APPLICATION_PATH . "/../public/upload/" . $pic;
            $exif=exif_read_data($uploadedPicture);
            $genericFilename = APPLICATION_PATH . "/../public/cache/" . uniqid('tp_');

            $cachedPicture = $genericFilename . "_orig.jpg";

            if (!copy($uploadedPicture, $cachedPicture)) {
                throw new Exception("failed to copy $uploadedPicture to cache directory (is directory writable?)\n");
            }

            require_once APPLICATION_PATH . '/../library/phpThumb/src/ThumbLib.inc.php';

            $thumb = PhpThumbFactory::create($cachedPicture);
            $thumb->resize(900, 700);
            $thumb->save($genericFilename . '_medium.jpg');

            $thumb = PhpThumbFactory::create($cachedPicture);
            $thumb->resize(200, 200);
            $thumb->save($genericFilename . '_small.jpg');

            $this->_setParam('cachedPicture', basename($genericFilename));
            $this->_setParam('removablePicture', basename($uploadedPicture));

            $this->_forward('edit', 'poi');
        }
    }
}
