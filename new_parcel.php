<?php if(!isset($conn)){ include 'db_connect.php'; } ?>
<style>
textarea {
    resize: none;
}
</style>

<div class="card card-outline">
    <div class="card-body">
        <form action="" id="manage-parcel">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div id="msg" class=""></div>
            <div class="row">
                <div class="col-md-3">
                  <h3 class="pl-0"><?php echo $title ?></h3>
                    <div class="form-group">
                        <label for="" class="control-label">Name</label>
                        <input type="text" name="sender_name" id="" class="form-control"
                            value="<?php echo isset($sender_name) ? $sender_name : '' ?>" required>
                    </div>
                    <div class="form-group d-none">
                        <label for="" class="control-label">Address</label>
                        <input type="text" name="sender_address" id="" class="form-control "
                            value="<?php echo isset($sender_address) ? $sender_address : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Contact #</label>
                        <input type="text" name="sender_contact" id="" class="form-control "
                            value="<?php echo isset($sender_contact) ? $sender_contact : '' ?>">
                    </div>
                </div>
                <div class="col-md-6 d-none">
                    <b>Recipient Information</b>
                    <div class="form-group">
                        <label for="" class="control-label">Name</label>
                        <input type="text" name="recipient_name" id="" class="form-control "
                            value="<?php echo isset($recipient_name) ? $recipient_name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Address</label>
                        <input type="text" name="recipient_address" id="" class="form-control "
                            value="<?php echo isset($recipient_address) ? $recipient_address : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Contact #</label>
                        <input type="text" name="recipient_contact" id="" class="form-control "
                            value="<?php echo isset($recipient_contact) ? $recipient_contact : '' ?>">
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <div class="col-md-2">
                    <button class="btn btn-primary" type="button" id="new_parcel">Add</button>
                </div>
            </div>

            <b>Parcel Information</b>
            <table class="table table-bordered" id="parcel-items">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Kg</th>
                        <th>Total Kg</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                        <th>Bag Qty</th>
                        <th>Remark</th>
                        <?php if(!isset($id)): ?>
                        <th></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name='weight[]' class="form-control"
                                value="<?php echo isset($weight) ? $weight :'' ?>">
                        </td>
                        <td>
                            <input type="text" name='height[]' class="form-control"
                                value="<?php echo isset($height) ? $height :'' ?>">
                        </td>
                        <td>
                            <input type="text" name='length[]' class="form-control"
                                value="<?php echo isset($length) ? $length :'' ?>">
                        </td>
                        <td>
                            <input type="text" name='width[]' class="form-control"
                                value="<?php echo isset($width) ? $width :'' ?>">
                        </td>
                        <td>
                            <input type="text" class="form-control text-right number" name='price[]'
                                value="<?php echo isset($price) ? $price :'' ?>">
                        </td>
                        <td>
                            <input type="text" class="form-control text-right number" name='amount[]'
                                value="<?php echo isset($amount) ? $amount :'' ?>" >
                        </td>
                        <td>
                            <input type="text" name='bag[]' class="form-control"
                                value="<?php echo isset($bag) ? $bag :'' ?>" >
                        </td>
                        <td>
                            <input type="text" name='remark[]' class="form-control"
                                value="<?php echo isset($remark) ? $remark :'' ?>" >
                        </td>
                        <?php if(!isset($id)): ?>
                        <td>
                            <button class="btn btn-sm btn-danger" type="button"
                                onclick="$(this).closest('tr').remove() && calc()"><i class="fa fa-times"></i></button>
                        </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
                <?php if(!isset($id)): ?>
                <tfoot>
                    <th class="text-right">Total</th>
                    <th class="text-right" id="tAmount">0.00</th>
                    <th></th>
                    <th class="text-right">0.00</th>
                    <th></th>
                    <th class="text-right">0.00</th>
                    <th class="text-right">0.00</th>
                </tfoot>
                <?php endif; ?>
            </table>
            <?php if(!isset($id)): ?>

            <?php endif; ?>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-primary" form="manage-parcel">Save</button>
            <button class="btn btn-secondary" href="./index.php?page=parcel_list">Cancel</button>
        </div>
    </div>

    <div id="ptr_clone" class="d-none">
        <table>
            <tr>
                <td><input type="text" class="form-control" name='weight[]'></td>
                <td><input type="text" class="form-control" name='height[]'></td>
                <td><input type="text" class="form-control" name='length[]'></td>
                <td><input type="text" class="form-control" name='width[]'></td>
                <td><input type="text" class="form-control text-right number" name='price[]'></td>
                <td><input type="text" class="form-control text-right number" name='amount[]'></td>
                <td><input type="text" class="form-control" name='bag[]'></td>
                <td><input type="text" class="form-control" name='remark[]'></td>
                <td><button class="btn btn-sm btn-danger" type="button"
                        onclick="$(this).closest('tr').remove() && calc()"><i class="fa fa-times"></i></button></td>
            </tr>
        </table>
    </div>

</div>

<script>
$('#dtype').change(function() {
    if ($(this).prop('checked') == true) {
        $('#tbi-field').hide()
    } else {
        $('#tbi-field').show()
    }
})
$('[name="price[]"]').keyup(function() {
    calc()
})
$('#new_parcel').click(function() {
    var tr = $('#ptr_clone tr').clone()
    $('#parcel-items tbody').append(tr)
    $('[name="price[]"]').keyup(function() {
        calc()
    })
    $('.number').on('input keyup keypress', function() {
        var val = $(this).val()
        val = val.replace(/[^0-9]/, '');
        val = val.replace(/,/g, '');
        val = val > 0 ? parseFloat(val).toLocaleString("en-US") : 0;
        $(this).val(val)
    })

})
$('#manage-parcel').submit(function(e) {
    e.preventDefault()
    start_load()
    if ($('#parcel-items tbody tr').length <= 0) {
        alert_toast("Please add atleast 1 parcel information.", "error")
        end_load()
        return false;
    }
    
    $.ajax({
        url: 'ajax.php?action=save_parcel',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success: function(resp) {
            if (resp == 1) {
                alert_toast('Data successfully saved', "success");
                setTimeout(function() {
                    location.href = 'index.php?page=parcel_list';
                }, 2000)

            }
        }
    })
})

function displayImgCover(input, _this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#cover').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

function calc() {

    var total = 0;
    $('#parcel-items [name="price[]"]').each(function() {
        var p = $(this).val();
        p = p.replace(/,/g, '')
        p = p > 0 ? p : 0;
        total = parseFloat(p) + parseFloat(total)
    })
    if ($('#tAmount').length > 0)
        $('#tAmount').text(parseFloat(total).toLocaleString('en-US', {
            style: 'decimal',
            maximumFractionDigits: 2,
            minimumFractionDigits: 2
        }))
}
</script>
</div>
</div>