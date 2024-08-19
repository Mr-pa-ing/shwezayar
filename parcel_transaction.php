<?php include 'db_connect.php'; ?>
<div class="col-lg-12">
    <div class="card card-outline">
        <div class="card-header">
            <h5 class="card-title">Parcel Transactions</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover" id="transaction-list">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Transaction No</th>
                        <th>Sender Name</th>
                        <th>Recipient Name</th>
                        <th>Total Items</th>
                        <th>Total Kg</th>
                        <th>Total Amount</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        // Aggregated query to group items by reference_number and sum the relevant fields
                        $qry = $conn->query("SELECT 
                            p.transaction_number, 
                            p.sender_name, 
                            p.recipient_name, 
                            COUNT(p.id) as total_items, 
                            SUM(p.length) as total_kg, 
                            SUM(p.amount) as total_amount, 
                            MIN(p.date_created) as date_created
                        FROM 
                            parcels p
                        GROUP BY 
                            p.transaction_number, 
                            p.sender_name, 
                            p.recipient_name
                        ORDER BY 
                            unix_timestamp(date_created) DESC");


                    if($qry && $qry->num_rows > 0) {
                        while ($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['transaction_number']); ?></td>
                        <td><?php echo ucwords(htmlspecialchars($row['sender_name'])); ?></td>
                        <td><?php echo ucwords(htmlspecialchars($row['recipient_name'])); ?></td>
                        <td><?php echo number_format($row['total_items'], 0); ?></td>
                        <td><?php echo number_format($row['total_kg'], 0); ?></td>
                        <td><?php echo number_format($row['total_amount'], 0); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])); ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="index.php?page=print_invoice&transaction_number=<?php echo htmlspecialchars($row['transaction_number']); ?>"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#transaction-list').dataTable();

    $('#transaction-list').on('click', '.delete_parcel', function() {
        _conf("Are you sure to delete this transaction?", "delete_parcel", [$(this).attr('data-id')]);
    });
});

function delete_parcel($id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_parcel',
        method: 'POST',
        data: {
            id: $id
        },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Transaction successfully deleted", 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }
    });
}
</script>
