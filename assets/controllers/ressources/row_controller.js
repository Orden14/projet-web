import { Controller } from "@hotwired/stimulus";
import { Tooltip } from "bootstrap";

export default class extends Controller {
    static values = {
        url: String,
    };

    connect() {
        // Initialiser la tooltip Bootstrap sur la ligne
        if (this.element.hasAttribute("data-bs-toggle")) {
            this.tooltip = new Tooltip(this.element);
        }
    }

    disconnect() {
        // Détruire la tooltip quand l'élément est déconnecté
        if (this.tooltip) {
            this.tooltip.dispose();
        }
    }

    open(event) {
        const interactiveSelector = "a, button, input, textarea, select, [role='button']";
        const clickedElement = event.target.closest(interactiveSelector);
        if (clickedElement || event.target.closest('[data-controller="row-menu"]')) {
            return;
        }
        event.preventDefault();
        window.open(this.urlValue, "_blank");
    }
}
