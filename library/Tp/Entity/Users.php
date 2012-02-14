<?php
/**
 * User: uhon
 * Date: 02.02.12
 * GitHub: git@github.com:uhon/traxpacking.git
 */

namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 * 
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string $email
 * @property string $createddate
 * @property string $modifieddate
 *
 * @ORM\Table(
 * 	   uniqueConstraints={
 *         @ORM\UniqueConstraint(name="users_username",columns={"username"}),
 *	       @ORM\UniqueConstraint(name="users_email",columns={"email"})
 * 	   }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Users
{
	/**
	 * @var integer $userId
	 *
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string $username
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $username;

	/**
	 * @var string $password
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $password;

	/**
	 * @var string $role
	 *
	 * @ORM\Column(type="string", nullable=false)
	 */
	private $role;

	/**
	 * @var string $email
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $email;

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
}
