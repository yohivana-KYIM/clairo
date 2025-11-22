<?php

namespace App\Controller\refEntreprise;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(CartService $cart): Response
    {

        $totalcart = $cart->getTotal();

        return $this->render('refEntreprise/cart/index.html.twig', [
            'totalcart' => $totalcart
        ]);
    }

    #[Route('/cart/add/{id}/{stepType}/{stepId}', name: 'app_add_to_cart')]
    public function add(CartService $cart, int $id, string $stepType, int $stepId): RedirectResponse
    {
        $cart->add($id, $stepId, $stepType);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_remove_cart')]
    public function remove(CartService $cart): RedirectResponse
    {
        $cart->clear();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{stepType}/{stepId}/{id<\d+>}', name: 'app_delete_cart')]
    public function delete(CartService $cart, int $id, string $stepType, int $stepId): RedirectResponse
    {
        $cart->delete($id, $stepId, $stepType);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{stepType}/{stepId}/{id<\d+>}', name: 'app_decrease_cart')]
    public function decrease(CartService $cart, int $id, string $stepType, int $stepId): RedirectResponse
    {
        $cart->delete($id, $stepId, $stepType);

        return $this->redirectToRoute('app_cart');
    }
}
