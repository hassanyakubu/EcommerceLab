document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    const placeOrderBtn = document.getElementById('place-order');
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const paymentProcessing = document.getElementById('payment-processing');
    const paymentSuccess = document.getElementById('payment-success');
    const paymentFailed = document.getElementById('payment-failed');

    placeOrderBtn.addEventListener('click', function() {
        if (!checkoutForm.checkValidity()) {
            checkoutForm.classList.add('was-validated');
            return;
        }

        paymentModal.show();
        paymentProcessing.classList.remove('d-none');
        paymentSuccess.classList.add('d-none');
        paymentFailed.classList.add('d-none');

        setTimeout(() => {
            processPayment();
        }, 2000);
    });

    function processPayment() {
        fetch('actions/process_checkout_action.php', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                paymentProcessing.classList.add('d-none');
                document.getElementById('order-id').textContent = data.order_id;
                document.getElementById('order-total').textContent = '₵' + parseFloat(data.total_amount).toFixed(2);
                paymentSuccess.classList.remove('d-none');

                const cartCount = document.getElementById('cart-count');
                if (cartCount) cartCount.textContent = '0';
            } else {
                throw new Error(data.message || 'Payment failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            paymentProcessing.classList.add('d-none');
            document.getElementById('error-message').textContent = error.message;
            paymentFailed.classList.remove('d-none');
        });
    }

    document.getElementById('paymentModal').addEventListener('hidden.bs.modal', function() {
        paymentProcessing.classList.remove('d-none');
        paymentSuccess.classList.add('d-none');
        paymentFailed.classList.add('d-none');
    });
});
