<?php
/**
 * User: uhon
 * Date: 2012/04/10
 * GitHub: git@github.com:uhon/traxpacking.git
 */


namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @property int $id
 * @property string name
 * @property float long_west
 * @property float lat_south
 * @property float long_east
 * @property float lat_north
 * @property Poi $pois
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Country
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
	 * @var string $name
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $name;


    /**
     * @var Poi $pois
     *
     * @ORM\OneToMany(targetEntity="Poi", mappedBy="country")
     */
    private $pois;

    /**
   	 * @var float $long_west
   	 *
   	 * @ORM\Column(type="float")
   	 */
   	private $long_west;

    /**
   	 * @var float $lat_south
   	 *
   	 * @ORM\Column(type="float")
   	 */
   	private $lat_south;

    /**
   	 * @var float $long_east
   	 *
   	 * @ORM\Column(type="float")
   	 */
   	private $long_east;

    /**
   	 * @var float $lat_north
   	 *
   	 * @ORM\Column(type="float")
   	 */
   	private $lat_north;

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

    /**
     * @return array
     */
    public function toArray() {
        return get_object_vars($this);
    }

    public function getCountryNameArray() {
        $countryArray = array();
        $repository = \Zend_Registry::get('doctrine')->getEntityManager()->getRepository('Tp\Entity\Country');
        $countries = $repository->findBy(array(), array("name" => "ASC"));
        foreach($countries as $country) {
            $countryArray[$country->id] = $country->name;
        }
        return $countryArray;
    }

}
