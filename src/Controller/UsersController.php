<?php
namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends FOSRestController
{
    private $userRepository;
    private $em;

public function __construct(UserRepository $userRepository,EntityManagerInterface $em)
{
        $this->userRepository = $userRepository;
        $this->em =$em;
}

/**
* @Rest\View(serializerGroups={"user"})
*/
public function getUsersAction()
{
    $users = $this->userRepository->findAll();
    return $this->view($users);
} // "get_users"[GET] /users
public function getUserAction(User $user)
{
    return $this->view($user);
} 

/**
* @Rest\Post("/users")
* @ParamConverter("user", converter="fos_rest.request_body")
*/
public function postUsersAction(User $user)
{
        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user);
} 

public function putUserAction(Request $request, int $id)
{
    $user_data = $this->userRepository->find($id);
        // $request->get()
        if ($fn = $request->get('firstname') )
        {
            $user_data->setFirstname($fn);
        }
        if ($ln = $request->get('lastname') )
        {
            $user_data->setLastname($ln);
        }
        if ($email = $request->get('email') )
        {
            $user_data->setEmail($email);
        }
        $this->em->persist($user_data);
        $this->em->flush();
        return $this->view($user_data);
} 
 /**
  * @Rest\View(serializerGroups={"user"})
  */
public function deleteUserAction(User $user)
{
    $this->em->remove($user);
    $this->em->flush();
    return $this->view($user);
} 
}