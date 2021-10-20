<?php

namespace App\Controller;

use App\Form\AdvFormType;
use App\Form\ProfileType;
use App\Form\PasswordChangeType;
use App\Repository\ArticleRepository;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdvertisingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="profile")
     */
    public function index(PlatformRepository $platformRepo, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, AdvertisingRepository $advertisingRepository, ArticleRepository $articleRepo): Response
    {
        $user = $this->getUser();
        if ($user->isVerified() === false) throw new AccessDeniedHttpException(); // Changer par une redirection vers une page qui dit de valider le mail

        $platforms = $platformRepo->findBy(
            ['user' => $user]
        );
        $articles = $articleRepo->findBy(
            ['user' => $user]
        );

        $activeAdvertising = $advertisingRepository->findActiveByUser($user);
        $actualAdvertisingUser = $advertisingRepository->findActiveUserAdd();
        $dateOfDisponibility = $advertisingRepository->dateOfDisponibility();

        $form = $this->createForm(ProfileType::class, $user);
        $passwordform = $this->createForm(PasswordChangeType::class, $user);
        $advForm = $this->createForm(AdvFormType::class, $user);

        $form->handleRequest($request);
        $passwordform->handleRequest($request);
        $advForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre compte a bien été édité');

            $em->persist($user);
            $em->flush();
            
        } elseif ($passwordform->isSubmitted() && $passwordform->isValid()) {

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $passwordform->get('plainPassword')->getData()
                )
            );

            $this->addFlash('success', 'Votre mot de passe a bien été changé');
            
            $em->persist($user);
            $em->flush();
            
        } elseif ($advForm->isSubmitted() && $advForm->isValid()) {

            $this->addFlash('success', 'Votre bannière de pub a bien été éditée');
            
            $em->persist($user);
            $em->flush();
        }

        return $this->render('profile/index.html.twig', [
            'platforms' => $platforms,
            'articles' => $articles,
            'form' => $form->createView(),
            'passwordform' => $passwordform->createView(),
            'advform' => $advForm->createView(),
            'advertisings' => $activeAdvertising,
            'actualAdvertisingUser' => $actualAdvertisingUser,
            'disponibility' => $dateOfDisponibility
        ]);
    }
}
