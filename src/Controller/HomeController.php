<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use App\Repository\PlatformRepository;
use App\Repository\AdvertisingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PlatformRepository $platformRepo, AdvertisingRepository $ar): Response
    {
        $platforms = $platformRepo->findPremiums();

        $activeUserAdd = $ar->findActiveUserAdd();

        return $this->render('home/index.html.twig', [
            'platforms' => $platforms,
            'activeUserAdd' => $activeUserAdd
        ]);
    }

    /**
     * @Route("/cgu", name="cgu")
     */
    public function cgu(): Response
    {
        return $this->render('home/cgu.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject($form->get('name')->getData())
            ->text($form->get('subject')->getData())
            ;

            $mailer->send($email);
        }


        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
            'current_menu' => 'contact'
        ]);
    }
}