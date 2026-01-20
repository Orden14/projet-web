import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["menu", "button"];

    toggle(event) {
        event.preventDefault();
        this.menuTarget.classList.toggle("active");
    }

    selectOption(event) {
        event.preventDefault();
        const selectedText = event.currentTarget.textContent.trim();

        // Mettre à jour le texte du bouton
        const buttonText = this.element.querySelector(".filter-pill");
        buttonText.innerHTML = `${selectedText} <span class="caret">▾</span>`;

        // Fermer le menu
        this.menuTarget.classList.remove("active");

        // Déclencher l'événement de filtre
        this.dispatch("filter-selected", { detail: { value: selectedText } });
    }

    connect() {
        this.handleClickOutside = (event) => {
            if (!this.element.contains(event.target)) {
                this.menuTarget.classList.remove("active");
            }
        };
        document.addEventListener("click", this.handleClickOutside);
    }

    disconnect() {
        document.removeEventListener("click", this.handleClickOutside);
    }
}
