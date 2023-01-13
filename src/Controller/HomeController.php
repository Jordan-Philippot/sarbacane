<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;


class HomeController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();
        $countUsersNafCode = 0;
        $countUsersNafCode = count($userRepository->findByCodeNafNotNull());

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user->setFirstname($data->getFirstname());
            $user->setLastname($data->getLastname());
            $user->setEmail($data->getEmail());
            $user->setPhone($data->getPhone());
            $user->setCompany($data->getCompany());
            $user->setAddress($data->getAddress());
            $user->setCity($data->getCity());
            $user->setZipCode($data->getZipCode());
            $user->setSiren($data->getSiren());
            $user->setNaf($data->getNaf());

            dump($data);
            $em = $this->doctrine->getManager();


            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Félicitations, votre inscription à bien été prise en compte! À très bientôt');

            return $this->redirectToRoute('app_home');
        }

        dump($users);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'users' => $users,
            'countUsersNafCode' => $countUsersNafCode,
            'form' => $form->createView(),
        ]);
    }
}
