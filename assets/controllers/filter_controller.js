import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["source", "row"];

    filters = {
        search: "",
        type: null,
        category: null,
        date: null,
    };

    filter(event) {
        this.filters.search = event.target.value.toLowerCase();
        this.applyAllFilters();
    }

    filterByType(event) {
        event.preventDefault();
        const selectedText = event.currentTarget.textContent.trim();
        this.filters.type = selectedText === "Type" ? null : selectedText;
        this.applyAllFilters();
    }

    filterByCategory(event) {
        event.preventDefault();
        const selectedText = event.currentTarget.textContent.trim();
        this.filters.category = selectedText === "Catégorie" ? null : selectedText;
        this.applyAllFilters();
    }

    filterByDate(event) {
        event.preventDefault();
        const selectedText = event.currentTarget.textContent.trim();
        this.filters.date = selectedText === "Date de modification" ? null : selectedText;
        this.applyAllFilters();
    }

    applyAllFilters() {
        this.rowTargets.forEach((row) => {
            const nameCell = row.cells[0].textContent.toLowerCase();
            const typeCell = row.cells[1].textContent.trim();
            const categoryCell = row.cells[2].textContent.trim();
            const dateCell = row.cells[3].textContent.trim();

            const matchSearch = nameCell.includes(this.filters.search);
            const matchType = !this.filters.type || typeCell.includes(this.filters.type);
            const matchCategory = !this.filters.category || categoryCell.includes(this.filters.category);
            const matchDate = !this.filters.date || this.matchDate(dateCell, this.filters.date);

            if (matchSearch && matchType && matchCategory && matchDate) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    matchDate(dateStr, filterType) {
        const today = new Date();
        const rowDate = new Date(dateStr);

        switch (filterType) {
            case "Aujourd'hui":
                return rowDate.toDateString() === today.toDateString();
            case "7 derniers jours":
                const sevenDaysAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                return rowDate >= sevenDaysAgo;
            case "30 derniers jours":
                const thirtyDaysAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                return rowDate >= thirtyDaysAgo;
            case "Cette année":
                return rowDate.getFullYear() === today.getFullYear();
            case "Année dernière":
                return rowDate.getFullYear() === today.getFullYear() - 1;
            default:
                return true;
        }
    }
}
