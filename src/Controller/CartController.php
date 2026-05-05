<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

#[Route('/api/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    public function __construct(private CartService $cartService) {}

    #[Route('', name: 'get', methods: ['GET'])]
    public function get(#[CurrentUser] ?User $user): JsonResponse
    {
        return $this->json($this->cartService->getCart($user));
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(
        Request $request,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $productId = $data['productId'] ?? null;
        $quantity  = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->json(['error' => 'productId manquant'], Response::HTTP_BAD_REQUEST);
        }

        $cart = $this->cartService->addItem($user, (int) $productId, (int) $quantity);

        if (isset($cart['error'])) {
            return $this->json($cart, Response::HTTP_BAD_REQUEST);
        }

        return $this->json($cart);
    }

    #[Route('/update/{productId}', name: 'update', methods: ['PATCH'])]
    public function update(
        int $productId,
        Request $request,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        $data     = json_decode($request->getContent(), true);
        $quantity = $data['quantity'] ?? null;

        if ($quantity === null) {
            return $this->json(['error' => 'quantity manquante'], Response::HTTP_BAD_REQUEST);
        }

        $cart = $this->cartService->updateItem($user, $productId, (int) $quantity);

        if (isset($cart['error'])) {
            return $this->json($cart, Response::HTTP_BAD_REQUEST);
        }

        return $this->json($cart);
    }

    #[Route('/remove/{productId}', name: 'remove', methods: ['DELETE'])]
    public function remove(
        int $productId,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        $cart = $this->cartService->removeItem($user, $productId);
        return $this->json($cart);
    }
}