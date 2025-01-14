import { Controller } from "stimulus";

export default class extends Controller {

    static targets = [];

    connect() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.cartCount = document.getElementById('cart-count');
        this.vatPrice = document.getElementById('vat-price');
        this.checkoutButton = document.getElementById('checkout-button');
        this.shippingFee = document.getElementById('shipping-fee');
        this.paymentFee = document.getElementById('payment-fee');
        this.cartSubtotalContainer = document.querySelector('#order-summary-container .cart-subtotal');
        this.headerCartTotal = document.querySelector('header .cart-total');
        this.cartTotalContainer = document.querySelector('#order-summary-container .cart-total');
        this.emptyCartMessageContainer = document.querySelectorAll('.empty-cart-message');
        this.orderSummaryContainer = document.getElementById('order-summary-container');
    }

    fetchData(form, body) {
        return fetch(form.action, {
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body,
            method: form.method
        })
        .then(response => response.json())
    }

    toggleEmptyCartMessage(cartCount) {
        this.emptyCartMessageContainer.forEach((container) => {
            container.classList.toggle("hidden", cartCount > 0);
            container.classList.toggle("block", cartCount === 0);
        });
    }

    updateCartUi(data) {
        this.cartCount.textContent = data.cartCount;
        if(this.orderSummaryContainer) {
            this.cartTotalContainer.textContent = `${data.cartTotal}$`;
            this.cartSubtotalContainer.textContent = `${data.cartSubtotal}$`;
            this.vatPrice.textContent = `${data.vatPrice}$`;

            if(data.cartSubtotal == 0) {
                this.checkoutButton.setAttribute('disabled', true);
            } else {
                this.checkoutButton.removeAttribute('disabled');
            }

            if(data.shippingFee == 0){
                this.shippingFee.textContent = 'Free';
            } else {
                this.shippingFee.textContent = `${data.shippingFee}$`;
            }

            console.log(data);
            if(data.paymentFee == 0){
                this.paymentFee.textContent = 'Free';
            } else {
                this.paymentFee.textContent = `${data.paymentFee}$`;
            }
            
        } else {
            this.headerCartTotal.textContent = `${data.cartSubtotal}$`;    
        }
    }

    showErrorMessage(message) {
        const errorMessage = document.querySelector('#errorMessage');
        errorMessage.classList.remove('hidden');
        errorMessage.textContent = message;
        
        setTimeout(() => {
            errorMessage.classList.add('hidden');
            errorMessage.textContent = '';
        }, 2000);
    }

    add(event) {
        event.preventDefault();
        const selectedCartForm = event.currentTarget;
        const cartTeasersContainer = document.querySelector('.cart-teasers-container');

        this.fetchData(selectedCartForm, new FormData(selectedCartForm))
        .then(data => {
            if(!data.error){
                this.cartCount.textContent = data.cartCount;
                this.headerCartTotal.textContent = `${data.cartSubtotal}$`;
                
                this.toggleEmptyCartMessage(data.cartCount);
                
                if(!data.lineItemExists){
                    cartTeasersContainer.insertAdjacentHTML('beforeend', data.view);
                } else {
                    cartTeasersContainer.querySelector(`#product-${data.product_id}`).querySelector('.quantity-title').textContent = `${data.quantity} x ${data.title}`;
                    cartTeasersContainer.querySelector(`#product-${data.product_id}`).querySelector('.total').textContent = `${data.total}$`;         
                }
            } else {
                this.showErrorMessage(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
    }

    quantity(event) {
        event.preventDefault();
        const selectedCartForm = event.currentTarget;
        const quantityButton = event.submitter;
        const formData = new FormData(selectedCartForm);
        formData.append(quantityButton.name, quantityButton.value);

        this.fetchData(selectedCartForm, formData)
        .then(data => {
            if(!data.error){
                console.log(data);
                const quantityContainer = quantityButton.closest('.quantity-container');
                const buttons = quantityContainer.querySelectorAll('button[name="quantity"]');
                const quantity = data.quantity;
                quantityButton.textContent = quantity;
                quantityButton.value = quantity;
                quantityContainer.querySelector('.quantity').textContent = quantity;
                selectedCartForm.classList.add('hidden');

                this.updateCartUi(data);
                this.toggleEmptyCartMessage(data.cartCount);
                console.log(data);

                buttons.forEach(button => {
                    if(quantity == 0) { 
                        quantityButton.closest(`#product-${data.product_id}`).remove();
                    }else if(button.value == quantity) {
                        button.classList.add('bg-primary-500', 'text-white');
                        button.classList.remove('hover:bg-gray-100', 'hover:text-black');
                    } else {
                        button.classList.remove('bg-primary-500', 'text-white');
                        button.classList.add('hover:bg-gray-100', 'hover:text-black');
                    }
                });
            } else {
                this.showErrorMessage(data.error);
            }
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }

    delete(event) {
        event.preventDefault();
        const selectedCartForm = event.currentTarget;
        const cartTeasersContainer = document.querySelectorAll('.cart-teasers-container');

        this.fetchData(selectedCartForm, new FormData(selectedCartForm))
        .then(data => {
            if(!data.error){
                this.updateCartUi(data);
                this.toggleEmptyCartMessage(data.cartCount);

                cartTeasersContainer.forEach(container => {
                    const cartTeaser = container.querySelector(`[data-teaser-${data.product_id}]`);

                    if(cartTeaser) {
                        cartTeaser.remove();              
                    }
                });
            } else {
                this.showErrorMessage(data.error);
            }
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }
    
}