<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Form\PasswordChangeType;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="profile")
     */
    public function index(PlatformRepository $plateformRepo, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if ($user->isVerified() === false) throw new AccessDeniedHttpException(); // Changer par une redirection vers une page qui dit de valider le mail

        $plateforms = $plateformRepo->findBy(
            ['user' => $user]
        );

        $form = $this->createForm(ProfileType::class, $user);
        $passwordform = $this->createForm(PasswordChangeType::class, $user);

        $form->handleRequest($request);
        $passwordform->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
        } elseif ($passwordform->isSubmitted() && $passwordform->isValid() && $passwordHasher->isPasswordValid($user, $passwordform->get('plainPassword')->getData())) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $passwordform->get('plainPassword')->getData()
                )
            );
            $em->persist($user);
            $em->flush();
        } elseif ($passwordform->isSubmitted() && $passwordform->isValid()) {
            $passwordform->addError(new FormError('Ancien mot de passe incorrect'));
        }

        return $this->render('profile/index.html.twig', [
            'plateforms' => $plateforms,
            'form' => $form->createView(),
            'passwordform' => $passwordform->createView()
        ]);
    }
}
