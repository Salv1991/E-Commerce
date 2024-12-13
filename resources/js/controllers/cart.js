import { Controller } from "stimulus";

export default class extends Controller {

    static targets = [];

    connect() {
        console.log('connected');
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    add(event) {
        event.preventDefault();
        console.log('added');
        console.log(event.target);
        const selectedCartForm = event.currentTarget;
        const cartTeasersContainer = document.querySelector('#cart-teasers-container');
        const emptyCartMessageContainer = document.querySelector('.empty-cart-message');
        console.log(selectedCartForm.action);

        fetch(selectedCartForm.action, {
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(selectedCartForm),
            method: selectedCartForm.method
        })
        .then(response => response.json())
        .then(data => {
            if(!data.error){
                document.querySelector('header #cart-count').textContent = data.cartCount;
                document.querySelector('header #cart-total').textContent = `${data.cartTotal}$`;
                if(emptyCartMessageContainer){
                    emptyCartMessageContainer.classList.toggle('hidden', data.cartCount > 0);
                    emptyCartMessageContainer.classList.toggle('block', data.cartCount === 0);
                }
                if(!data.lineItemExists){
                    cartTeasersContainer.insertAdjacentHTML('beforeend', data.view);
                } else {
                    cartTeasersContainer.querySelector(`#product-${data.product_id}`).querySelector('.quantity-title').textContent = `${data.quantity} x ${data.title}`;
                    cartTeasersContainer.querySelector(`#product-${data.product_id}`).querySelector('.total').textContent = `${data.total}$`;         
                }
            } 
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }

    delete(event) {
        event.preventDefault();
        console.log('delete');
        const selectedCartForm = event.currentTarget;
        const emptyCartMessageContainer = document.querySelector('.empty-cart-message');
        const cartTeasersContainer = document.querySelector('#cart-teasers-container');
        console.log(event.currentTarget);

        fetch(selectedCartForm.action, {
            headers:{
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(selectedCartForm),
            method: selectedCartForm.method
        })
        .then(response => response.json())
        .then(data => {
            if(!data.error){
                if(emptyCartMessageContainer){
                    emptyCartMessageContainer.classList.toggle('hidden', data.cartCount > 0);
                    emptyCartMessageContainer.classList.toggle('block', data.cartCount === 0);
                }
                document.querySelector('header #cart-count').textContent = data.cartCount;
                document.querySelector('header #cart-total').textContent = `${data.cartTotal}$`;

                cartTeasersContainer.querySelector(`#product-${data.product_id}`).remove();
                
            } 
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }
}