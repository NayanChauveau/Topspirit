<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="profile")
     */
    public function index(PlatformRepository $plateformRepo, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $clone = clone $user;

        $plateforms = $plateformRepo->findBy(
            ['user' => $user]
        );

        $form = $this->createForm(ProfileType::class, $clone);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($clone->getEmail() !== $user->getEmail()) $user->setEmail($clone->getEmail());

            $em->persist($user);
            $em->flush();
        }

        return $this->render('profile/index.html.twig', [
            'plateforms' => $plateforms,
            'form' => $form->createView()
        ]);
    }
}
