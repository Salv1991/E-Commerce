import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['sidebar', 'content', 'sidebarSmall'];

    connect() {
    
    }

    toggle() {
        this.sidebarTarget.classList.toggle('-translate-x-full');
        this.sidebarSmallTarget.classList.toggle('-translate-x-full');
        this.contentTarget.classList.toggle('ml-64');    
        this.contentTarget.classList.toggle('ml-16');    

    }
}
