import { Controller } from "@hotwired/stimulus";

export default class extends Controller {

    static targets = [];

    connect() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.subtotal = document.getElementById('subtotal');
        this.paymentFee = document.getElementById('payment-fee');
        this.shippingFee = document.getElementById('shipping-fee');
        this.total = document.getElementById('total');
        console.log('Connected order')
    }

    updateShippingMethod(event){
        const selectedShippingMethod = event.currentTarget.value;
        console.log(selectedShippingMethod);
        fetch('/checkout/shipping-method', {
            method: 'POST',
            body: JSON.stringify({shipping_method: selectedShippingMethod}),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(!data.error){
                this.subtotal.textContent = data.cartSubtotal;
                if( data.shippingFee == 0) {
                    this.shippingFee.textContent = 'Free';
                } else {
                    this.shippingFee.textContent = data.shippingFee + ' $';
                }
                this.subtotal.textContent = data.cartSubtotal;
                this.total.textContent = data.cartTotal;
            }
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }

    updatePaymentMethod(event){
        const selectedPaymentMethod = event.currentTarget.value;
        console.log(selectedPaymentMethod);
        fetch('/checkout/payment-method', {
            method: 'POST',
            body: JSON.stringify({payment_method: selectedPaymentMethod}),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(!data.error){
                this.subtotal.textContent = data.cartSubtotal;
                this.paymentFee.textContent = data.paymentFee + ' $';
                this.subtotal.textContent = data.cartSubtotal;
                this.total.textContent = data.cartTotal;
            }
        })
        .catch(error => {
            console.log('Error:', error);
        })
    }
}