/**
 * Cart Management Script
 * Handles Add-to-Cart with AJAX and cart badge updates
 */

(function() {
    'use strict';

    // Update cart badge in header
    function updateCartBadge(count) {
        const cartLink = document.querySelector('a[href*="/cart"]');
        if (!cartLink) return;

        let badge = cartLink.querySelector('.cart-badge');
        
        if (!badge && count > 0) {
            badge = document.createElement('span');
            badge.className = 'cart-badge';
            cartLink.appendChild(badge);
        }

        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Load initial cart count
    function loadInitialCartCount() {
        fetch('/cart/count')
            .then(response => response.json())
            .then(data => {
                if (data.count !== undefined) {
                    updateCartBadge(data.count);
                }
            })
            .catch(error => console.error('Error loading cart count:', error));
    }

    // Handle Add-to-Cart form submission
    function handleAddToCart(event) {
        event.preventDefault();

        const form = event.target;
        const btn = form.querySelector('button[type="submit"]');
        const productId = form.dataset.productId;

        if (!productId) {
            console.error('Product ID not found');
            return;
        }

        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Adding...';
        btn.style.opacity = '0.7';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.cartCount !== undefined) {
                    updateCartBadge(data.cartCount);
                }
                btn.textContent = '✓ Added!';
                btn.style.background = '#27ae60';
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }, 1500);
            } else {
                btn.textContent = 'Error';
                btn.style.background = '#e74c3c';
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.textContent = 'Error';
            btn.style.background = '#e74c3c';
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
                btn.style.opacity = '1';
                btn.disabled = false;
            }, 2000);
        });
    }

    // Initialize
    function init() {
        loadInitialCartCount();
        
        document.querySelectorAll('form[data-action="add-to-cart"]').forEach(form => {
            form.addEventListener('submit', handleAddToCart);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

