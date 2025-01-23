import { Controller } from "stimulus";

export default class extends Controller {

    static targets = ['confirmPrompt'];

    connect() {
        console.log('de');
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