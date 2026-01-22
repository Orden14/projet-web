import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        url: String,
        detailUrl: String,
    };

    open(event) {
        const interactiveSelector = "a, button, input, textarea, select, [role='button']";
        const clickedElement = event.target.closest(interactiveSelector);
        if (clickedElement || event.target.closest('[data-controller="row-menu"]')) {
            return;
        }
        event.preventDefault();
        window.open(this.urlValue, "_blank");
    }

    openDetail(event) {
        const interactiveSelector = "a, button, input, textarea, select, [role='button']";
        const clickedElement = event.target.closest(interactiveSelector);
        if (clickedElement || event.target.closest('[data-controller="row-menu"]')) {
            return;
        }
        event.preventDefault();
        window.location.href = this.detailUrlValue;
    }
}
