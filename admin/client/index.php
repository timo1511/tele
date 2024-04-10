<?php if($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo htmlspecialchars($_settings->flashdata('success')); ?>",'success');
    </script>
<?php endif;?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Patient List</h3>
        <div class="card-tools">
            <a href="<?php echo htmlspecialchars(base_url."admin?page=client/manage_client"); ?>" id="create_new" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-plus"></span>  Add New Patient</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="20%">
                    <col width="30%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Created</th>
                        <th>Image</th>
                        <th>Patient Details</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $stmt = $conn->prepare("SELECT * FROM `client_list` ORDER BY fullname ASC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) :
                        $meetingDetails = array();
                        $stmt_meta = $conn->prepare("SELECT * FROM `client_meta` WHERE client_id = ?");
                        $stmt_meta->bind_param("i", $row['id']);
                        $stmt_meta->execute();
                        $result_meta = $stmt_meta->get_result();
                        while ($meta = $result_meta->fetch_assoc()){
                            $meetingDetails[$meta['meta_field']] = $meta['meta_value'];
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($i++); ?></td>
                            <td class="text-right"><?php echo htmlspecialchars(date("Y-m-d H:i",strtotime($row['date_created']))); ?></td>
                            <td class="text-center"><img src="<?php echo htmlspecialchars(validate_image("uploads/client-".$row['id'].".png")."?v=".(isset($row['date_updated']) ? strtotime($row['date_updated']) : "")); ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
                            <td>
                                <p class="m-0">
                                    <small>
                                        <span class="text-muted">Code: </span><span><?php echo htmlspecialchars($row['client_code']); ?></span><br>
                                        <span class="text-muted">Name: </span><span><?php echo htmlspecialchars($row['fullname']); ?></span><br>
                                        <span class="text-muted">Meeting Date: </span><span><?php echo isset($meetingDetails['meeting_date']) ? htmlspecialchars($meetingDetails['meeting_date']) : 'N/A'; ?></span><br>
                                        <span class="text-muted">Meeting Time: </span><span><?php echo isset($meetingDetails['meeting_time']) ? htmlspecialchars($meetingDetails['meeting_time']) : 'N/A'; ?></span><br>
                                        <span class="text-muted">Meeting Link: </span><span><?php echo isset($meetingDetails['meeting_link']) ? "<a href='".htmlspecialchars($meetingDetails['meeting_link'])."' target='_blank'>Join Meeting</a>" : 'N/A'; ?></span><br>
                                        <span class="text-muted">Meeting Platform: </span><span><?php echo isset($meetingDetails['meeting_platform']) ? htmlspecialchars($meetingDetails['meeting_platform']) : 'N/A'; ?></span>
                                    </small>
                                </p>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success rounded-pill">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger rounded-pill">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="<?php echo htmlspecialchars(base_url."admin?page=client/view_client&id=".$row['id']); ?>" data-id ="<?php echo htmlspecialchars($row['id']); ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo htmlspecialchars(base_url."admin?page=client/manage_client&id=".$row['id']); ?>" data-id ="<?php echo htmlspecialchars($row['id']); ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this Client permanently?","delete_client",[$(this).attr('data-id')]);
        })
        $('.view_details').click(function(){
            uni_modal("Client Details","clients/view_details.php?id="+$(this).attr('data-id'));
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle');
        $('.table').dataTable();
    })

    function delete_client($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_client",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:function(err){
                console.log(err);
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
</script>
