// Dashboard Categories Page JavaScript

function previewIcon(iconClass, previewId) {
    const preview = document.getElementById(previewId);
    if (iconClass.trim()) {
        // Remove all existing classes and add new ones
        preview.className = iconClass.trim();
    } else {
        // Default icon - use a simple, always-available icon
        preview.className = 'fas fa-folder';
    }
}

function showToast(message, type = 'success') {
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

function saveCategory() {
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'add');
    
    fetch(window.categoriesUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (e) {
            console.error('JSON Parse Error:', e);
            console.error('Response text:', text);
            showToast('Invalid server response. Check console for details.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function editCategory(id) {
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'get');
    formData.append('id', id);
    
    fetch(window.categoriesUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const category = data.category;
            document.getElementById('editCategoryId').value = category.id;
            document.getElementById('editCategoryName').value = category.name;
            document.getElementById('editCategoryIcon').value = category.icon || '';
            document.getElementById('editCategoryDescription').value = category.description || '';
            document.getElementById('editCategorySortOrder').value = category.sort_order;
            
            // Preview icon
            previewIcon(category.icon || '', 'editIconPreview');
            
            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function updateCategory() {
    const form = document.getElementById('editCategoryForm');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'edit');
    
    fetch(window.categoriesUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

let deleteItemId = null;

function deleteCategory(id, name, productCount) {
    if (productCount > 0) {
        showToast(`Cannot delete "${name}" because it has ${productCount} product(s). Please remove or reassign products first.`, 'warning');
        return;
    }
    
    deleteItemId = id;
    document.getElementById('deleteConfirmMessage').textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (!deleteItemId) return;
            
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'delete');
            formData.append('id', deleteItemId);
            
            fetch(window.categoriesUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            });
        });
    }
});
