<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Entity\Vote;
use App\Form\PlatformType;
use App\Form\VoteType;
use Doctrine\ORM\QueryBuilder;
use App\Repository\PlatformRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ContainerEsAMPdG\PaginatorInterface_82dac15;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Monolog\DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/platform")
 */
class PlatformController extends AbstractController
{
    /**
     * @Route("/", name="platform_index", methods={"GET"})
     */
    public function index(PlatformRepository $platformRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $platforms = $platformRepository->findAllPremiumsFirst();

        $paginatePlatforms = $paginator->paginate(
            $platforms, 
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        $paginatePlatforms->setCustomParameters([
            'align' => 'center', 
        ]);


        return $this->render('platform/index.html.twig', [
            'platforms' => $paginatePlatforms,
            'current_menu' => 'platforms'
        ]);
    }

    /**
     * @Route("/new", name="platform_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UserInterface $user, SluggerInterface $slugger): Response
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

            $platform->setSlug($slugger->slug($platform->getId() . ' ' . $platform->getName()));
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
     * @Route("/{slug}/edit", name="platform_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Platform $platform, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user !== $platform->getUser()) throw new AccessDeniedHttpException();

        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre platforme ' . $platform->getName() . ' a bien été éditée');

            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->renderForm('platform/edit.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}", name="platform_delete", methods={"POST"})
     */
    public function delete(Request $request, Platform $platform, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $platform->getId(), $request->request->get('_token'))) {

            $this->addFlash('success', 'Votre platforme ' . $platform->getName() . ' a bien été supprimée');

            $em->remove($platform);
            $em->flush();
        }

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/vote/resultat", name="voteResult")
     */
    public function voteResult(): Response
    {
        return $this->render('platform/voteresult.html.twig', [
        ]);
    }

    /**
     * @Route("/vote/{redirectToken}", name="vote")
     */
    public function vote(Platform $platform, EntityManagerInterface $em, Request $request, VoteRepository $vr): Response
    {

        $form = $this->createForm(VoteType::class);
        $form->handleRequest($request);

        $actualVisitorArchive = $vr->findOneBy(['ipAddress' => $request->getClientIp()]);

        if ($actualVisitorArchive !== null && $actualVisitorArchive->getVisitedAt()->format('n') === (new \DateTimeImmutable)->format('n')) {
            $this->addFlash('vote', 'Vous ne pouvez voter qu\'une fois par mois. Revenez le mois prochain !');
            
            return $this->redirectToRoute('voteResult');
        }

        if (
            $form->isSubmitted() 
            // && $form->isValid() // TODO activer ça quand passage en prod, pour l'instant SSL error
            ) {
            if ($platform->getActualMonth()->format('n') != (new \DateTimeImmutable)->format('n')) {
                $platform->setActualMonth(new \DateTimeImmutable);
                $platform->setMonthRedirect(1);
            } else {
                $platform->setMonthRedirect($platform->getMonthRedirect() === null ? 1 : $platform->getMonthRedirect() + 1);
            }
            
            if ($actualVisitorArchive === null) $actualVisitorArchive = new Vote;

            $actualVisitorArchive->setIpAddress($request->getClientIp());
            $actualVisitorArchive->setVisitedAt(new \DateTimeImmutable);
            
    
            $em->persist($platform);
            $em->persist($actualVisitorArchive);
            $em->flush();

            $this->addFlash('vote', 'Votre vote a bien été pris en compte. Merci pour votre visite !');

            return $this->redirectToRoute('voteResult');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('vote', 'Ah ! Il semblerait que vous soyez un robot. Revenez quand les robots auront le droit de vote !'); 
            return $this->redirectToRoute('voteResult');
        } 


        return $this->render('platform/vote.html.twig', [
            'form' => $form->createView(),
            'platform' => $platform
        ]);

    }
}
