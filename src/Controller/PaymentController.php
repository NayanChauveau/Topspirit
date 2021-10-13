<?php

namespace App\Controller;

use App\Entity\Advertising;
use Stripe\Stripe;
use App\Entity\Platform;
use App\Entity\Subscriptions;
use App\Repository\AdvertisingRepository;
use App\Repository\SubscriptionsRepository;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Monolog\DateTimeImmutable;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment/{id}", name="payment")
     */
    public function index(Platform $platform): Response
    {

        $user = $this->getUser();

        if ($user !== $platform->getUser()) throw new AccessDeniedHttpException();

        return $this->render('payment/index.html.twig', [
            'platform' => $platform
        ]);
        

    }

    /**
     * @Route("/checkout/publicite", name="advertising_checkout")
     */
    public function advertisingCheckout($stripeSK, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        Stripe::setApiKey($stripeSK);

        $this->trySetClient($stripeSK, $em);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'mois publicité TopSpirit',
                    ],
                    'unit_amount' => 15000,
                ],
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'customer' => $user->getStripeId(),
            'success_url' => $this->generateUrl('success_pub_url', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    /**
     * @Route("/checkout/{id}", name="checkout")
     */
    public function checkout($stripeSK, EntityManagerInterface $em, Platform $platform): Response
    {
        $user = $this->getUser();

        if ($user !== $platform->getUser()) throw new AccessDeniedHttpException();

        Stripe::setApiKey($stripeSK);

        $this->trySetClient($stripeSK, $em);
        
        $this->trySetPrice($platform, $stripeSK, $em);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $platform->getMonthPriceId(),
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'customer' => $user->getStripeId(),
            'success_url' => $this->generateUrl('success_url', ['id' => $platform->getId()], UrlGeneratorInterface::ABSOLUTE_URL) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    /**
     * @Route("/success/publicite", name="success_pub_url")
     */
    public function successPubUrl(Request $request, $stripeSK, EntityManagerInterface $em, AdvertisingRepository $advRepo): Response
    {
        Stripe::setApiKey($stripeSK);

        $user = $this->getUser();
        $session = Session::retrieve($request->get('session_id'));
        $line_items = $session->allLineItems($session['id']);

        
        if($advRepo->doesExistsByPiId($session->payment_intent)) return $this->redirectToRoute('profile');

        $advRepo->findAll();
        
        $advertising = new Advertising;
        $advertising
            ->setUser($user)
            ->setPaymentIntent($session->payment_intent)
            ->setCustomerId($session->customer)
            ->setStartingDate($advRepo->dateOfDisponibility()->modify('+1 day'))
            ->setEndingDate($advRepo->dateOfDisponibility()->modify('+1 month +1 day'))
        ;
        

        $em->persist($advertising);
        $em->flush();



        return $this->render('payment/success.html.twig', [
            'customer' => '$customer'
        ]);
    }

    /**
     * @Route("/success/{id}", name="success_url")
     */
    public function successUrl(Request $request, $stripeSK, SubscriptionsRepository $subRepo, Platform $platform, EntityManagerInterface $em): Response
    {
        Stripe::setApiKey($stripeSK);

        $user = $this->getUser();
        $session = Session::retrieve($request->get('session_id'));
        $line_items = $session->allLineItems($session['id']);

        
        if($subRepo->doesExistsByPiId($session->payment_intent)) return $this->redirectToRoute('profile');
        if($platform->getUser() !== $user) throw new AccessDeniedHttpException();
        
        $subscription = new Subscriptions();
        $subscription
            ->setUser($user)
            ->setPlatform($platform)
            ->setPaymentIntent($session->payment_intent)
            ->setCustomerId($session->customer)
            ->setProductId($line_items->data[0]->price->product)
            ->setCreationDate(new \DateTimeImmutable())
        ;
        
        $platform->setEndOfSubscription(
            $platform->getEndOfSubscription() === null || $platform->getEndOfSubscription() <  (new \DateTimeImmutable()) ?
            (new \DateTimeImmutable())->modify('+1 month') :
                $platform->getEndOfSubscription()->modify('+1 month')
            );

        $em->persist($subscription);
        $em->persist($platform);
        $em->flush();



        return $this->render('payment/success.html.twig', [
            'customer' => '$customer'
        ]);
    }

    /**
     * @Route("/cancel", name="cancel_url")
     */
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }


    private function trySetClient($stripeSK, $em) {
        $user = $this->getUser();

        if($user->getStripeId() === null) {
            $stripe = new StripeClient($stripeSK);
            $customer = $stripe->customers->create();
    
            $user->setStripeId($customer['id']);
    
            $em->persist($user);
            $em->flush();
        }
    }

    private function trySetPrice($platform, $stripeSK, $em) {
        if($platform->getStripeId() === null) {
            $stripe = new StripeClient($stripeSK);
            $product = $stripe->products->create([
                'name' => $platform->getId() . ' - ' . $platform->getName()
            ]);
            $price = $stripe->prices->create([
                'unit_amount' => 5000,
                'currency' => 'eur',
                'product' => $product['id']
            ]);

            $platform->setStripeId($product['id']);
            $platform->setMonthPriceId($price['id']);

            $em->persist($platform);
            $em->flush();
        }
    }
}
