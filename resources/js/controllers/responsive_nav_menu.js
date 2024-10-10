import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['menu', 'close']

    toggle() {
        this.menuTarget.classList.toggle('translate-x-0');
        this.menuTarget.classList.toggle('translate-x-full');
    }
}