<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation - EESHA SILKS</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #2D2A26; margin: 0; padding: 0; background-color: #FCF9F4; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border: 1px solid #D4AF37; }
        .header { background-color: #5D0E41; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-family: 'Playfair Display', serif; letter-spacing: 2px; }
        .content { padding: 40px; }
        .order-meta { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #f9f9f9; padding-bottom: 10px; }
        .totals { margin-top: 30px; text-align: right; }
        .footer { background-color: #f8f8f8; color: #6B6661; padding: 20px; text-align: center; font-size: 12px; }
        .btn { background-color: #D4AF37; color: #ffffff; padding: 12px 25px; text-decoration: none; display: inline-block; margin-top: 20px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EESHA SILKS</h1>
            <p style="margin-top: 5px; opacity: 0.8;">HANDLOOM BOUTIQUE</p>
        </div>
        <div class="content">
            <h2>Thank You for Your Order!</h2>
            <p>Dear <?= esc($customer_name) ?>,</p>
            <p>We are delighted to confirm that we have received your order. Our artisans are now preparing your heritage pieces for their journey to you.</p>
            
            <div class="order-meta">
                <p><strong>Order ID:</strong> #<?= esc($id) ?></p>
                <p><strong>Date:</strong> <?= date('F j, Y, g:i a') ?></p>
            </div>

            <h3>Order Summary</h3>
            <?php foreach ($items as $item): ?>
            <div class="item-row">
                <span><?= esc($item['product_name']) ?> (x<?= esc($item['quantity']) ?>)</span>
                <span>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
            </div>
            <?php endforeach; ?>

            <div class="totals">
                <p>Subtotal: ₹<?= number_format($subtotal, 2) ?></p>
                <?php if ($discount > 0): ?>
                <p>Discount: -₹<?= number_format($discount, 2) ?></p>
                <?php endif; ?>
                <p>Delivery: ₹<?= number_format($delivery_fee, 2) ?></p>
                <h3 style="color: #5D0E41;">Total: ₹<?= number_format($total_amount, 2) ?></h3>
            </div>

            <div style="text-align: center;">
                <a href="<?= base_url('my-orders') ?>" class="btn">View Order Status</a>
            </div>
        </div>
        <div class="footer">
            <p>Railway Mens HBCS limited layout, Mallathahalli, Bengaluru</p>
            <p>&copy; <?= date('Y') ?> EESHA SILKS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
