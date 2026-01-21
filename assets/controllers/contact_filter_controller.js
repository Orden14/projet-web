import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["source", "row"];

    filter(event) {
        const searchValue = event.target.value.toLowerCase();

        this.rowTargets.forEach((row) => {
            const nameCell = row.cells[0].textContent.toLowerCase();
            const phoneCell = row.cells[1].textContent.toLowerCase();
            const emailCell = row.cells[2].textContent.toLowerCase();

            const matches = nameCell.includes(searchValue) || phoneCell.includes(searchValue) || emailCell.includes(searchValue);

            if (matches) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
}
