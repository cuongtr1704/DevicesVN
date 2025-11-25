// Dashboard Products Page JavaScript

let currentProductId = null;
let categoriesList = [];

// Load categories on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
});

/**
 * Load all categories
 */
function loadCategories() {
    fetch(`${window.location.origin}/devicesvn/dashboard/categories/list`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                categoriesList = data.categories;
            }
        })
        .catch(error => console.error('Error loading categories:', error));
}

/**
 * Add new product
 */
function addProduct() {
    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
    const modalBody = document.getElementById('addProductContent');
    
    // Build category options
    let categoryOptions = '<option value="">Select Category</option>';
    categoriesList.forEach(cat => {
        categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
    });
    
    modalBody.innerHTML = `
        <form id="addProductForm">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-box me-2 text-primary"></i>Product Name *
                    </label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-barcode me-2 text-primary"></i>SKU *
                    </label>
                    <input type="text" class="form-control" name="sku" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-layer-group me-2 text-primary"></i>Category
                    </label>
                    <select class="form-control" name="category_id">
                        ${categoryOptions}
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-boxes me-2 text-primary"></i>Stock Quantity *
                    </label>
                    <input type="number" class="form-control" name="stock_quantity" value="0" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-dollar-sign me-2 text-success"></i>Regular Price (VND) *
                    </label>
                    <input type="number" step="1000" class="form-control" name="price" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-tag me-2 text-danger"></i>Sale Price (VND)
                    </label>
                    <input type="number" step="1000" class="form-control" name="sale_price">
                    <small class="text-muted">Leave empty if no sale</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-align-left me-2 text-primary"></i>Description
                </label>
                <textarea class="form-control" name="description" rows="3" placeholder="Enter product description..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-list-ul me-2 text-primary"></i>Specifications
                </label>
                <textarea class="form-control" name="specifications" rows="3" placeholder="Enter specifications..."></textarea>
            </div>
        </form>
    `;
    
    modal.show();
}

/**
 * Save new product
 */
function saveNewProduct() {
    const form = document.getElementById('addProductForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    fetch(`${window.productsUrl}/add`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to add product', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product. Please try again.', 'danger');
    });
}

/**
 * View product details
 */
function viewProduct(productId) {
    const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
    const content = document.getElementById('viewProductContent');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch product details
    fetch(`${window.productsUrl}/view/${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                const imageUrl = product.main_image 
                    ? `${window.location.origin}/devicesvn/public/${product.main_image}` 
                    : `${window.location.origin}/devicesvn/public/images/no-image.png`;
                
                content.innerHTML = `
                    <div class="row g-4">
                        <div class="col-md-5">
                            <div class="product-image-container">
                                <img src="${imageUrl}" 
                                     class="img-fluid rounded shadow-sm" 
                                     alt="${product.name}"
                                     style="width: 100%; height: 300px; object-fit: cover;"
                                     onerror="this.src='${window.location.origin}/devicesvn/public/images/no-image.png'">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4 class="mb-3 fw-bold text-primary">${product.name}</h4>
                            
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                ${product.category_name 
                                    ? `<span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-layer-group me-1"></i>${product.category_name}
                                       </span>`
                                    : `<span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-layer-group me-1"></i>No Category
                                       </span>`
                                }
                                ${product.stock_quantity > 10 
                                    ? `<span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>In Stock (${product.stock_quantity})
                                       </span>`
                                    : product.stock_quantity > 0 
                                        ? `<span class="badge bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Low Stock (${product.stock_quantity})
                                           </span>`
                                        : `<span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                                           </span>`
                                }
                            </div>
                            
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <small class="text-muted d-block">SKU</small>
                                            <strong class="text-dark">${product.sku}</strong>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <small class="text-muted d-block">Price</small>
                                            ${product.sale_price && parseFloat(product.sale_price) < parseFloat(product.price)
                                                ? `<div>
                                                    <strong class="text-danger fs-5">${parseInt(product.sale_price).toLocaleString('vi-VN')} VND</strong><br>
                                                    <small class="text-muted text-decoration-line-through">${parseInt(product.price).toLocaleString('vi-VN')} VND</small>
                                                   </div>`
                                                : `<strong class="text-success fs-5">${parseInt(product.price).toLocaleString('vi-VN')} VND</strong>`
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${product.description ? `
                                <div class="mb-3">
                                    <h6 class="fw-bold text-secondary mb-2">
                                        <i class="fas fa-align-left me-2"></i>Description
                                    </h6>
                                    <p class="text-muted">${product.description}</p>
                                </div>
                            ` : ''}
                            
                            ${product.specifications ? `
                                <div class="mb-3">
                                    <h6 class="fw-bold text-secondary mb-2">
                                        <i class="fas fa-list-ul me-2"></i>Specifications
                                    </h6>
                                    <pre class="bg-light p-3 rounded border" style="white-space: pre-wrap; font-size: 0.9rem;">${product.specifications}</pre>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Failed to load product details'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error loading product details. Please try again.
                </div>
            `;
        });
}

/**
 * Edit product
 */
function editProduct(productId) {
    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
    const content = document.getElementById('editProductContent');
    currentProductId = productId;
    
    // Show loading
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch product details for editing
    fetch(`${window.productsUrl}/view/${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                
                // Build category options
                let categoryOptions = '<option value="">Select Category</option>';
                categoriesList.forEach(cat => {
                    const selected = cat.id == product.category_id ? 'selected' : '';
                    categoryOptions += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
                });
                
                content.innerHTML = `
                    <form id="editProductForm">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-box me-2 text-primary"></i>Product Name *
                                </label>
                                <input type="text" class="form-control" name="name" value="${product.name}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-barcode me-2 text-primary"></i>SKU *
                                </label>
                                <input type="text" class="form-control" name="sku" value="${product.sku}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-layer-group me-2 text-primary"></i>Category
                                </label>
                                <select class="form-control" name="category_id">
                                    ${categoryOptions}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-boxes me-2 text-primary"></i>Stock Quantity *
                                </label>
                                <input type="number" class="form-control" name="stock_quantity" value="${product.stock_quantity}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign me-2 text-success"></i>Regular Price (VND) *
                                </label>
                                <input type="number" step="1000" class="form-control" name="price" value="${product.price}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag me-2 text-danger"></i>Sale Price (VND)
                                </label>
                                <input type="number" step="1000" class="form-control" name="sale_price" value="${product.sale_price || ''}">
                                <small class="text-muted">Leave empty if no sale</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Description
                            </label>
                            <textarea class="form-control" name="description" rows="3">${product.description || ''}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-list-ul me-2 text-primary"></i>Specifications
                            </label>
                            <textarea class="form-control" name="specifications" rows="3">${product.specifications || ''}</textarea>
                        </div>
                    </form>
                `;
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Failed to load product details'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error loading product details. Please try again.
                </div>
            `;
        });
}

/**
 * Save product changes
 */
function saveProductChanges() {
    const form = document.getElementById('editProductForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    fetch(`${window.productsUrl}/update/${currentProductId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product updated successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to update product', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating product. Please try again.', 'danger');
    });
}

/**
 * Delete product
 */
function deleteProduct(productId, productName) {
    currentProductId = productId;
    document.getElementById('deleteProductMessage').textContent = 
        `Are you sure you want to delete "${productName}"? This action cannot be undone.`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
    modal.show();
    
    // Set up delete confirmation
    document.getElementById('confirmDeleteBtn').onclick = function() {
        fetch(`${window.productsUrl}/delete/${productId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Product deleted successfully!', 'success');
                modal.hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Failed to delete product', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting product. Please try again.', 'danger');
        });
    };
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

/**
 * Create toast container if it doesn't exist
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}
