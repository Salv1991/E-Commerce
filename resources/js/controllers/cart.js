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
        const cartTeasersContainer = document.querySelector('.cart-teasers-container');
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
                document.querySelector('header .cart-total').textContent = `${data.cartTotal}$`;
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
            } else {
                const errorMessage = document.querySelector('#errorMessage');
                errorMessage.classList.remove('hidden');
                errorMessage.textContent = data.error;
                
                setTimeout(() => {
                    errorMessage.classList.add('hidden');
                    errorMessage.textContent = '';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
    }

    quantity(event) {
        event.preventDefault();
        console.log("current",event.currentTarget);
        const cartTeasersContainer = document.querySelectorAll('.cart-teasers-container');
        const orderSummaryContainer = document.getElementById('order-summary-container');
        const selectedCartForm = event.currentTarget;
        const quantityButton = event.submitter;
        const formData = new FormData(selectedCartForm);
        formData.append(quantityButton.name, quantityButton.value);
     
        fetch(selectedCartForm.action, {
            headers:{
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            method: selectedCartForm.method
        })
        .then(response => response.json())
        .then(data => {
            if(!data.error){
                console.log(data);
                const quantityContainer = quantityButton.closest('.quantity-container');
                const buttons = quantityContainer.querySelectorAll('button[name="quantity"]');
                const quantity = data.quantity;
                const cartTotal = data.cartTotal;
                document.querySelector('header #cart-count').textContent = data.cartCount;
                quantityButton.textContent = quantity;
                quantityButton.value = quantity;
                quantityContainer.querySelector('.quantity').textContent = quantity;

                buttons.forEach(button => {
                    if( quantity == 0){ 
                        quantityButton.closest(`#product-${data.product_id}`).remove();   
                    }else if (button.value == quantity) {
                        button.classList.add('bg-primary-500', 'text-white');
                        button.classList.remove('hover:bg-gray-100', 'hover:text-black');
                    } else {
                        button.classList.remove('bg-primary-500', 'text-white');
                        button.classList.add('hover:bg-gray-100', 'hover:text-black');
                    }
                });

                if(orderSummaryContainer){
                    document.querySelector('#order-summary-container .cart-total').textContent = cartTotal;
                    document.querySelector('#order-summary-container .cart-subtotal').textContent = cartTotal;
                } else {
                    document.querySelector('header #cart-count').textContent = data.cartCount;
                    document.querySelector('header .cart-total').textContent = `${cartTotal}$`;    
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
        const cartTeasersContainer = document.querySelectorAll('.cart-teasers-container');
        const orderSummaryContainer = document.getElementById('order-summary-container');
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
                 document.querySelector('header #cart-count').textContent = data.cartCount;
                if(emptyCartMessageContainer){
                    emptyCartMessageContainer.classList.toggle('hidden', data.cartCount > 0);
                    emptyCartMessageContainer.classList.toggle('block', data.cartCount <= 0);
                }
 
                if(orderSummaryContainer){
                    document.querySelector('#order-summary-container .cart-total').textContent = data.cartTotal;
                    document.querySelector('#order-summary-container .cart-subtotal').textContent = data.cartTotal;
                } else {
                    document.querySelector('header .cart-total').textContent = `${data.cartTotal}$`;    
                }
                
                cartTeasersContainer.forEach(container => {
                    const cartTeaser = container.querySelector(`[data-teaser-${data.product_id}]`);
                    if(cartTeaser) cartTeaser.remove();              
                });
            } 
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }
}