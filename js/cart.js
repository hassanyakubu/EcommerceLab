document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    document.querySelectorAll('.update-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.quantity-input');
            const productId = input.dataset.productId;
            let newQty = parseInt(input.value);

            if (this.dataset.action === 'increase') {
                newQty++;
            } else if (this.dataset.action === 'decrease' && newQty > 1) {
                newQty--;
            }

            if (newQty !== parseInt(input.value)) {
                updateCartItem(productId, newQty);
            }
        });
    });

    // Handle direct input
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const newQty = Math.max(1, parseInt(this.value) || 1);

            if (newQty !== parseInt(this.value)) {
                this.value = newQty;
            }

            updateCartItem(productId, newQty);
        });
    });

    // Remove item
    let productToRemove = null;
    const removeModal = new bootstrap.Modal(document.getElementById('removeItemModal'));

    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function() {
            productToRemove = this.dataset.productId;
            removeModal.show();
        });
    });

    document.getElementById('confirm-remove').addEventListener('click', function() {
        if (productToRemove) {
            removeFromCart(productToRemove);
            removeModal.hide();
        }
    });

    // Empty cart
    document.getElementById('empty-cart')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to empty your cart?')) {
            emptyCart();
        }
    });

    // Functions
    function updateCartItem(productId, quantity) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        fetch('actions/update_quantity_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) {
                    const price = parseFloat(row.querySelector('.price').textContent.replace('₵', ''));
                    row.querySelector('.subtotal').textContent = `₵${(price * quantity).toFixed(2)}`;
                    document.getElementById('cart-total').textContent = `₵${parseFloat(data.cart_total).toFixed(2)}`;
                }
            } else {
                alert('Failed to update cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the cart.');
        });
    }

    function removeFromCart(productId) {
        const formData = new FormData();
        formData.append('product_id', productId);

        fetch('actions/remove_from_cart_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) row.remove();

                document.getElementById('cart-total').textContent = `₵${parseFloat(data.cart_total).toFixed(2)}`;
                const cartCount = document.getElementById('cart-count');
                if (cartCount) cartCount.textContent = data.cart_count;

                if (data.cart_count === 0) location.reload();
            } else {
                alert('Failed to remove item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the item.');
        });
    }

    function emptyCart() {
        fetch('actions/empty_cart_action.php', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) cartCount.textContent = '0';
                location.reload();
            } else {
                alert('Failed to empty cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while emptying the cart.');
        });
    }
});
