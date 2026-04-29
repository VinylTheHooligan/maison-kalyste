export const CartManager = {
    items: [],

    dispatch() {
        document.dispatchEvent(new CustomEvent("cart:updated", {
            detail: { items: this.items }
        }));
    },

    addItem(item) {
        this.items.push(item);
        this.dispatch();
    },

    removeItem(id) {
        this.items = this.items.filter(i => i.id !== id);
        this.dispatch();
    },

    clear() {
        this.items = [];
        this.dispatch();
    },

    total() {
        return this.items.reduce((t, i) => t + i.price * i.qty, 0);
    }
};
