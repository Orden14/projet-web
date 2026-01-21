import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["row"];

    filter(event) {
        const query = event.target.value.trim().toLowerCase();

        this.rowTargets.forEach((row) => {
            const name = row.dataset.name?.toLowerCase() ?? "";
            const phone = row.dataset.phone?.toLowerCase() ?? "";
            const email = row.dataset.email?.toLowerCase() ?? "";

            const matches = !query || [name, phone, email].some((text) => text.includes(query));
            row.style.display = matches ? "" : "none";
        });
    }
}
