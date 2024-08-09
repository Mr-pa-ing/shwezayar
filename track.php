<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline ">
		<div class="card-body">
			<div class="d-flex w-100 px-1 py-2 justify-content-center align-items-center">
				<label for="">Enter Tracking Number</label>
				<div class="input-group col-sm-5">
                    <input type="search" id="ref_no" class="form-control form-control-sm" placeholder="Type the tracking number here">
                    <div class="input-group-append">
                        <button type="button" id="track-btn" class="">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<div class="col-md-12 d-flex">
				<div class="col-md-6">
					<!-- <div class="callout callout-info">
						<b class="border-bottom border-primary">Sender Information</b>
						<dl>
							<dt>Name:
								<span id="sender_name"></span>
							</dt>
							<dt>Address:
								<span id="sender_address"></span>
							</dt>
							<dt>Contact:
								<span id="sender_contact"></span>
							</dt>
						</dl>
					</div> -->
					<div class="callout callout-info">
						<b class="border-bottom border-primary">Recipient Information</b>
						<dl>
							<dt>Name:
								<span id="recipient_name"></span>
							</dt>
							<dt>Address:
								<span id="recipient_address"></span>
							</dt>
							<dt>Contact:
								<span id="recipient_contact"></span>
							</dt>
						</dl>
					</div>
				</div>
				<div class="col-md-6">
					<div class="callout callout-info">
						<b class="border-bottom border-primary">Parcel Details</b>
							<div class="row">
								<div class="col-sm-6">
									<dl>
										<dt>Weight:
											<span id="weight"></span>
										</dt>
										<dt>Height:
											<span id="height"></span>
										</dt>
										<dt>Price:
											<span id="price"></span>
										</dt>
									</dl>	
								</div>
								<div class="col-sm-6">
									<dl>
										<dt>Width:
											<span id="width"></span>
										</dt>
										<dt>length:
											<span id="length"></span>
										</dt>
									</dl>	
								</div>
							</div>
					</div>
				</div>
			</div>
			<div class="timeline" id="parcel_history">
				
			</div>
		</div>
	</div>
</div>
<div id="clone_timeline-item" class="d-none">
	<div class="iitem">
	    <i class="fas fa-box bg-blue"></i>
	    <div class="timeline-item">
	      <span class="time"><i class="fas fa-clock"></i> <span class="dtime">12:05</span></span>
	      <div class="timeline-body">
	      	asdasd
	      </div>
	    </div>
	  </div>
</div>
<script>
	function track_now(){
		start_load()
		var tracking_num = $('#ref_no').val()
		if(tracking_num == ''){
			$('#parcel_history').html('')
			end_load()
		}else{
			$.ajax({
				url:'ajax.php?action=get_parcel_heistory',
				method:'POST',
				data:{ref_no:tracking_num},
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error')
					end_load()
				},
				success:function(resp){
					if(typeof resp === 'object' || Array.isArray(resp) || typeof JSON.parse(resp) === 'object'){
						resp = JSON.parse(resp)
						if(Object.keys(resp).length > 0){
							$('#parcel_history').html('')
							Object.keys(resp).map(function(k){
								var tl = $('#clone_timeline-item .iitem').clone()
								tl.find('.dtime').text(resp[k].date_created)
								tl.find('.timeline-body').text(resp[k].status)
								$('#parcel_history').append(tl)
								$('#parcel_history').append(tl)
							})
						}
						if(Object.keys(resp).length > 1){
							$('#sender_name').text(resp[1].sender_name);
							$('#sender_address').text(resp[1].sender_address);
							$('#sender_contact').text(resp[1].sender_contact);
							$('#recipient_name').text(resp[1].recipient_name);
							$('#recipient_address').text(resp[1].recipient_address);
							$('#recipient_contact').text(resp[1].recipient_contact);
							$('#weight').text(resp[1].weight);
							$('#height').text(resp[1].height);
							$('#length').text(resp[1].length);
							$('#width').text(resp[1].width);
							$('#price').text(parseFloat(resp[1].price).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' Ks');
						}
						console.log(resp[1])
					}else if(resp == 2){
						alert_toast('Unkown Tracking Number.',"error")
					}
				}
				,complete:function(){
					end_load()
				}
			})
		}
	}
	$('#track-btn').click(function(){
		track_now()
	})
	$('#ref_no').on('search',function(){
		track_now()
	})
</script>