import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["menu"];

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

    toggle(event) {
        event.preventDefault();
        event.stopPropagation();

        this.menuTarget.classList.toggle("active");

        if (this.menuTarget.classList.contains("active")) {
            const rect = this.menuTarget.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

            if (rect.bottom > viewportHeight - 16) {
                this.menuTarget.classList.add("row-menu--top");
            } else {
                this.menuTarget.classList.remove("row-menu--top");
            }
        }
    }
}
