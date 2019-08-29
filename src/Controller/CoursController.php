<?php

namespace App\Controller;

use App\Entity\Cours;
use FOS\RestBundle\View\ViewHandler;
use Doctrine\ORM\EntityManager;
use App\Repository\CoursRepository;
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

class CoursController extends FOSRestController
{
    private $coursRepository;
    private $em;

    public function __construct(CoursRepository $coursRepository, EntityManagerInterface $em)
    {
        $this->coursRepository = $coursRepository;
        $this->em = $em;
    }
    /**
     * @Route("/cours", name="cours")
     */
    public function index()
    {
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
        ]);
    }

    /**
     * @Rest\Get("/test")
     */
    public function getTestEssai()
    {
        return $this->json('iLoveYou');
    }

    /**
     * ListsallCours.
     * @Rest\Get("/listcours")
     */
    public function getListCours()
    {
        $cours = $this->coursRepository->findAll();
        return $this->view($cours);
    }

    /**
     * oneCours.
     * @Rest\Get("/cours/{id}")
     */
    public function getCours(Cours $cours)
    {
        return $this->view($cours);
    }

    /**
     * @Rest\Post("/addcours")
     * @ParamConverter("cours", converter="fos_rest.request_body")
     */
    public function postCours(Cours $cours)
    {
        $this->em->persist($cours);
        $this->em->flush();
        return $this->view($cours);
    }


    /**
     * @Rest\Put("/modifiercours/{id}")
     */
    public function putCours(Request $request, int $id)
    {
        $user_data = $this->coursRepository->find($id);

        if ($_nom_cours = $request->get('_nom_cours')) {
            $user_data->setNomCours($_nom_cours);
        }
        if ($ponderation = $request->get('ponderation')) {
            $user_data->setPonderation($ponderation);
        }
        if ($code = $request->get('code')) {
            $user_data->setCode($code);
        }
        if ($titulaire = $request->get('titulaire')) {
            $user_data->setTitulaire($titulaire);
        }
        $this->em->persist($user_data);
        $this->em->flush();
        return $this->view($user_data);
    }

    /**
     * @Rest\Delete("/supprimercours/{id}")
     */

    public function deleteCours(Cours $cours)
    {
        $this->em->remove($cours);
        $this->em->flush();
        return $this->view($cours);
    }
}