<?php
/**
 * User: uhon
 * Date: 02.02.12
 * GitHub: git@github.com:uhon/traxpacking.git
 */

namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Poi
 * 
 * @property int $id
 * @property float $latitude
 * @property float $longitude
 * @property float $svgCoordinates
 * @property float $svgPrevCoordinates
 * @property PoiCategory $category
 * @property string $title
 * @property string $url
 * @property Country $country
 * @property Picture $pictures
 * @property Route $routes
 * @property \Doctrine\Common\DateTime\DateTime $createddate
 * @property \Doctrine\Common\DateTime\DateTime $modifieddate
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Poi
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var float $latitude
	 *
	 * @ORM\Column(type="float", nullable=false)
	 */
	private $latitude;

	/**
	 * @var float $longitude
	 *
	 * @ORM\Column(type="float", nullable=false)
	 */
	private $longitude;

    /**
   	 * @var string $svgCoordinates
   	 *
   	 * @ORM\Column(type="string", nullable=false)
   	 */
   	private $svgCoordinates;

    /**
     * @var string $svgPrevCoordinates
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $svgPrevCoordinates;

    /**
	 * @var PoiCategory $category
	 *
	 * @ORM\ManyToOne(targetEntity="PoiCategory")
	 */
	private $category;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $title;

	/**
	 * @var string $url
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $url;


    /**
     * @var Country $country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     */
    private $country;


    /**
     * @var Picture $pictures
     *
     * @ORM\OneToMany(targetEntity="Picture", mappedBy="poi")
     */
	private $pictures;





	/**
	 * @var \Doctrine\Common\DateTime\DateTime $createddate
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	private $createddate;

	/**
	 * @var \Doctrine\Common\DateTime\DateTime $modifieddate
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	private $modifieddate;
	
	/**
	 * @ORM\PrePersist
	 */
	public function datePrePersist() {
		$this->createddate = new \DateTime('now');
		$this->modifieddate = $this->createddate;
	}
	
	/**
	 * @ORM\PreUpdate 
	 */
	public function datePreUpdate () {
		$this->modifieddate = new \DateTime('now');
	}

    /**
	 * @param string
	 * @param mixed
	 * @return self
	 */
	public function __set($property, $value)  {
		if(method_exists($this, 'set' . ucfirst($property))) {
			return call_user_func(array($this, 'set' . ucfirst($property)), $value);
		}
		else {
			$this->$property = $value;
		}
		return $this;
	}

	/**
	 *
	 * @param string
	 * @return mixed
	 */
	public function __get($property) {
		if(method_exists($this, 'get' . ucfirst($property))) {
			return call_user_func(array($this, 'get' . ucfirst($property)));
		}
		else {
			return $this->$property;
		}
	}


    public function getPoiTitleArray() {
        $poiArray = array();
        $repository = \Zend_Registry::get('doctrine')->getEntityManager()->getRepository('Tp\Entity\Poi');
        $pois = $repository->findBy(array(), array("id" => "DESC"));
        foreach($pois as $poi) {
            $poiArray[$poi->id] = $poi->title;
        }
        return $poiArray;
    }

    public function getPoiIdOfLastAssignedPicture() {
        $poiArray = array();
        $collection =  \Zend_Registry::get('doctrine')->getEntityManager()
                ->getRepository('Tp\Entity\Picture')
                ->findBy(array(), array('modifieddate' => 'DESC'));

        foreach($collection as $picture) {
            if($picture->poi !== null) {
                return $picture->poi->id;
            }
        }
        return null;
    }

    public function getPoisAsJsonArray($makeLastWithUrlCurrent = false) {
        $allPois = \Zend_Registry::get('doctrine')->getEntityManager()->getRepository('Tp\Entity\Poi')->findAll();

        $poiArray = array();

        $lastPoiWithUrl = null;
        $currentSet = false;

        foreach($allPois as $poi)  {
            $poiArray[$poi->id] = array(
                "id" => $poi->id,
                "svgCoords" => $poi->svgCoordinates,
                "svgPrevCoords" => $poi->svgPrevCoordinates,
                "latitude" => $poi->latitude,
                "longitude" => $poi->longitude,
                "title" => $poi->title,
                "url" => $poi->url,
            );
            if($poi->id === $this->id) {
                $poiArray[$poi->id]["current"] = true;
                $currentSet = true;
            }
            if(!empty($poi->url)) {
                $lastPoiWithUrl = $poi->id;
            }
        }
        if($makeLastWithUrlCurrent && $currentSet == false && $lastPoiWithUrl !== null) {
            $poiArray[$lastPoiWithUrl]["current"] = true;
        }

        $poiArray = \Zend_Json::encode($poiArray);
        str_replace('"', "'", $poiArray);
        return $poiArray;
    }


    public function getPicturesOrderedByDate() {
        $collection =  \Zend_Registry::get('doctrine')->getEntityManager()
                ->getRepository('Tp\Entity\Picture')
                ->findBy(array('poi' => $this->id), array('datetime' => 'ASC'));
        return $collection;
    }

    public function getLatestPoiWithPicturesAndUrl() {
        $qb = \Zend_Registry::get('doctrine')->getEntityManager()->createQueryBuilder();
        $picturesByPoi = $qb
            ->select('pic, poi')
            ->from('\Tp\Entity\Picture', 'pic')
            ->innerJoin('pic.poi', 'poi')
            ->add('where', $qb->expr()->orX(
                $qb->expr()->isNotNull('poi.url')
            ))
            ->orderBy('poi.id', 'ASC')
            ->groupBy('poi')
            ->getQuery()->execute();

        $lastOne = array_pop($picturesByPoi);
        return $lastOne->poi;
    }

	/**
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}
    
    public function getEditUrl() {
        return \Tp_Shortcut::getView()->url(
            array(
                'module' => 'admin',
                'controller' => 'poi',
                'action' => 'edit',
                'poi' => $this->id
            ), null, true
        );
    }

    public function getDeleteUrl() {
        return \Tp_Shortcut::getView()->url(
            array(
                'module' => 'admin',
                'controller' => 'poi',
                'action' => 'delete',
                'poi' => $this->id
            ), null, true
        );
    }
}
