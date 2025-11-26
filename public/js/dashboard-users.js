// Dashboard Users Page JavaScript

const usersUrl = `${window.location.origin}/devicesvn/dashboard/users`;

/**
 * View user details
 */
function viewUserDetails(userId) {
    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    const content = document.getElementById('userDetailsContent');
    
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    fetch(`${usersUrl}/view/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                content.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="avatar-circle mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px;">
                                ${user.full_name.charAt(0).toUpperCase()}
                            </div>
                            <h5>${user.full_name}</h5>
                            ${user.role === 'admin' 
                                ? '<span class="badge bg-danger fs-6">Admin</span>' 
                                : '<span class="badge bg-secondary fs-6">Customer</span>'}
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Personal Information
                            </h6>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 150px;"><i class="fas fa-envelope me-2"></i>Email</td>
                                        <td><strong>${user.email}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-phone me-2"></i>Phone</td>
                                        <td><strong>${user.phone || 'Not provided'}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address</td>
                                        <td><strong>${user.address || 'Not provided'}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Registered</td>
                                        <td><strong>${new Date(user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Failed to load user details'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error loading user details. Please try again.
                </div>
            `;
        });
}

/**
 * View user orders - redirect to dedicated page
 */
function viewUserOrders(userId) {
    window.location.href = `${usersUrl}/user-orders/${userId}`;
}

/**
 * Edit user role
 */
function editUserRole(userId, userName, currentRole) {
    const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
    
    document.getElementById('editUserId').value = userId;
    document.getElementById('editUserRole').value = currentRole;
    
    modal.show();
}

/**
 * Save user role
 */
function saveUserRole() {
    const userId = document.getElementById('editUserId').value;
    const role = document.getElementById('editUserRole').value;
    
    fetch(`${usersUrl}/update-role/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ role: role })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'User role updated successfully');
            bootstrap.Modal.getInstance(document.getElementById('editRoleModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Failed to update user role');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred. Please try again.');
    });
}

/**
 * Show delete user modal
 */
function deleteUser(userId, userName) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = userName;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    modal.show();
}

/**
 * Confirm delete user
 */
function confirmDeleteUser() {
    const userId = document.getElementById('deleteUserId').value;
    
    fetch(`${usersUrl}/delete/${userId}`, {
        method: 'POST',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'User deleted successfully');
            bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Failed to delete user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred. Please try again.');
    });
}

/**
 * Get order status badge
 */
function getOrderStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning text-dark">Pending</span>',
        'processing': '<span class="badge bg-info">Processing</span>',
        'shipped': '<span class="badge bg-primary">Shipped</span>',
        'delivered': '<span class="badge bg-success">Delivered</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

/**
 * Show notification (matching categories section styling)
 */
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'error'}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'} me-2"></i>
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
