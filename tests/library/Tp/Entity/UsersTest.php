<?php

namespace Tp\Entity;
/**
 * Description of UserTest
 *
 * @author jon
 */
class UsersTest
	extends \ModelTestCase
{
	/**
	 *
	 * @return User
	 */
	public function testCanSaveUserAndRetrieveIt()
	{
		$user = new Users();
        $user->username = "admin";
        $user->email = "admin@admin.dev";
        $user->password = "password";
        $user->salt = md5("secretString");
        $user->userType = 0;
        $em = $this->doctrineContainer->getEntityManager();
        $em->persist($user);
        $em->flush();
	}

}
