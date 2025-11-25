// Dashboard All Orders Page JavaScript

function updateOrderStatus(orderId, newStatus) {
    if (!confirm('Are you sure you want to update the order status?')) {
        location.reload();
        return;
    }
    
    // AJAX request to update status
    fetch(window.updateOrderStatusUrl || '<?= url("api/orders/update-status") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the select background color
            const select = event.target;
            const colors = {
                'pending': 'warning',
                'confirmed': 'info',
                'shipped': 'primary',
                'delivered': 'success',
                'cancelled': 'danger'
            };
            
            // Remove all badge classes
            select.className = 'badge badge-select';
            
            // Add the appropriate badge class
            select.classList.add(`bg-${colors[newStatus]}`);
            
            showNotification('Order status updated successfully', 'success');
        } else {
            showNotification('Failed to update order status', 'error');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
        location.reload();
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type === 'success' ? 'success' : 'error'}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'times-circle'} me-2"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add notification styles if not already present
if (!document.getElementById('all-orders-notification-styles')) {
    const style = document.createElement('style');
    style.id = 'all-orders-notification-styles';
    style.textContent = `
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-weight: 500;
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            min-width: 300px;
        }
        
        .custom-notification.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .custom-notification.success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .custom-notification.error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    `;
    document.head.appendChild(style);
}
