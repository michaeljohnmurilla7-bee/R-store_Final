<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= isset($sale->invoice_number) ? $sale->invoice_number : 'N/A' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            .invoice-box { margin: 0; padding: 0; }
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }
            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="invoice-box no-print mb-3 text-center">
    <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    <a href="<?= base_url('sales') ?>" class="btn btn-secondary">Back to Sales</a>
</div>

<div class="invoice-box">
    <?php if(isset($sale) && $sale): ?>
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <h2>RStore</h2>
                            <h4>SALES INVOICE</h4>
                        </td>
                        <td>
                            Invoice #: <?= isset($sale->invoice_number) ? $sale->invoice_number : 'N/A' ?><br>
                            Date: <?= isset($sale->sale_date) ? date('F d, Y', strtotime($sale->sale_date)) : date('Y-m-d') ?><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <strong>Sold To:</strong><br>
                            <?= isset($sale->customer_name) && $sale->customer_name ? $sale->customer_name : 'Walk-in Customer' ?><br>
                            <?= isset($sale->address) ? $sale->address : '' ?><br>
                            <?= isset($sale->phone) ? $sale->phone : '' ?>
                        </td>
                        <td>
                            <strong>Store:</strong><br>
                            RStore<br>
                            Sales & Inventory System<br>
                            <?= date('Y') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="heading">
            <td>Item</td>
            <td>Price</td>
        </tr>
        
        <?php if(isset($items) && !empty($items)): ?>
            <?php foreach($items as $item): ?>
            <tr class="item">
                <td><?= isset($item->product_name) ? $item->product_name : 'Product' ?> (x<?= isset($item->quantity) ? $item->quantity : 0 ?>)</td>
                <td>₱ <?= isset($item->subtotal) ? number_format($item->subtotal, 2) : '0.00' ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr class="item">
                <td colspan="2" class="text-center">No items found</td>
            </tr>
        <?php endif; ?>
        
        <tr class="total">
            <td></td>
            <td>Subtotal: ₱ <?= isset($sale->total_amount) ? number_format($sale->total_amount, 2) : '0.00' ?></td>
        </tr>
        
        <?php if(isset($sale->discount) && $sale->discount > 0): ?>
        <tr class="total">
            <td></td>
            <td>Discount: ₱ <?= number_format($sale->discount, 2) ?></td>
        </tr>
        <?php endif; ?>
        
        <tr class="total">
            <td></td>
            <td><strong>Total: ₱ <?= number_format(($sale->total_amount ?? 0) - ($sale->discount ?? 0), 2) ?></strong></td>
        </tr>
        
        <tr class="total">
            <td></td>
            <td>Amount Paid: ₱ <?= isset($sale->amount_paid) ? number_format($sale->amount_paid, 2) : '0.00' ?></td>
        </tr>
        
        <tr class="total">
            <td></td>
            <td><strong>Due: ₱ <?= isset($sale->due_amount) ? number_format($sale->due_amount, 2) : '0.00' ?></strong></td>
        </tr>
    </table>
    
    <div style="margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <p>Thank you for your business!</p>
        <p><small>This is a computer generated invoice. No signature required.</small></p>
    </div>
    
    <?php else: ?>
    <div class="text-center">
        <h3>Sale not found</h3>
        <p>The sale you're looking for does not exist.</p>
        <a href="<?= base_url('sales') ?>" class="btn btn-primary">Back to Sales</a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>