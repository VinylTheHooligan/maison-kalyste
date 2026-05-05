import { Controller } from "@hotwired/stimulus";
import { CartManager } from "../cart_manager.js";

export default class extends Controller {
    static targets = [
        "panel", "overlay", "button", "closeBtn",
        "list", "total", "empty", "payment", "count"
    ];

    isOpen = false;

    async connect() {
        // Targets optionnels — présents uniquement si le header est dans le DOM
        if (this.hasButtonTarget) {
            this.buttonTarget.addEventListener("click", () => this.toggle());
        }
        if (this.hasOverlayTarget) {
            this.overlayTarget.addEventListener("click", () => this.close());
        }
        if (this.hasCloseBtnTarget) {
            this.closeBtnTarget.addEventListener("click", () => this.close());
        }

        document.addEventListener("cart:updated", (e) => this.render(e.detail));
        await CartManager.fetchCart();
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        this.panelTarget.classList.remove("translate-x-full");
        this.overlayTarget.classList.remove("opacity-0", "pointer-events-none");
    }

    close() {
        this.isOpen = false;
        this.panelTarget.classList.add("translate-x-full");
        this.overlayTarget.classList.add("opacity-0", "pointer-events-none");
    }

    // -------------------------------------------------------------------------
    // Rendu
    // -------------------------------------------------------------------------

    render(cart) {
        // Compteur dans la navbar
        if (this.hasCountTarget) {
            this.countTarget.textContent = cart.count > 0 ? cart.count : '';
            this.countTarget.classList.toggle("hidden", cart.count === 0);
        }

        if (!this.hasEmptyTarget) return;

        // Panier vide
        if (cart.items.length === 0) {
            this.emptyTarget.classList.remove("hidden");
            this.listTarget.innerHTML = "";
            this.totalTarget.textContent = "0,00 €";
            this.paymentTarget.style.display = "none";
            return;
        }

        // Panier non vide
        this.emptyTarget.classList.add("hidden");
        this.paymentTarget.style.display = "";
        this.listTarget.innerHTML = "";

        cart.items.forEach(item => {
            const li = document.createElement("li");
            li.className = "flex items-center justify-between gap-4 py-4 border-b border-black/10";
            li.dataset.productId = item.productId;
            li.innerHTML = `
                <div class="flex-1">
                    <p class="font-semibold text-sm">${item.name}</p>
                    <p class="text-xs opacity-60">${this.formatPrice(item.unitPrice)} / unité</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        class="qty-btn w-6 h-6 border border-black/20 flex items-center justify-center text-sm hover:bg-black/5"
                        data-action="decrement"
                        data-product-id="${item.productId}"
                        data-quantity="${item.quantity}"
                    >−</button>
                    <span class="w-6 text-center text-sm font-medium">${item.quantity}</span>
                    <button
                        class="qty-btn w-6 h-6 border border-black/20 flex items-center justify-center text-sm hover:bg-black/5"
                        data-action="increment"
                        data-product-id="${item.productId}"
                        data-quantity="${item.quantity}"
                        data-stock="${item.stock}"
                    >+</button>
                </div>
                <div class="text-right min-w-16">
                    <p class="font-bold text-sm">${this.formatPrice(item.subtotal)}</p>
                    <button
                        class="remove-btn text-xs opacity-40 hover:opacity-100 hover:text-red-600 transition"
                        data-product-id="${item.productId}"
                    >
                        Supprimer
                    </button>
                </div>
            `;
            this.listTarget.appendChild(li);
        });

        // Total
        this.totalTarget.textContent = this.formatPrice(cart.total);

        // Events sur les boutons dynamiques
        this.listTarget.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleQuantity(e));
        });

        this.listTarget.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleRemove(e));
        });
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    async handleQuantity(e)
    {
        const btn       = e.currentTarget;
        const productId = parseInt(btn.dataset.productId);
        const action    = btn.dataset.action;
        const current   = parseInt(btn.dataset.quantity);
        const stock     = parseInt(btn.dataset.stock ?? 99);

        const newQty = action === 'increment'
            ? Math.min(current + 1, stock)
            : Math.max(current - 1, 0);

        try {
            await CartManager.updateItem(productId, newQty);
        } catch (err) {
            console.error(err.message);
        }
    }

    async handleRemove(e) {
        const productId = parseInt(e.currentTarget.dataset.productId);
        await CartManager.removeItem(productId);
    }

    // Appelé depuis product/show.html.twig via data-action
    async addItem(e)
    {
        const btn = e.currentTarget;
        console.log('dataset complet :', JSON.stringify(btn.dataset));
        console.log('productId :', btn.dataset.productId);

        const productId = parseInt(btn.dataset.productId);

        if (isNaN(productId)) {
            console.error('productId invalide, vérifier data-product-id sur le bouton');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Ajout en cours...';

        try {
            await CartManager.addItem(productId, 1);
            btn.textContent = 'Ajouté ✓';
            setTimeout(() => {
                btn.disabled    = false;
                btn.textContent = 'Ajouter au panier';
            }, 2000);
            this.open();
        } catch (err) {
            btn.textContent = err.message;
            btn.disabled    = false;
        }
    }

    // -------------------------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------------------------

    formatPrice(cents) {
        return (cents / 100).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }) + ' €';
    }
}