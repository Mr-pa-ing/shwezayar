<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline ">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-primary rounded-0" href="./index.php?page=new_parcel" style="border-radius: 5px !important;">Add</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover" id="list">
				
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Reference Number</th>
						<th>Name</th>
						<th>Description</th>
						<th>Qty</th>
						<th>Kg</th>
						<th>Total Kg</th>
						<th>Unit Price</th>
						<th>Amount</th>
						<th>Bag Qty</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$where = "";
					if(isset($_GET['s'])){
						$where = " where status = {$_GET['s']} ";
					}
					if($_SESSION['login_type'] != 1 ){
						if(empty($where))
							$where = " where ";
						else
							$where .= " and ";
						$where .= " (from_branch_id = {$_SESSION['login_branch_id']} or to_branch_id = {$_SESSION['login_branch_id']}) ";
					}
					$qry = $conn->query("SELECT * from parcels $where order by  unix_timestamp(date_created) desc ");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td><?php echo ($row['reference_number']) ?></td>
						<td><?php echo ucwords($row['sender_name']) ?></td>
						<td><?php echo ucwords($row['weight']) ?></td>
						<td><?php echo ucwords($row['height']) ?></td>
						<td><?php echo ucwords($row['length']) ?></td>
						<td><?php echo ucwords($row['width']) ?></td>
						<td><?php echo number_format($row['price'],0) ?></td>
						<td><?php echo number_format($row['amount'],0) ?></td>
						<td><?php echo ucwords($row['bag']) ?></td>
						<!-- <td class="text-center">
							<?php 
							switch ($row['status']) {
								case '1':
									echo "<span class='badge badge-pill badge-primary'>Collected</span>";
									break;
								case '2':
									echo "<span class='badge badge-pill badge-secondary'> Shipped</span>";
									break;
								default:
									echo "<span class='badge badge-pill badge-warning'> Item Accepted</span>";
									
									break;
							}

							?>
						</td> -->
						<td class="text-center">
		                    <div class="btn-group">
		                    	<a  class="view view_parcel" data-id="<?php echo $row['id'] ?>" style="cursor: pointer">
		                          <i class="fas fa-eye"></i>
		                        </a>
		                        <a href="index.php?page=edit_parcel&id=<?php echo $row['id'] ?>" class="edit">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <a type="button" class="delete  delete_parcel" data-id="<?php echo $row['id'] ?>">
		                          <i class="fas fa-trash"></i>
		                        </a>
	                      </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table td{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
		$('.view_parcel').click(function(){
			uni_modal("Parcel's Details","view_parcel.php?id="+$(this).attr('data-id'),"large")
		})
	$('.delete_parcel').click(function(){
	_conf("Are you sure to delete this parcel?","delete_parcel",[$(this).attr('data-id')])
	})
	})
	function delete_parcel($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_parcel',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>