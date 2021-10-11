<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Form\PlatformType;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/platform")
 */
class PlatformController extends AbstractController
{
    // /**
    //  * @Route("/", name="platform_index", methods={"GET"})
    //  */
    // public function index(PlatformRepository $platformRepository): Response
    // {
    //     return $this->render('platform/index.html.twig', [
    //         'platforms' => $platformRepository->findAll(),
    //     ]);
    // }

    /**
     * @Route("/new", name="platform_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        $platform = new Platform();
        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $platform->setUser($user);
            $platform->setRedirectToken(sha1(random_bytes(12)));

            $platform->setActualMonth(new \DateTimeImmutable);

            $em->persist($platform);
            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->renderForm('platform/new.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    // /**
    //  * @Route("/{id}", name="platform_show", methods={"GET"})
    //  */
    // public function show(Platform $platform): Response
    // {
    //     return $this->render('platform/show.html.twig', [
    //         'platform' => $platform,
    //     ]);
    // }

    /**
     * @Route("/{id}/edit", name="platform_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Platform $platform, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user !== $platform->getUser()) throw new AccessDeniedHttpException();

        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre plateforme ' . $platform->getName() . ' a bien été éditée');

            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->renderForm('platform/edit.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="platform_delete", methods={"POST"})
     */
    public function delete(Request $request, Platform $platform, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $platform->getId(), $request->request->get('_token'))) {

            $this->addFlash('success', 'Votre plateforme ' . $platform->getName() . ' a bien été supprimée');

            $em->remove($platform);
            $em->flush();
        }

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/redirect/{redirectToken}", name="redirect")
     */
    public function platformRedirect(Platform $platform, EntityManagerInterface $em): Response
    {
        if ($platform->getActualMonth()->format('n') != (new \DateTimeImmutable)->format('n')) {
            $platform->setActualMonth(new \DateTimeImmutable);
            $platform->setMonthRedirect(1);
        } else {
            $platform->setMonthRedirect($platform->getMonthRedirect() === null ? 1 : $platform->getMonthRedirect() + 1);
        }

        $em->persist($platform);
        $em->flush();

        return $this->redirectToRoute('home'); // Je pense le changer par une redirection vers la platforme
    }
}
