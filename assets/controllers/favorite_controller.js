import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    async toggleFavorite(event) {
        event.preventDefault();
        const form = this.element.querySelector("form");

        if (!form) {
            console.error("Form not found");
            return;
        }

        const url = form.action;
        const token = form.querySelector('input[name="_token"]').value;
        const button = event.currentTarget;
        const icon = button.querySelector("i");

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: `_token=${token}`,
            });

            const data = await response.json();
            const row = this.element.closest("tr");

            if (data.favorite) {
                icon.classList.remove("bi-star");
                icon.classList.add("bi-star-fill");
                icon.style.color = "gold";
                if (row) row.dataset.favorite = "true";
            } else {
                icon.classList.remove("bi-star-fill");
                icon.classList.add("bi-star");
                icon.style.color = "grey";
                if (row) row.dataset.favorite = "false";
            }

            // Notify filters to re-apply if needed
            window.dispatchEvent(new CustomEvent("favorite:toggled"));
        } catch (error) {
            console.error("Error toggling favorite:", error);
        }
    }
}
