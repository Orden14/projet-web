import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["source", "row"];

    connect() {
        this.handleFavoriteToggled = () => this.applyAllFilters();
        window.addEventListener("favorite:toggled", this.handleFavoriteToggled);
    }

    disconnect() {
        window.removeEventListener("favorite:toggled", this.handleFavoriteToggled);
    }

    filters = {
        search: "",
        type: null,
        category: null,
        date: null,
        favorite: false,
    };

    filter(event) {
        this.filters.search = event.target.value.toLowerCase();
        this.applyAllFilters();
    }

    filterByType(event) {
        event.preventDefault();
        const selectedType = event.currentTarget.dataset.type;
        this.filters.type = selectedType || null;
        this.setClearButtonVisibility("type", !!this.filters.type);
        this.applyAllFilters();
    }

    filterByCategory(event) {
        event.preventDefault();
        const selectedText = event.currentTarget.textContent.trim();
        this.filters.category = selectedText === "Catégorie" ? null : selectedText;
        this.setClearButtonVisibility("category", !!this.filters.category);
        this.applyAllFilters();
    }

    filterByDate(event) {
        event.preventDefault();
        const selectedText = event.currentTarget.textContent.trim();
        this.filters.date = selectedText === "Date de modification" ? null : selectedText;
        this.setClearButtonVisibility("date", !!this.filters.date);
        this.applyAllFilters();
    }

    clearTypeFilter(event) {
        event.preventDefault();
        this.filters.type = null;
        this.resetFilterButtonLabel("type", "Type");
        this.setClearButtonVisibility("type", false);
        this.applyAllFilters();
    }

    clearCategoryFilter(event) {
        event.preventDefault();
        this.filters.category = null;
        this.resetFilterButtonLabel("category", "Catégorie");
        this.setClearButtonVisibility("category", false);
        this.applyAllFilters();
    }

    clearDateFilter(event) {
        event.preventDefault();
        this.filters.date = null;
        this.resetFilterButtonLabel("date", "Date de modification");
        this.setClearButtonVisibility("date", false);
        this.applyAllFilters();
    }

    filterByFavorite(event) {
        event.preventDefault();
        this.filters.favorite = !this.filters.favorite;
        this.setFavoritesButtonStyle();
        this.setClearButtonVisibility("favorite", this.filters.favorite);
        this.applyAllFilters();
    }

    clearFavoriteFilter(event) {
        event.preventDefault();
        this.filters.favorite = false;
        this.setFavoritesButtonStyle();
        this.setClearButtonVisibility("favorite", false);
        this.applyAllFilters();
    }

    setFavoritesButtonStyle() {
        const button = this.element.querySelector(`.filter-pill[data-filter-group="favorite"]`);
        if (button) {
            button.style.backgroundColor = this.filters.favorite ? "#cae3fb" : "";
        }
    }

    resetFilterButtonLabel(group, defaultLabel) {
        const button = this.element.querySelector(`.filter-pill[data-filter-group="${group}"]`);
        if (button) {
            button.innerHTML = `${defaultLabel} <span class="caret">▾</span>`;
        }
    }

    setClearButtonVisibility(group, isVisible) {
        const clearButton = this.element.querySelector(`.filter-clear[data-filter-group="${group}"]`);
        if (clearButton) {
            clearButton.hidden = !isVisible;
        }
    }

    applyAllFilters() {
        this.rowTargets.forEach((row) => {
            const name = row.dataset.name.toLowerCase();
            const type = row.dataset.type;
            const category = row.dataset.category;
            const date = row.dataset.date;
            const favorite = row.dataset.favorite === "true";

            const matchSearch = name.includes(this.filters.search);
            const matchType = !this.filters.type || type === this.filters.type;
            const matchCategory = !this.filters.category || category === this.filters.category;
            const matchDate = !this.filters.date || this.matchDate(date, this.filters.date);
            const matchFavorite = !this.filters.favorite || favorite;

            if (matchSearch && matchType && matchCategory && matchDate && matchFavorite) {
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
