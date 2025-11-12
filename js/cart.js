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

    // Update cart item quantity
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
                // Update the UI
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) {
                    const price = parseFloat(row.querySelector('.price').textContent.replace('₵', ''));
                    const subtotal = price * quantity;
                    row.querySelector('.subtotal').textContent = `₵${subtotal.toFixed(2)}`;
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

    // Remove item from cart
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
                // Remove the row from the table
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Update cart total
                document.getElementById('cart-total').textContent = `₵${parseFloat(data.cart_total).toFixed(2)}`;
                
                // Update cart count in header if it exists
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                
                // If cart is empty, reload the page to show empty cart message
                if (data.cart_count === 0) {
                    location.reload();
                }
            } else {
                alert('Failed to remove item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the item.');
        });
    }

    // Empty cart
    function emptyCart() {
        fetch('actions/empty_cart_action.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update cart count in header if it exists
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = '0';
                }
                // Reload to show empty cart message
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