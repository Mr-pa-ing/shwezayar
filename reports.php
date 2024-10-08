<?php include 'db_connect.php' ?>
<?php $status = isset($_GET['status']) ? $_GET['status'] : 'all' ?>
<div class="col-lg-12">
    <div class="card card-outline">
        <div class="card-body">
            <div class="d-flex w-100 px-1 py-1 justify-content-end align-items-center">
                <?php 
			$status_arr = array("Item Accepted","Collected","Shipped"); ?>
                <!-- <label for="date_from" class="mx-3">Status</label> -->
                <select name="" id="status" class="custom-select custom-select-sm col-sm-3 d-none">
                    <option value="all" <?php echo $status == 'all' ? "selected" :'' ?>>All</option>
                    <?php foreach($status_arr as $k => $v): ?>
                    <option value="<?php echo $k ?>" <?php echo $status != 'all' && $status == $k ? "selected" :'' ?>>
                        <?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="date_from" class="mx-3">From</label>
                <input type="date" id="date_from" class="form-control form-control col-sm-3"
                    value="<?php echo isset($_GET['date_from']) ? date("Y-m-d",strtotime($_GET['date_from'])) : '' ?>">
                <label for="date_to" class="mx-3">To</label>
                <input type="date" id="date_to" class="form-control form-control col-sm-3"
                    value="<?php echo isset($_GET['date_to']) ? date("Y-m-d",strtotime($_GET['date_to'])) : '' ?>">
                <button class="btn btn-primary mx-4 mb-2 rounded-0" type="button" id='view_report'
                    style="border-radius: 5px !important;">View</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-success float-right mb-3" style="display: none"
                                id="print"><i class="fa fa-print"></i> Invoice</button>
                            <button class="btn btn-info float-right mb-3 mr-3" id="Commodity"><i
                                    class="fa fa-print"></i> Commodity</button>
                        </div>
                    </div>

                    <table class="table table-bordered" id="report-list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Kg</th>
                                <th>Total Kg</th>
                                <th>Unit price</th>
                                <th>Amount</th>
                                <th>Bag</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<noscript>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    h3 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .details p {
        font-size: 14px;
        margin-bottom: 5px;
    }

    table.table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table.table th,
    table.table td {
        border: 1px solid #000;
        padding: 8px;
        font-size: 14px;
    }

    table.table th {
        background-color: #f0f0f0;
        text-align: left;
    }

    table.table td {
        text-align: left;
    }

    tfoot th {
        border-top: 2px solid #000;
        font-weight: bold;
    }

    /* Adjustments for smaller screens or pages */
    @media print {
        body {
            font-size: 12px;
            margin: 10px;
        }

        table.table th,
        table.table td {
            font-size: 12px;
        }

        h3 {
            font-size: 20px;
        }

        .details p {
            font-size: 12px;
        }
    }
    </style>
    <h3 class="text-center"><b>Report</b></h3>
</noscript>

<div class="details d-none">
    <p><b>Date Range:</b> <span class="drange"></span></p>
    <!-- <p><b>Status:</b> <span class="status-field">All</span></p> -->
</div>
<script>
function load_report() {
    start_load()
    var date_from = $('#date_from').val()
    var date_to = $('#date_to').val()
    var status = $('#status').val()
    $.ajax({
        url: 'ajax.php?action=get_report',
        method: 'POST',
        data: {
            status: status,
            date_from: date_from,
            date_to: date_to
        },
        error: err => {
            console.log(err)
            alert_toast("An error occurred", 'error')
            end_load()
        },
        success: function(resp) {
            try {
                resp = JSON.parse(resp) // Ensure response is parsed as JSON
            } catch (e) {
                console.error("Failed to parse response:", e)
                alert_toast("An error occurred while processing the report.", 'error')
                end_load()
                return
            }

            if (resp && Object.keys(resp).length > 0) {
                $('#report-list tbody').html('') // Clear any existing rows
                var i = 1;
                var totalQty = 0,
                    totalKg = 0,
                    totalAmount = 0,
                    totalBag = 0;

                Object.keys(resp).forEach(function(k) {
                    if (resp[k]) { // Check if resp[k] is defined
                        var tr = $('<tr></tr>')
                        tr.append('<td>' + (i++) + '</td>')
                        tr.append('<td>' + (resp[k].date_created || '') + '</td>')
                        tr.append('<td>' + (resp[k].sender_name || '') + '</td>')
                        tr.append('<td>' + (resp[k].weight || '') + '</td>')
                        tr.append('<td>' + (resp[k].length || '') + '</td>')
                        tr.append('<td>' + (resp[k].height || '') + '</td>')
                        tr.append('<td>' + (resp[k].width || '') + '</td>')
                        tr.append('<td>' + (resp[k].price || '') + '</td>')
                        tr.append('<td>' + (resp[k].amount || '') + '</td>')
                        tr.append('<td>' + (resp[k].bag || '') + '</td>')
                        tr.append('<td>' + (resp[k].remark || '') + '</td>')

                        // Calculate totals
                        totalQty += parseFloat(resp[k].length) || 0;
                        totalKg += parseFloat(resp[k].width) || 0;
                        totalAmount += parseFloat((resp[k].amount).replace(/,/g, '')) || 0;
                        totalBag += parseFloat(resp[k].bag) || 0;

                        $('#report-list tbody').append(tr)
                    }
                })

                // Append the totals row
                var tr = $('<tr></tr>')
                tr.append('<td colspan="4" class="text-right"><b>Total:</b></td>')
                tr.append('<td><b>' + totalQty.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                    '</b></td>')
                tr.append('<td></td>')
                tr.append('<td><b>' + totalKg.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                    '</b></td>')
                tr.append('<td></td>')
                tr.append('<td><b>' + totalAmount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                    '</b></td>')
                tr.append('<td><b>' + totalBag.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                    '</b></td>')
                tr.append('<td></td>')

                $('#report-list tbody').append(tr)

                $('#print').show()
            } else {
                $('#report-list tbody').html('')
                var tr = $('<tr></tr>')
                tr.append('<th class="text-center" colspan="11">No result.</th>')
                $('#report-list tbody').append(tr)
                $('#print').hide()
            }
        },
        complete: function() {
            end_load()
        }
    })
}

$('#view_report').click(function() {
    if ($('#date_from').val() == '' || $('#date_to').val() == '') {
        alert_toast("Please select dates first.", "error")
        return false;
    }
    load_report()
    var date_from = $('#date_from').val()
    var date_to = $('#date_to').val()
    var status = $('#status').val()
    var target = './index.php?page=reports&filtered&date_from=' + date_from + '&date_to=' + date_to +
        '&status=all'
    window.history.pushState({}, null, target);
})

$(document).ready(function() {
    if ('<?php echo isset($_GET['filtered']) ?>' == 1)
        load_report()
})
$('#print').click(function() {
    start_load()
    var ns = $('noscript').clone()
    var details = $('.details').clone()
    var content = $('#report-list').clone()
    var date_from = $('#date_from').val()
    var date_to = $('#date_to').val()
    var status = $('#status').val()
    var stat_arr = '<?php echo json_encode($status_arr) ?>';
    stat_arr = JSON.parse(stat_arr);
    details.find('.drange').text(date_from + " to " + date_to)
    if (status > -1)
        details.find('.status-field').text(stat_arr[status])
    ns.append(details)

    ns.append(content)
    var nw = window.open('', '', 'height=700,width=900')
    nw.document.write(ns.html())
    nw.document.close()
    nw.print()
    setTimeout(function() {
        nw.close()
        end_load()
    }, 750)

})

$('#Commodity').click(function() {
    printCommodity();
});

function printCommodity() {
    start_load();

    // Clone the necessary elements for printing
    var ns = $('noscript').clone();
    var details = $('.details').clone();
    var content = $('#report-list').clone();
    var date_from = $('#date_from').val();
    var date_to = $('#date_to').val();
    var status = $('#status').val();

    // Set the date range in the details section
    details.find('.drange').text(date_from + " to " + date_to);

    // Remove any existing totals row
    content.find('tr').last().remove();

    // Remove the Unit Price and Amount columns from the content
    content.find('th:nth-child(8), th:nth-child(9), td:nth-child(8), td:nth-child(9)').remove();

    // Recalculate the totals
    var totalQty = 0;
    var totalKg = 0;
    var totalBag = 0;

    content.find('tbody tr').each(function() {
        totalQty += parseFloat($(this).find('td:nth-child(5)').text()) || 0;
        totalKg += parseFloat($(this).find('td:nth-child(7)').text()) || 0;
        totalBag += parseFloat($(this).find('td:nth-child(8)').text()) || 0;
    });

    // Append the totals row
    var tr = $('<tr></tr>');
    tr.append('<td colspan="4" class="text-right"><b>Total:</b></td>');
    tr.append('<td><b>' + totalQty.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b></td>');
    tr.append('<td></td>'); // For the Kg column
    tr.append('<td><b>' + totalKg.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b></td>');
    tr.append('<td><b>' + totalBag.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b></td>');
    tr.append('<td></td>'); // For the empty columns after Bag

    content.find('tbody').append(tr);

    // Append the modified content for printing
    ns.append(details);
    ns.append(content);

    // Open a new window to print the commodity report
    var nw = window.open('', '', 'height=700,width=900');
    nw.document.write(ns.html());
    nw.document.close();
    nw.print();

    // Close the window after printing
    setTimeout(function() {
        nw.close();
        end_load();
    }, 750);
}
</script>