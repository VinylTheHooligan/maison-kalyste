export const CartManager = {
    cart: { items: [], total: 0, count: 0 },

    async fetchCart() {
        const response = await fetch('/api/cart', {
            credentials: 'same-origin',
        });
        this.cart = await response.json();
        this.dispatch();
        return this.cart;
    },

    async addItem(productId, quantity = 1)
    {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ productId, quantity }),
            credentials: 'same-origin',
        });

        if (!response.ok)
        {
            const error = await response.json();
            throw new Error(error.error ?? "Erreur lors de l'ajout");
        }

        this.cart = await response.json();
        this.dispatch();
        return this.cart;
    },

    async updateItem(productId, quantity) {
        const response = await fetch(`/api/cart/update/${productId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity }),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error ?? 'Erreur lors de la mise à jour');
        }

        this.cart = await response.json();
        this.dispatch();
        return this.cart;
    },

    async removeItem(productId) {
        const response = await fetch(`/api/cart/remove/${productId}`, {
            method: 'DELETE',
            credentials: 'same-origin',
        });

        this.cart = await response.json();
        this.dispatch();
        return this.cart;
    },

    dispatch() {
        document.dispatchEvent(new CustomEvent('cart:updated', {
            detail: this.cart,
        }));
    },

    total() {
        return this.cart.total;
    },

    count() {
        return this.cart.count;
    },
};