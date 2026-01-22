import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        this.dragCounter = 0;
    }

    onDragEnter(event) {
        event.preventDefault();
        event.stopPropagation();

        this.dragCounter++;
        this.element.classList.add("ressources-dropzone-active");
    }

    onDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = "copy";
    }

    onDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();

        this.dragCounter--;
        if (this.dragCounter <= 0) {
            this.dragCounter = 0;
            this.element.classList.remove("ressources-dropzone-active");
        }
    }

    onDrop(event) {
        event.preventDefault();
        event.stopPropagation();

        this.dragCounter = 0;
        this.element.classList.remove("ressources-dropzone-active");

        const files = Array.from(event.dataTransfer.files || []);
        if (files.length > 0) {
            this.openModalWithFiles(files);
        }
    }

    openModalWithFiles(files) {
        const modalElement = document.getElementById("createFileModal");
        if (!modalElement) return;
        const modal = new window.bootstrap.Modal(modalElement);
        const handleShown = () => {
            const fileInput = modalElement.querySelector('input[type="file"]');
            if (fileInput) {
                const dataTransfer = new DataTransfer();
                files.forEach((file) => dataTransfer.items.add(file));
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event("change", { bubbles: true }));
            }
            modalElement.removeEventListener("shown.bs.modal", handleShown);
        };

        modalElement.addEventListener("shown.bs.modal", handleShown);
        modal.show();
    }
}
