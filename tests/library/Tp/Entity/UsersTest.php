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
	 */
	public function testCanSaveUserAndRetrieveIt()
	{
		$user = new Users();
        $user->username = "admin";
        $user->email = "admin@traxpacking.dev";
        $user->password = "7dd12f3a9afa0282a575b8ef99dea2a0c1becb51";
        $user->userType = 0;
        $em = $this->doctrineContainer->getEntityManager();
        $em->persist($user);
        $em->flush();
	}

}
