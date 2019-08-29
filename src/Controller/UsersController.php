<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\View\ViewHandler;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;


class UsersController extends FOSRestController
{
    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/serge")
     */
    public function getSergeAwake()
    {
        return $this->json('serge');
    }

    /**
     *ListsallUsers.
     *@Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $users = $this->userRepository->findAll();
        return $this->view($users);
    }

    /**
     * @Rest\Post("/add-users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function postUsersAction(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user);
    }

    /**
     * @Rest\Get("/user/{id}")
     */
    public function getUserAction(Request $request, User $user)
    {

        $apikey = $request->headers->get('authorization');

        $apikey = substr($apikey, 7, strlen($apikey));

        if (strlen($apikey) <= 0) {
            return $this->json(['Erreur' => 'Vous avez besoin d\'une ApiKey.']);
        }

        $verifExistApiKey = $this->userRepository->findBy(['apiKey' => $apikey]);

        if (!$verifExistApiKey) {
            return $this->json(['Erreur' => 'ApiKey non valide.']);
        }

        //return $this->view($user);
        //return $this->userRepository->find($user);
        $user_data = $this->userRepository->find($user);
        // $fn = $request->get('firstname');
        return $user_data;
    }
    /**
     * @Rest\Put("/users/{id}")
     */
    public function putUserAction(Request $request, int $id)
    {
        $user_data = $this->userRepository->find($id);
        // $request->get()
        if ($fn = $request->get('firstname')) {
            $user_data->setFirstname($fn);
        }
        if ($ln = $request->get('lastname')) {
            $user_data->setLastname($ln);
        }
        if ($email = $request->get('email')) {
            $user_data->setEmail($email);
        }
        $this->em->persist($user_data);
        $this->em->flush();
        return $this->view($user_data);
    }

    /**
     * supprimer cours
     * @Rest\Delete("/supprimeruser/{id}")
     */
    public function deleteUserAction(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
        return $this->view($user);
    }
}