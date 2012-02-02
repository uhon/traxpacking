<?php

namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 * 
 * @property int $userId 
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property int $userType
 * @property string $email
 * @property string $createddate
 * @property string $modifieddate
 *
 * @ORM\Table(name="users",
 * 		uniqueConstraints={
 *	@ORM\UniqueConstraint(name="users_username",columns={"username"})
 *	, @ORM\UniqueConstraint(name="users_email",columns={"email"})
 * 		},
 * 		indexes={@ORM\Index(name="users_user_type", columns={"user_type"})}
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Users
{
	/**
	 * @var integer $userId
	 *
	 * @ORM\Column(name="user_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $userId;

	/**
	 * @var string $username
	 *
	 * @ORM\Column(name="username", type="string", length=255, nullable=false)
	 */
	private $username;

	/**
	 * @var string $password
	 *
	 * @ORM\Column(name="password", type="string", length=255, nullable=false)
	 */
	private $password;

	/**
	 * @var integer $userType
	 *
	 * @ORM\Column(name="user_type", type="integer", nullable=false)
	 */
	private $userType;

	/**
	 * @var string $email
	 *
	 * @ORM\Column(name="email", type="string", length=255, nullable=false)
	 */
	private $email;

	/**
	 * @var datetime $createddate
	 *
	 * @ORM\Column(name="createddate", type="datetime", nullable=false)
	 */
	private $createddate;

	/**
	 * @var datetime $modifieddate
	 *
	 * @ORM\Column(name="modifieddate", type="datetime", nullable=false)
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
