<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\CartStatus;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const SESSION_KEY = 'guest_cart';

    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack           $requestStack,
        private CartRepository         $cartRepository,
        private CartItemRepository     $cartItemRepository,
        private ProductRepository      $productRepository,
    ) {}

    public function getCart(?User $user): array
    {
        if ($user) {
            return $this->getDbCart($user);
        }

        return $this->getSessionCart();
    }

    public function addItem(?User $user, int $productId, int $quantity = 1): array
    {
        $product = $this->productRepository->find($productId);

        if (!$product || !$product->isInStock()) {
            return ['error' => 'Produit indisponible'];
        }

        if ($quantity < 1 || $quantity > $product->getStockQuantity()) {
            return ['error' => 'Quantité invalide'];
        }

        if ($user) {
            return $this->addItemToDb($user, $product, $quantity);
        }

        return $this->addItemToSession($product, $quantity);
    }

    public function updateItem(?User $user, int $productId, int $quantity): array
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            return ['error' => 'Produit introuvable'];
        }

        if ($quantity < 1) {
            return $this->removeItem($user, $productId);
        }

        if ($quantity > $product->getStockQuantity()) {
            return ['error' => 'Stock insuffisant'];
        }

        if ($user) {
            return $this->updateItemInDb($user, $productId, $quantity);
        }

        return $this->updateItemInSession($productId, $quantity);
    }

    public function removeItem(?User $user, int $productId): array
    {
        if ($user) {
            return $this->removeItemFromDb($user, $productId);
        }

        return $this->removeItemFromSession($productId);
    }

    // fusion guest (lorsqu'un panier invité se fusionne avec le panier d'un connecté)

    public function mergeGuestCartOnLogin(User $user): void
    {
        $session   = $this->requestStack->getSession();
        $guestCart = $session->get(self::SESSION_KEY, []);

        if (empty($guestCart)) {
            return;
        }

        foreach ($guestCart as $entry) {
            $product = $this->productRepository->find($entry['productId']);
            if (!$product || !$product->isInStock()) {
                continue;
            }

            $quantity = min($entry['quantity'], $product->getStockQuantity());
            $this->addItemToDb($user, $product, $quantity);
        }

        $session->remove(self::SESSION_KEY);
    }

    // db (pour les utilisateurs connecté)

    private function getDbCart(User $user): array
    {
        $cart = $this->getOrCreateDbCart($user);
        return $this->normalizeDbCart($cart);
    }

    private function addItemToDb(User $user, Product $product, int $quantity): array
    {
        $cart     = $this->getOrCreateDbCart($user);
        $existing = $this->cartItemRepository->findOneBy([
            'cart'    => $cart,
            'product' => $product,
        ]);

        if ($existing) {
            $newQty = $existing->getQuantity() + $quantity;
            $newQty = min($newQty, $product->getStockQuantity());
            $existing->setQuantity($newQty);
        } else {
            $item = new CartItem();
            $item->setCart($cart);
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $item->setUnitPrice($product->getPrice());
            $this->em->persist($item);
        }

        $this->em->flush();
        return $this->normalizeDbCart($cart);
    }

    private function updateItemInDb(User $user, int $productId, int $quantity): array
    {
        $cart = $this->getOrCreateDbCart($user);
        $item = $this->cartItemRepository->findOneBy([
            'cart'    => $cart,
            'product' => $productId,
        ]);

        if ($item) {
            $item->setQuantity($quantity);
            $this->em->flush();
        }

        return $this->normalizeDbCart($cart);
    }

    private function removeItemFromDb(User $user, int $productId): array
    {
        $cart = $this->getOrCreateDbCart($user);
        $item = $this->cartItemRepository->findOneBy([
            'cart'    => $cart,
            'product' => $productId,
        ]);

        if ($item) {
            $this->em->remove($item);
            $this->em->flush();
        }

        return $this->normalizeDbCart($cart);
    }

    private function getOrCreateDbCart(User $user): Cart
    {
        $cart = $user->getCart();

        if (!$cart || $cart->getStatus() !== CartStatus::ACTIVE)
        {
            $cart = new Cart();
            $cart->setStatus(CartStatus::ACTIVE);
            $cart->setCreatedAt(new \DateTimeImmutable());
            $cart->setOwner($user);
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }

    private function normalizeDbCart(Cart $cart): array
    {
        $items = [];
        $total = 0;

        foreach ($cart->getItems() as $item) {
            $subtotal = $item->getUnitPrice() * $item->getQuantity();
            $total += $subtotal;

            $items[] = [
                'productId' => $item->getProduct()->getId(),
                'name'      => $item->getProduct()->getName(),
                'price'     => $item->getUnitPrice(),
                'quantity'  => $item->getQuantity(),
                'subtotal'  => $subtotal,
                'stock'     => $item->getProduct()->getStockQuantity(),
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'count' => array_sum(array_column($items, 'quantity')),
        ];
    }

    // session guest

    private function getSessionCart(): array
    {
        $entries = $this->requestStack->getSession()->get(self::SESSION_KEY, []);
        return $this->normalizeSessionCart($entries);
    }

    private function addItemToSession(Product $product, int $quantity): array
    {
        $session = $this->requestStack->getSession();
        $entries = $session->get(self::SESSION_KEY, []);

        $found = false;
        foreach ($entries as &$entry) {
            if ($entry['productId'] === $product->getId()) {
                $entry['quantity'] = min(
                    $entry['quantity'] + $quantity,
                    $product->getStockQuantity()
                );
                $found = true;
                break;
            }
        }

        if (!$found) {
            $entries[] = [
                'productId' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $quantity,
                'stock' => $product->getStockQuantity(),
            ];
        }

        $session->set(self::SESSION_KEY, $entries);
        return $this->normalizeSessionCart($entries);
    }

    private function updateItemInSession(int $productId, int $quantity): array
    {
        $session = $this->requestStack->getSession();
        $entries = $session->get(self::SESSION_KEY, []);

        foreach ($entries as &$entry) {
            if ($entry['productId'] === $productId) {
                $entry['quantity'] = $quantity;
                break;
            }
        }

        $session->set(self::SESSION_KEY, $entries);
        return $this->normalizeSessionCart($entries);
    }

    private function removeItemFromSession(int $productId): array
    {
        $session = $this->requestStack->getSession();
        $entries = $session->get(self::SESSION_KEY, []);
        $entries = array_values(array_filter($entries, fn($e) => $e['productId'] !== $productId));
        $session->set(self::SESSION_KEY, $entries);
        return $this->normalizeSessionCart($entries);
    }

    private function normalizeSessionCart(array $entries): array
    {
        $items = [];
        $total = 0;

        foreach ($entries as $entry) {
            $subtotal = $entry['price'] * $entry['quantity'];
            $total += $subtotal;

            $items[] = [
                'productId' => $entry['productId'],
                'name'      => $entry['name'],
                'price'     => $entry['price'],
                'quantity'  => $entry['quantity'],
                'subtotal'  => $subtotal,
                'stock'     => $entry['stock'],
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'count' => array_sum(array_column($items, 'quantity')),
        ];
    }
}