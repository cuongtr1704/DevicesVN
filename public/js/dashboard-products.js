// Dashboard Products Page JavaScript

let currentProductId = null;
let categoriesList = [];
let uploadedMainImage = null;
let uploadedAltImages = {}; // Store by input ID
let imagesToDelete = []; // Track images marked for deletion
let currentProductImages = []; // Store current product's images to prevent reload

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
 * Add new specification row
 */
function addSpecificationRow(container, key = '', value = '') {
    const row = document.createElement('div');
    row.className = 'spec-row mb-2';
    row.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-5">
                <input type="text" class="form-control" name="spec_keys[]" placeholder="e.g. Processor" value="${key}">
            </div>
            <div class="col-6">
                <input type="text" class="form-control" name="spec_values[]" placeholder="e.g. Intel Core i7" value="${value}">
            </div>
            <div class="col-1 text-center">
                <button type="button" class="btn btn-danger btn-sm" 
                        onclick="this.closest('.spec-row').remove()" 
                        style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
}

/**
 * Preview image before upload
 */
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Load images in edit modal with upload functionality
 */
function loadEditImages(images) {
    const mainContainer = document.getElementById('mainImageDisplay');
    const altContainer = document.getElementById('altImagesDisplay');
    
    if (!mainContainer || !altContainer) {
        console.error('Image containers not found');
        return;
    }
    
    // Separate main and alternative images
    const mainImages = images.filter(img => img.is_main == 1);
    const altImages = images.filter(img => img.is_main != 1);
    
    // Main Image Section
    if (mainImages.length > 0) {
        const mainImage = mainImages[0];
        const imageUrl = `${window.location.origin}/devicesvn/public/${mainImage.image_url}`;
        const isMarkedForDeletion = imagesToDelete.includes(mainImage.id);
        
        if (isMarkedForDeletion) {
            // Show placeholder for marked-for-deletion main image
            mainContainer.innerHTML = `
                <div class="col-12">
                    <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                         style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                         onclick="document.getElementById('mainImageUpload').click()">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-0 text-muted"><small>Click to upload<br>main image</small></p>
                        </div>
                        <input type="file" id="mainImageUpload" name="main_image" accept="image/*" style="display:none;" onchange="previewUploadedImage(this, 'mainImageDisplay', true)">
                    </div>
                </div>
            `;
        } else {
            mainContainer.innerHTML = `
                <div class="col-12" data-image-id="${mainImage.id}">
                    <div class="position-relative" style="padding-bottom: 100%; overflow: hidden;">
                        <img src="${imageUrl}" 
                             class="rounded shadow-sm" 
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">MAIN IMAGE</span>
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" 
                                onclick="markImageForDeletion(${mainImage.id}, this)" 
                                style="padding: 4px 8px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }
    } else {
        mainContainer.innerHTML = `
            <div class="col-12">
                <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                     style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                     onclick="document.getElementById('mainImageUpload').click()">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                        <p class="mb-0 text-muted"><small>Click to upload<br>main image</small></p>
                    </div>
                    <input type="file" id="mainImageUpload" name="main_image" accept="image/*" style="display:none;" onchange="previewUploadedImage(this, 'mainImageDisplay', true)">
                </div>
            </div>
        `;
    }
    
    // Alternative Images Section
    altContainer.innerHTML = '';
    
    // Show existing alternative images
    altImages.forEach(image => {
        const imageUrl = `${window.location.origin}/devicesvn/public/${image.image_url}`;
        const isMarkedForDeletion = imagesToDelete.includes(image.id);
        
        if (!isMarkedForDeletion) {
            const col = document.createElement('div');
            col.className = 'col-6 mb-2';
            col.setAttribute('data-image-id', image.id);
            col.innerHTML = `
                <div class="position-relative" style="padding-bottom: 100%; overflow: hidden;">
                    <img src="${imageUrl}" 
                         class="rounded shadow-sm" 
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                            onclick="markImageForDeletion(${image.id}, this)" 
                            style="padding: 2px 6px; font-size: 11px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            altContainer.appendChild(col);
        }
    });
    
    // Add upload placeholders for remaining slots (up to 3 total)
    const activeAltImages = altImages.filter(img => !imagesToDelete.includes(img.id));
    const remainingSlots = 3 - activeAltImages.length;
    for (let i = 0; i < remainingSlots; i++) {
        const col = document.createElement('div');
        col.className = 'col-6 mb-2';
        col.innerHTML = `
            <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                 style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                 onclick="document.getElementById('altImageUpload${i}').click()">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                    <i class="fas fa-plus-circle fa-2x text-muted mb-1"></i>
                    <p class="mb-0 text-muted" style="font-size:10px;">Add Image</p>
                </div>
                <input type="file" id="altImageUpload${i}" name="alt_image_${i + 1}" accept="image/*" style="display:none;" onchange="previewUploadedImage(this, 'altImagesDisplay', false)">
            </div>
        `;
        altContainer.appendChild(col);
    }
}

/**
 * Preview uploaded image immediately
 */
function previewUploadedImage(input, containerId, isMain) {
    if (input.files && input.files[0]) {
        // Store file reference BEFORE destroying the input
        if (isMain) {
            uploadedMainImage = input.files[0];
        } else {
            uploadedAltImages[input.id] = input.files[0];
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById(containerId);
            
            if (isMain) {
                // Replace main image display
                container.innerHTML = `
                    <div class="col-12">
                        <div class="position-relative" style="padding-bottom: 100%; overflow: hidden;">
                            <img src="${e.target.result}" 
                                 class="rounded shadow-sm" 
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge bg-info position-absolute top-0 start-0 m-2">NEW</span>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" 
                                    onclick="cancelImageUpload('${containerId}', true)" 
                                    style="padding: 4px 8px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else {
                // Find the input that was used and its parent
                const parentCol = input.closest('.col-6');
                if (parentCol) {
                    const inputId = input.id;
                    parentCol.innerHTML = `
                        <div class="position-relative" style="padding-bottom: 100%; overflow: hidden;">
                            <img src="${e.target.result}" 
                                 class="rounded shadow-sm" 
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge bg-info position-absolute top-0 start-0 m-1" style="font-size:9px;">NEW</span>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                    onclick="cancelAltImageUpload('${inputId}', this)" 
                                    style="padding: 2px 6px; font-size: 10px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Cancel image upload (Edit Modal)
 */
function cancelImageUpload(containerId, isMain) {
    // Clear stored file
    if (isMain) {
        uploadedMainImage = null;
    }
    
    // Restore upload placeholder
    const container = document.getElementById(containerId);
    if (isMain && container) {
        container.innerHTML = `
            <div class="col-12">
                <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                     style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                     onclick="document.getElementById('mainImageUpload').click()">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                        <p class="mb-0 text-muted"><small>Click to upload<br>main image</small></p>
                    </div>
                    <input type="file" id="mainImageUpload" name="main_image" accept="image/*" style="display:none;" onchange="previewUploadedImage(this, 'mainImageDisplay', true)">
                </div>
            </div>
        `;
    }
}

/**
 * Cancel alt image upload (Edit Modal)
 */
function cancelAltImageUpload(inputId, button) {
    // Clear stored file
    if (uploadedAltImages[inputId]) {
        delete uploadedAltImages[inputId];
    }
    
    const parentCol = button.closest('.col-6');
    if (parentCol) {
        parentCol.innerHTML = `
            <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                 style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                 onclick="document.getElementById('${inputId}').click()">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                    <i class="fas fa-plus-circle fa-2x text-muted mb-1"></i>
                    <p class="mb-0 text-muted" style="font-size:10px;">Add Image</p>
                </div>
                <input type="file" id="${inputId}" name="${inputId.replace('altImageUpload', 'alt_image_')}" accept="image/*" style="display:none;" onchange="previewUploadedImage(this, 'altImagesDisplay', false)">
            </div>
        `;
    }
}

/**
 * Cancel main image upload (Add Modal)
 */
function cancelAddMainImage() {
    // Clear stored file
    uploadedMainImage = null;
    
    const container = document.getElementById('addMainImageDisplay');
    if (container) {
        container.innerHTML = `
            <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                 style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                 onclick="document.getElementById('addMainImageUpload').click()">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                    <p class="mb-0 text-muted"><small>Click to upload<br>main image</small></p>
                </div>
                <input type="file" id="addMainImageUpload" name="main_image" accept="image/*" style="display:none;" required onchange="previewAddImage(this, 'addMainImageDisplay', true)">
            </div>
        `;
    }
}

/**
 * Cancel alt image upload (Add Modal)
 */
function cancelAddAltImage(inputId, button) {
    // Clear stored file
    if (uploadedAltImages[inputId]) {
        delete uploadedAltImages[inputId];
    }
    
    // Find parent column using the button element
    const parentCol = button.closest('.col-6');
    if (parentCol) {
        const imageNum = inputId.replace('addAltImageUpload', '');
        parentCol.innerHTML = `
            <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                 style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                 onclick="document.getElementById('${inputId}').click()">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                    <i class="fas fa-plus-circle fa-2x text-muted mb-1"></i>
                    <p class="mb-0 text-muted" style="font-size:10px;">Add Image</p>
                </div>
                <input type="file" id="${inputId}" name="alt_image_${parseInt(imageNum) + 1}" accept="image/*" style="display:none;" onchange="previewAddImage(this, 'addAltImagesDisplay', false)">
            </div>
        `;
    }
}

/**
 * Mark image for deletion (not deleted until save)
 */
function markImageForDeletion(imageId, button) {
    if (!imagesToDelete.includes(imageId)) {
        imagesToDelete.push(imageId);
        // Reload only the image display to show the change
        loadEditImages(currentProductImages);
    }
}

/**
 * Restore image that was marked for deletion
 */
function restoreImage(imageId) {
    const index = imagesToDelete.indexOf(imageId);
    if (index > -1) {
        imagesToDelete.splice(index, 1);
        // Reload only the image display to show the change
        loadEditImages(currentProductImages);
    }
}

/**
 * Delete single image (kept for compatibility but not used in edit modal)
 */
function deleteImage(imageId) {
    fetch(`${window.location.origin}/devicesvn/dashboard/products/delete-image/${imageId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Image deleted successfully!', 'success');
            // Reload product data to refresh image list
            editProduct(currentProductId);
        } else {
            showToast(data.message || 'Failed to delete image', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting image. Please try again.', 'error');
    });
}

/**
 * Add new product
 */
function addProduct() {
    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
    const modalBody = document.getElementById('addProductContent');
    
    // Clear any previously uploaded files
    uploadedMainImage = null;
    uploadedAltImages = {};
    
    // Build category options
    let categoryOptions = '<option value="">Select Category</option>';
    categoriesList.forEach(cat => {
        categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
    });
    
    modalBody.innerHTML = `
        <form id="addProductForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-box me-2 text-primary"></i>Product Name *
                    </label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-barcode me-2 text-primary"></i>SKU *
                    </label>
                    <input type="text" class="form-control" name="sku" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-copyright me-2 text-primary"></i>Brand
                    </label>
                    <input type="text" class="form-control" name="brand" placeholder="e.g. Apple">
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
            
            <!-- Image Upload Section -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-images me-2 text-primary"></i>Product Images
                </label>
                
                <div class="row g-3">
                    <!-- Main Image Section -->
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <small class="fw-bold">Main Image *</small>
                            </div>
                            <div class="card-body">
                                <div id="addMainImageDisplay">
                                    <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                                         style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                                         onclick="document.getElementById('addMainImageUpload').click()">
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                            <p class="mb-0 text-muted"><small>Click to upload<br>main image</small></p>
                                        </div>
                                        <input type="file" id="addMainImageUpload" name="main_image" accept="image/*" style="display:none;" required onchange="previewAddImage(this, 'addMainImageDisplay', true)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alternative Images Section -->
                    <div class="col-md-6">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white py-2">
                                <small class="fw-bold">Alternative Images</small>
                            </div>
                            <div class="card-body">
                                <div class="row g-2" id="addAltImagesDisplay">
                                    <!-- 3 upload placeholders -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-align-left me-2 text-primary"></i>Description
                </label>
                <textarea class="form-control" name="description" rows="3" placeholder="Enter product description..."></textarea>
            </div>
            
            <!-- Specifications Builder -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-list-ul me-2 text-primary"></i>Specifications
                </label>
                <div class="card bg-light">
                    <div class="card-body">
                        <div id="specificationsContainer">
                            <!-- Specification rows will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-success mt-2" onclick="addSpecificationRow(document.getElementById('specificationsContainer'))">
                            <i class="fas fa-plus me-1"></i>Add Specification
                        </button>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>Add product specifications like Processor, RAM, Storage, etc.
                        </small>
                    </div>
                </div>
            </div>
        </form>
    `;
    
    // Add initial alternative image placeholders
    setTimeout(() => {
        addSpecificationRow(document.getElementById('specificationsContainer'));
        initializeAddAltImages();
    }, 100);
    
    modal.show();
}

/**
 * Initialize alternative images for add modal
 */
function initializeAddAltImages() {
    const container = document.getElementById('addAltImagesDisplay');
    for (let i = 0; i < 3; i++) {
        const col = document.createElement('div');
        col.className = 'col-6 mb-2';
        col.innerHTML = `
            <div class="upload-placeholder text-center border border-2 border-dashed rounded bg-light" 
                 style="cursor:pointer; padding-bottom: 100%; position: relative;" 
                 onclick="document.getElementById('addAltImageUpload${i}').click()">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                    <i class="fas fa-plus-circle fa-2x text-muted mb-1"></i>
                    <p class="mb-0 text-muted" style="font-size:10px;">Add Image</p>
                </div>
                <input type="file" id="addAltImageUpload${i}" name="alt_image_${i + 1}" accept="image/*" style="display:none;" onchange="previewAddImage(this, 'addAltImagesDisplay', false)">
            </div>
        `;
        container.appendChild(col);
    }
}

/**
 * Preview image in add modal
 */
function previewAddImage(input, containerId, isMain) {
    if (input.files && input.files[0]) {
        // Store file reference BEFORE destroying the input
        if (isMain) {
            uploadedMainImage = input.files[0];
        } else {
            uploadedAltImages[input.id] = input.files[0];
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (isMain) {
                const container = document.getElementById(containerId);
                container.innerHTML = `
                    <div class="position-relative" style="padding-bottom: 100%; overflow: hidden;">
                        <img src="${e.target.result}" 
                             class="rounded shadow-sm" 
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        <span class="badge bg-info position-absolute top-0 start-0 m-2">NEW</span>
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" 
                                onclick="cancelAddMainImage()" 
                                style="padding: 4px 8px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            } else {
                const parentCol = input.closest('.col-6');
                if (parentCol) {
                    const inputId = input.id;
                    parentCol.innerHTML = `
                        <div class="position-relative" style="padding-bottom: 100%; overflow: hidden;">
                            <img src="${e.target.result}" 
                                 class="rounded shadow-sm" 
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge bg-info position-absolute top-0 start-0 m-1" style="font-size:9px;">NEW</span>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                    onclick="cancelAddAltImage('${inputId}', this)" 
                                    style="padding: 2px 6px; font-size: 10px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
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
    
    // Remove empty file inputs from FormData
    const keysToDelete = [];
    for (let pair of formData.entries()) {
        if (pair[1] instanceof File && pair[1].size === 0 && pair[1].name === '') {
            keysToDelete.push(pair[0]);
        }
    }
    keysToDelete.forEach(key => formData.delete(key));
    
    // Add stored files
    if (uploadedMainImage) {
        formData.set('main_image', uploadedMainImage);
    }
    
    let altIndex = 1;
    for (let inputId in uploadedAltImages) {
        const file = uploadedAltImages[inputId];
        if (file && file.size > 0) {
            formData.set(`alt_image_${altIndex}`, file);
            altIndex++;
        }
    }
    
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
            showToast(data.message || 'Failed to add product', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product. Please try again.', 'error');
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
                    : '';
                
                // Build specifications HTML
                let specificationsHtml = '';
                if (product.specifications_array && Object.keys(product.specifications_array).length > 0) {
                    specificationsHtml = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                    for (const [key, value] of Object.entries(product.specifications_array)) {
                        specificationsHtml += `
                            <tr>
                                <td class="fw-bold bg-light" style="width: 35%">${key}</td>
                                <td>${value}</td>
                            </tr>
                        `;
                    }
                    specificationsHtml += '</table></div>';
                }
                
                content.innerHTML = `
                    <div class="row g-4">
                        ${imageUrl ? `
                        <div class="col-md-5">
                            <div class="product-image-container">
                                <img src="${imageUrl}" 
                                     class="img-fluid rounded shadow-sm" 
                                     alt="${product.name}"
                                     style="width: 100%; height: 300px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-7">` : '<div class="col-12">'}
                            <h4 class="mb-3 fw-bold text-primary">${product.name}</h4>
                            
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                ${product.brand 
                                    ? `<span class="badge bg-info px-3 py-2">
                                        <i class="fas fa-copyright me-1"></i>${product.brand}
                                       </span>`
                                    : ''
                                }
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
                            
                            ${specificationsHtml ? `
                                <div class="mb-3">
                                    <h6 class="fw-bold text-secondary mb-2">
                                        <i class="fas fa-list-ul me-2"></i>Specifications
                                    </h6>
                                    ${specificationsHtml}
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
    
    // Clear any previously uploaded files and marked deletions
    uploadedMainImage = null;
    uploadedAltImages = {};
    imagesToDelete = [];
    currentProductImages = [];
    
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
                    <form id="editProductForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-box me-2 text-primary"></i>Product Name *
                                </label>
                                <input type="text" class="form-control" name="name" value="${product.name}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-barcode me-2 text-primary"></i>SKU *
                                </label>
                                <input type="text" class="form-control" name="sku" value="${product.sku}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-copyright me-2 text-primary"></i>Brand
                                </label>
                                <input type="text" class="form-control" name="brand" value="${product.brand || ''}" placeholder="e.g. Apple">
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
                        
                        <!-- Image Management Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-images me-2 text-primary"></i>Product Images
                            </label>
                            
                            <div class="row g-3">
                                <!-- Main Image Section -->
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white py-2">
                                            <small class="fw-bold">Main Image</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2" id="mainImageDisplay">
                                                <!-- Main image will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Alternative Images Section -->
                                <div class="col-md-6">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-secondary text-white py-2">
                                            <small class="fw-bold">Alternative Images</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2" id="altImagesDisplay">
                                                <!-- Alternative images will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Description
                            </label>
                            <textarea class="form-control" name="description" rows="3">${product.description || ''}</textarea>
                        </div>
                        
                        <!-- Specifications Builder -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-list-ul me-2 text-primary"></i>Specifications
                            </label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div id="editSpecificationsContainer">
                                        <!-- Specification rows will be added here -->
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="addSpecificationRow(document.getElementById('editSpecificationsContainer'))">
                                        <i class="fas fa-plus me-1"></i>Add Specification
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                `;
                
                // Populate existing specifications
                setTimeout(() => {
                    const container = document.getElementById('editSpecificationsContainer');
                    if (product.specifications_array && Object.keys(product.specifications_array).length > 0) {
                        for (const [key, value] of Object.entries(product.specifications_array)) {
                            addSpecificationRow(container, key, value);
                        }
                    } else {
                        addSpecificationRow(container);
                    }
                    
                    // Store and load current images with upload functionality
                    currentProductImages = product.images || [];
                    loadEditImages(currentProductImages);
                }, 100);
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
    
    // Remove empty file inputs from FormData (they get added automatically by FormData(form))
    const keysToDelete = [];
    for (let pair of formData.entries()) {
        if (pair[1] instanceof File && pair[1].size === 0 && pair[1].name === '') {
            keysToDelete.push(pair[0]);
        }
    }
    keysToDelete.forEach(key => formData.delete(key));
    
    // Add stored main image file
    if (uploadedMainImage) {
        formData.set('main_image', uploadedMainImage);
    }
    
    // Add stored alternative images
    let altIndex = 1;
    for (let inputId in uploadedAltImages) {
        const file = uploadedAltImages[inputId];
        if (file && file.size > 0) {
            formData.append(`alt_image_${altIndex}`, file);
            altIndex++;
        }
    }
    
    // Add images marked for deletion
    if (imagesToDelete.length > 0) {
        formData.append('delete_images', JSON.stringify(imagesToDelete));
    }
    
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
            showToast(data.message || 'Failed to update product', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating product. Please try again.', 'error');
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
                showToast(data.message || 'Failed to delete product', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting product. Please try again.', 'error');
        });
    };
}

/**
 * Show custom notification
 */
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
