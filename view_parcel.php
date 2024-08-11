<?php
require 'vendor/autoload.php';

include 'db_connect.php';
include 'qrcode.php';

require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorHTML;

$generator = new BarcodeGeneratorHTML();

$qry = $conn->query("SELECT * FROM parcels where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
if($to_branch_id > 0 || $from_branch_id > 0){
	$to_branch_id = $to_branch_id  > 0 ? $to_branch_id  : '-1';
	$from_branch_id = $from_branch_id  > 0 ? $from_branch_id  : '-1';
$branch = array();
 $branches = $conn->query("SELECT *,concat(street,', ',city,', ',state,', ',zip_code,', ',country) as address FROM branches where id in ($to_branch_id,$from_branch_id)");
    while($row = $branches->fetch_assoc()):
    	$branch[$row['id']] = $row['address'];
	endwhile;
}
?>
<?php error_reporting(0);?>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-info d-flex justify-content-between">
                    <dl>
                        <dt>Tracking Number:</dt>
                        <dd>
                            <h4><b><?php echo $reference_number ?></b></h4>
                            <?php echo $generator->getBarcode('123456789', $generator::TYPE_CODE_128); ?>
                        </dd>
                    </dl>
                    <dl>
                        <?php echo "<img src='qrcode.php?s=qrl&d=$reference_number' alt='qr'>" ?>
                    </dl>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="callout callout-info py-4">
                    <b class="border-bottom border-primary">Customer Information</b>
                    <dl>
                        <dt class="my-3">Name:
                        	<span><?php echo ucwords($sender_name) ?></span>
						</dt>
                        <dt>Contact:
                        	<span><?php echo ucwords($sender_contact) ?></span>
						</dt>
                    </dl>
                </div>
                <!-- <div class="callout callout-info">
                    <b class="border-bottom border-primary">Recipient Information</b>
                    <dl>
                        <dt>Name:</dt>
                        <dd><?php echo ucwords($recipient_name) ?></dd>
                        <dt>Address:</dt>
                        <dd><?php echo ucwords($recipient_address) ?></dd>
                        <dt>Contact:</dt>
                        <dd><?php echo ucwords($recipient_contact) ?></dd>
                    </dl>
                </div> -->
            </div>
            <div class="col-md-6">
                <div class="callout callout-info">
                    <b class="border-bottom border-primary">Parcel Details</b>
					<table class="table table-bordered">
						<thead>
							<th>Description</th>
							<th>Qty</th>
							<th>Kg</th>
							<th>Total Kg</th>
							<th>Unit Price</th>
							<th>Amount</th>
							<th>Bag</th>
						</thead>
						<tbody>
							<tr>

								<td><span><?php echo $weight ?></span></td>
								<td><span><?php echo $length ?></span></td>
								<td><span><?php echo $height ?></span></td>
								<td><span><span><?php echo $width ?></span></td>
								<td><span><?php echo number_format($price,0) ?></span></td>
								<td><span><?php echo number_format($amount,0) ?></span></td>
								<td><span><?php echo $bag ?></span></td>
							</tr>
						</tbody>
					</table>
                    <!-- <dl>
                        <dt>Branch Accepted the Parcel:</dt>
                        <dd><?php echo ucwords($branch[$from_branch_id]) ?></dd>
                        <?php if($type == 2): ?>
                        <dt>Nearest Branch to Recipient for Pickup:</dt>
                        <dd><?php echo ucwords($branch[$to_branch_id]) ?></dd>
                        <?php endif; ?>
                        <dt>Status:</dt>
                        <dd>
                            <?php 
							switch ($status) {
								case '1':
									echo "<span class='badge badge-info'> Collected</span>";
									break;
								case '2':
									echo "<span class='badge badge-secondary'> Shipped</span>";
									break;
								
								default:
									echo "<span class='badge badge-warning'> Item Accepted</span>";
									
									break;
							}

							?>
                            <span class="btn btn-sm btn-primary mb-3" id='update_status'>Update</span>
                        </dd>

                    </dl> -->
                </div>
            </div>
        </div>
    </div>
	<div id="barcode-container" style="display:none;">
    <!-- Barcodes will be dynamically inserted here by JavaScript -->
</div>

</div>
<div class="modal-footer display p-0 m-0">
	<button type="button" class="btn btn-primary" id="print_btn">Print</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<style>
#uni_modal .modal-footer {
    display: none
}

#uni_modal .modal-footer.display {
    display: flex
}
</style>
<noscript>
    <style>
    table.table {
        width: 100%;
        border-collapse: collapse;
    }

    table.table tr,
    table.table th,
    table.table td {
        border: 1px solid;
    }

    .text-cnter {
        text-align: center;
    }
    </style>
</noscript>
<script>
$('#update_status').click(function() {
    uni_modal("Update Status of: <?php echo $reference_number ?>",
        "manage_parcel_status.php?id=<?php echo $id ?>&cs=<?php echo $status ?>", "")
})
$('#print_btn').click(function() {
    printLabels();
});

function printLabels() {
    start_load();

    var ns = $('noscript').clone();
    var barcodeContainer = $('<div style="display: flex; flex-wrap: wrap;"></div>');
    var bagCount = <?php echo $bag; ?>;
    var companyName = "Shwe Zayar"; // Replace with your actual company name
    var referenceNumber = "<?php echo $reference_number; ?>";
    var weightKg = "<?php echo $height; ?>";

    for (var i = 0; i < bagCount; i++) {
        var barcodeHTML = `
            <div style="text-align: center; margin-bottom: 10px; margin-right: 10px; padding: 10px; border: 1px solid #000; width: 200px;">
                <div style="font-weight: bold; margin-bottom: 5px;">${companyName}</div>
                <?php echo $generator->getBarcode($reference_number, $generator::TYPE_CODE_128); ?>
                <div style="margin-top: 5px;display: flex; align-items: center; justify-content: space-between;">
                    <div><strong>${referenceNumber}</strong></div>
                    <div><strong>${weightKg} Kg</strong></div>
                </div>
            </div>
        `;
        barcodeContainer.append(barcodeHTML);
    }

    ns.append(barcodeContainer);

    var nw = window.open('', '', 'height=700,width=900');
    nw.document.write(ns.html());
    nw.document.close();

    nw.onload = function() {
        nw.print();
        setTimeout(function() {
            nw.close();
            end_load();
        }, 750);
    };
}

</script>