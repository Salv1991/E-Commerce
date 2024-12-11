import { Controller } from "stimulus";

export default class extends Controller {

    static targets = ['filterContainer', 'aside'];

    connect() {
        console.log('connected');
    }

    add(event) {
        event.preventDefault();
        console.log('added');
        console.log(event.currentTarget);
        console.log(event.target);
    }

}