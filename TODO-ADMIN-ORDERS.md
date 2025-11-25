# TODO: Admin Orders Management

## Overview
Create a separate admin orders page (admin-order.php) that differs from the customer orders page.

## Requirements

### Features Needed:
1. **View All Orders** (from all customers)
   - List all orders with customer information
   - Filter by status (pending, confirmed, shipped, delivered, cancelled)
   - Search by order number or customer name
   - Sort by date, total amount, status

2. **Order Management**
   - Update order status
   - View detailed order information
   - View customer details
   - Print invoice/receipt
   - Add notes to orders

3. **Create Manual Orders** (for in-store purchases)
   - Search and select customer (or create new)
   - Add products to order
   - Set shipping information
   - Calculate total
   - Mark as "store purchase" or "phone order"
   - Choose payment method (cash, card, bank transfer)
   - Process payment immediately

4. **Order Statistics**
   - Total orders today/week/month
   - Revenue statistics
   - Order status breakdown
   - Popular products from orders

## Implementation Plan

### 1. Create Admin Order Views
- `resources/views/admin/orders/index.php` - List all orders
- `resources/views/admin/orders/detail.php` - Order details with management
- `resources/views/admin/orders/create.php` - Create manual order for store purchases

### 2. Update OrderController
Add admin-specific methods:
- `adminIndex()` - List all orders for admin
- `adminDetail($orderId)` - Admin order detail view
- `createManual()` - Form to create manual order
- `storeManual()` - Process manual order creation
- `updateStatus()` - Update order status

### 3. Add Routes
```php
'admin/orders' => OrderController::adminIndex
'admin/orders/create' => OrderController::createManual
'admin/orders/:id' => OrderController::adminDetail
'admin/orders/:id/update-status' => OrderController::updateStatus
```

### 4. Database Considerations
- Add `order_type` column: 'online', 'store', 'phone'
- Add `payment_method` column: 'cod', 'cash', 'card', 'bank_transfer'
- Add `payment_status` column: 'pending', 'paid', 'refunded'
- Add `staff_id` column to track which admin created the order

### 5. Additional Features
- Export orders to Excel/CSV
- Print order receipt/invoice
- Bulk status updates
- Order notifications to customers

## Notes
- Admin orders page should be accessible only to admin role
- Include middleware/guard to check admin access
- Customer orders page (current) remains separate and simpler
- Store purchases should skip shipping requirements
