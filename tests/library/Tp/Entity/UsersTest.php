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
        $user->role = "admin";
        $user->password = "7dd12f3a9afa0282a575b8ef99dea2a0c1becb51";
        $em = $this->doctrineContainer->getEntityManager();
        $em->persist($user);
        $em->flush();

        $user = new Users();
        $user->username = "member";
        $user->email = "member@traxpacking.dev";
        $user->role = "member";
        $user->password = "7dd12f3a9afa0282a575b8ef99dea2a0c1becb51";
        $em = $this->doctrineContainer->getEntityManager();
        $em->persist($user);
        $em->flush();
	}

}
