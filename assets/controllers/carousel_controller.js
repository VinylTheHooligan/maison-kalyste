import { Controller } from '@hotwired/stimulus';
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static values = {
        autoplay: { type: Boolean, default: false },
        loop: { type: Boolean, default: true },
    };

    initialize() {
    }

    connect() {
        this.swiper = new window.Swiper(this.element, {
            loop: true,
            parallax: true,
            autoplay: {
              delay: 6500,
            },
            speed: 300,
            autoHeight: true,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }

    // Add custom controller actions here
    // fooBar() { this.fooTarget.classList.toggle(this.bazClass) }

    disconnect() {
        // Called anytime its element is disconnected from the DOM
        // (on page change, when it's removed from or moved in the DOM, etc.)

        // Here you should remove all event listeners added in "connect()" 
        // this.fooTarget.removeEventListener('click', this._fooBar)
    }
}
