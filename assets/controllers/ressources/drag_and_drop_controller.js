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
            console.log("Fichiers déposés :", files);
        }
    }
}
