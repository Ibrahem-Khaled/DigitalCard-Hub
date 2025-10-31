import './bootstrap';

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    console.log('Digital Cards Store - Loaded Successfully');

    // Update cart count on page load
    updateCartCount();
});

// Update cart count from server
async function updateCartCount() {
    try {
        const response = await fetch('/api/cart/count');
        if (response.ok) {
            const data = await response.json();
            const countElement = document.getElementById('cart-count');
            if (countElement && data.count !== undefined) {
                countElement.textContent = data.count;
            }
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

// Export for use in other scripts
window.updateCartCount = updateCartCount;
