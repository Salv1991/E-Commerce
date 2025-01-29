import { Controller } from "@hotwired/stimulus";

export default class extends Controller {

    static targets = ['confirmPrompt'];

    connect() {
        this.form = document.getElementById('delete-account-form');
        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.confirmPrompt();
        })
    }

    confirmPrompt() {
        this.confirmPromptTarget.classList.toggle('hidden');
    }

    cancel() {
        this.confirmPromptTarget.classList.toggle('hidden');
    }

    delete() {
        this.form.submit();
    }
}