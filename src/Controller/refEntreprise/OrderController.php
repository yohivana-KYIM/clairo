<?php

namespace App\Controller\refEntreprise;

use App\Entity\EntrepriseUnifiee;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(CartService $cart, Request $request): Response
    {
        $email = $this->getUser()->getEmail();
        $entreprise = $this->em->getRepository(EntrepriseUnifiee::class)->findByEmailReferent($email);

        if (!$entreprise) {
            throw $this->createNotFoundException('Entreprise non trouvée.');
        }

        $orderLast = $this->em->getRepository(Order::class)->findOneBy(['User' => $this->getUser()], ['id' => 'DESC']);
        $form = $this->createForm(OrderType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();

            if ($orderLast && $orderLast->getIsPaid() == 0) {
                $order = $orderLast;

                foreach ($order->getOrderDetails() as $orderDetail) {
                    $this->em->remove($orderDetail);
                }
            } else {
                $order = new Order();
                $reference = $date->format('dmY') . '' . uniqid();
                $order->setReference($reference);
                $order->setUser($this->getUser());
                $order->setCreatedAt($date);
                $order->setIsPaid(0);
                $order->setState(0);
                $this->em->persist($order);
            }

            foreach ($cart->getTotal() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['produit']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setDescription($product['nom'] . ' ' . $product['prenom']);
                $orderDetails->setPrice($product['produit']->getPrice());
                $orderDetails->setTotal($product['produit']->getPrice() * $product['quantity']);
                $this->em->persist($orderDetails);
            }

            $this->em->flush();

            return $this->redirectToRoute('app_order_recap', [
                'reference' => $order->getReference(),
            ]);
        }

        return $this->render('refEntreprise/order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getTotal(),
        ]);
    }

    #[Route('/commande/recapitulatif/{reference}', name: 'app_order_recap', methods:['POST', 'GET'])]
    public function add(CartService $cart, Request $request, $reference): Response
    {
        $email = $this->getUser()->getEmail();
        $entreprise = $this->em->getRepository(EntrepriseUnifiee::class)->findByEmailReferent($email);
        // $order = null;
        // $form = $this->createForm(OrderType::class, null,[
        //     'user' => $this->getUser()
        // ]);
        // $form = $this->createForm(OrderType::class);
        // $form->handleRequest($request);
            // $delivery = $form->get('adresse')->getData();
            // $delivery_content = $delivery->getfirstname().' '.$delivery->getlastname();
            // $delivery_content .= '<br/>'.$delivery->getPhone();

            // if ($delivery->getCompany()) {
            //     $delivery_content .= '</br>'.$delivery->getCompany();
            // }
            // $delivery_content .= '</br>'.$delivery->getAddress();
            // $delivery_content .= '</br>'.$delivery->getPostalCode().''.$delivery->getCity();
            // $delivery_content .= '</br>'.$delivery->getCountry();

            //enregistrement commande
                                                                                                                    // $order = new Order();

            // $reference = $date->format('dmY') . '' . uniqid();
            // $order->setReference($reference);
            // $order->setUser($this->getUser());
            // $order->setCreatedAt($date);
            // $order->setIsPaid(0);
            // $order->setState(0);
            // $this->em->persist($order);

            // //enregistrement de mes produits dans orderDetails()
            // foreach ($cart->getTotal() as $product) {
            //     $orderDetails = new OrderDetails();
            //     $orderDetails->setMyOrder($order);
            //     $orderDetails->setProduct($product['produit']->getName());
            //     $orderDetails->setQuantity($product['quantity']);
            //     $orderDetails->setDescription($product['nom'] . ' ' . $product['prenom']);
            //     $orderDetails->setPrice($product['produit']->getPrice());
            //     $orderDetails->setTotal($product['produit']->getPrice() * $product['quantity']);
            //     $this->em->persist($orderDetails);
            // }

            // $this->em->flush();
        // }

        return $this->render('refEntreprise/order/add.html.twig', [
                'cart' => $cart->getTotal(),
                'reference' => $reference,
                'entreprise' => $entreprise[0],
            ]);
        // }
    }
}
