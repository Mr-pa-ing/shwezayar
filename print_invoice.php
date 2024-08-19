<?php
include 'db_connect.php';

// Get the transaction number from the URL
$transaction_number = isset($_GET['transaction_number']) ? $_GET['transaction_number'] : '';

if (empty($transaction_number)) {
    die("Invalid transaction number.");
}

// Fetch the transaction details grouped by the transaction number
$qry = $conn->query("SELECT * FROM parcels WHERE transaction_number = '$transaction_number'");

if (!$qry || $qry->num_rows == 0) {
    die("Transaction not found.");
}

// Get the first item to display sender and recipient information
$transaction = $qry->fetch_assoc();

// Generate Invoice ID
$invoice_id = 'INV-' . $transaction_number;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?php echo $invoice_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 90%;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 20px;
            color: #555;
            background-color: #fff;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        @media print {
            @page {
                size: A5 landscape;
                margin: 0;
                padding: 0;
            }

            .invoice-box {
                width: 100%;
                height: 100%;
                padding: 10px;
                margin: auto;
                padding: 0;
                box-shadow: none;
                border: none;
                page-break-after: avoid;
            }

            .invoice-box table tr.heading td {
                padding-bottom: 15px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 1.2rem;
            }

            table tr.item td {
                padding: 10px 0;
            }

            .line {
                line-height: 30px;
            }

            .invoice-box table tr.total td{
                padding-top: 15px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="8">
                    <table>
                        <tr>
                            <td class="title d-flex align-items-center">
                                <img src="assets/uploads/logo1.png" width="90px" height="90px">&nbsp;&nbsp;
                                <h2>Invoice</h2>
                            </td>
                            <td class="text-right line">
                                Invoice #: <?php echo $invoice_id; ?><br>
                                Date: <?php echo date("d-m-Y", strtotime($transaction['date_created'])); ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="8">
                    <table>
                        <tr>
                            <td class="text-left line">
                                <strong>From:</strong><br>
                                <?php echo htmlspecialchars($transaction['sender_name']); ?><br>
                                <?php echo htmlspecialchars($transaction['sender_address']); ?><br>
                                <?php echo htmlspecialchars($transaction['sender_contact']); ?>
                            </td>
                            <td class="text-right line">
                                <strong>To:</strong><br>
                                <?php echo htmlspecialchars($transaction['recipient_name']); ?><br>
                                <?php echo htmlspecialchars($transaction['recipient_address']); ?><br>
                                <?php echo htmlspecialchars($transaction['recipient_contact']); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Ref. No</td>
                <td>Description</td>
                <td class="text-right">Qty</td>
                <td class="text-right">Kg</td>
                <td class="text-right">Total Kg</td>
                <td class="text-right">Unit Price</td>
                <td class="text-right">Total Amount</td>
                <!-- <td class="text-right">Remark</td> -->
            </tr>

            <?php
            // Initialize totals
            $total_items = 0;
            $total_kg = 0;
            $total_total_kg = 0;
            $total_amount = 0;

            // Reset result pointer and loop through items to display them
            $qry->data_seek(0);
            while ($item = $qry->fetch_assoc()):
                $total_items++;
                $total_kg += $item['length'];
                $total_total_kg += $item['width'];
                $total_amount += $item['amount'];
            ?>
            <tr class="item">
                <td><?php echo htmlspecialchars($item['reference_number']); ?></td>
                <td><?php echo htmlspecialchars($item['weight']); ?></td>
                <td class="text-right"><?php echo htmlspecialchars($item['height']); ?></td>
                <td class="text-right"><?php echo htmlspecialchars($item['length']); ?></td>
                <td class="text-right"><?php echo htmlspecialchars($item['width']); ?></td>
                <td class="text-right"><?php echo number_format($item['price'], 0); ?></td>
                <td class="text-right"><?php echo number_format($item['amount'], 0); ?></td>
                <!-- <td class="text-right"><?php echo htmlspecialchars($item['remark']); ?></td> -->
            </tr>
            <?php endwhile; ?>
            
            <tr class="total">
                <td><strong>Total</strong></td>
                <td></td>
                <td class="text-right"><strong><?php echo $total_items; ?></strong></td>
                <td class="text-right"><strong><?php echo number_format($total_kg, 0); ?></strong></td>
                <td class="text-right"><strong><?php echo number_format($total_total_kg, 0); ?></strong></td>
                <td></td>
                <td class="text-right"><strong><?php echo number_format($total_amount, 0); ?></strong></td>
            </tr>
        </table>
        <div class="no-print mt-5">
            <button onclick="window.print()"><i class="fas fa-print"></i> Print Invoice</button>
        </div>
    </div>
</body>
</html>
