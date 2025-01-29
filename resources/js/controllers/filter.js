import { Controller } from "@hotwired/stimulus";

export default class extends Controller {

    static targets = ['filterContainer', 'aside'];

    connect() {
        this.asideTarget.addEventListener('click', event => event.stopPropagation());
    }

    toggleFilters() {
        this.asideTarget.classList.toggle('-translate-x-full');
        this.filterContainerTarget.classList.toggle('opacity-100');
        this.filterContainerTarget.classList.toggle('opacity-0');
        this.filterContainerTarget.classList.toggle('pointer-events-none');
    }
}