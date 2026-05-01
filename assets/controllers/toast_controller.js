import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        duration: { type: Number, default: 5000 } // auto-dismiss
    }

    connect() {
        this.element.classList.add('opacity-0', 'translate-y-2');
        requestAnimationFrame(() => {
            this.element.classList.remove('opacity-0', 'translate-y-2');
        });

        if (this.durationValue > 0) {
            setTimeout(() => this.dismiss(), this.durationValue);
        }
    }

    dismiss() {
        this.element.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => this.element.remove(), 200);
    }
}
