<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?= $sale['order_number'] ?></title>
    <style>
        body {
            font-family: monospace;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
        }
        .receipt {
            text-align: center;
        }
        .receipt h3 {
            margin: 0;
        }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            text-align: left;
        }
        table td, table th {
            padding: 2px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h3>RStore</h3>
        <p>123 Business St.<br>City, State 12345<br>Tel: (123) 456-7890</p>
        <hr>
        <p>
            <strong>Order #:</strong> <?= $sale['order_number'] ?><br>
            <strong>Date:</strong> <?= date('Y-m-d H:i:s', strtotime($sale['created_at'])) ?><br>
            <strong>Cashier:</strong> <?= session()->get('email') ?? 'Admin' ?>
        </p>
        <hr>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sale['items'] as $item): ?>
                <tr>
                    <td><?= esc(substr($item['product_name'], 0, 20)) ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-right">₱<?= number_format($item['price'], 2) ?></td>
                    <td class="text-right">₱<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">₱<?= number_format($sale['subtotal'], 2) ?></td>
            </tr>
            <?php if ($sale['discount'] > 0): ?>
            <tr>
                <td>Discount:</td>
                <td class="text-right">-₱<?= number_format($sale['discount'], 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>₱<?= number_format($sale['total'], 2) ?></strong></td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td class="text-right">₱<?= number_format($sale['amount_paid'], 2) ?></td>
            </tr>
            <tr>
                <td>Change:</td>
                <td class="text-right">₱<?= number_format($sale['change_due'], 2) ?></td>
            </tr>
        </table>
        <hr>
        <?php if ($sale['notes']): ?>
            <p><strong>Notes:</strong><br><?= nl2br(esc($sale['notes'])) ?></p>
            <hr>
        <?php endif; ?>
        <p>Thank you for your purchase!<br>Have a great day!</p>
        <br>
        <button onclick="window.print();">Print Receipt</button>
        <button onclick="window.close();">Close</button>
    </div>
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>