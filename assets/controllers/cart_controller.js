import { Controller } from "@hotwired/stimulus";
import { CartManager } from "../cart_manager.js";

export default class extends Controller {
    static targets = ["panel", "overlay", "button", "closeBtn", "list", "total", "empty", "payment"];

    isOpen = false;

    connect() {
        this.buttonTarget.addEventListener("click", () => this.toggle());
        this.overlayTarget.addEventListener("click", () => this.close());
        this.closeBtnTarget.addEventListener("click", () => this.close());

        this.render();
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

    // --- Rendu du panier ---

    render() {
        // Panier vide ?
        if (CartManager.items.length === 0) {
            this.emptyTarget.classList.remove("hidden");
            this.listTarget.innerHTML = "";
            this.totalTarget.textContent = "0 €";
            this.paymentTarget.style.display = "none";
            return;
        }

        // Panier non vide
        this.emptyTarget.classList.add("hidden");
        this.listTarget.innerHTML = "";

        CartManager.items.forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.name} — ${item.qty}`;
            this.listTarget.appendChild(li);
        });

        this.totalTarget.textContent = CartManager.total() + " €";
    }
}
