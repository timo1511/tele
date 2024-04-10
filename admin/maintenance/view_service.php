<?php 
require_once('./../../config.php');

// Check if 'id' is set, not empty, and is a number
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    // Sanitize the input
    $serviceId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM `services_list` WHERE id = ?");
    $stmt->bind_param("i", $serviceId); // "i" denotes the data type is integer
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $data = $result->fetch_assoc();
        foreach($data as $k => $v){
            $$k = $v; // Dynamically setting variable names
        }
    }
}
?>

   <style>
    #uni_modal .modal-footer{
        display:none;
    }
</style> 
<div class="container-fluid" id="print_out">
    <div id='transaction-printable-details' class='position-relative'>
        <div class="row">
            <fieldset class="w-100">
                <div class="col-12">
                    
                    <dl>
                        <dt class="text-info">Name:</dt>
                        <dd class="pl-3"><?php echo $name ?></dd>
                        <dt class="text-info">Description:</dt>
                        <dd class="pl-3"><?php echo isset($description) ? $description : '' ?></dd>
                        <dt class="text-info">Price:</dt>
                        <dd class="pl-3"><?php echo isset($price) ? number_format($price,2) : '' ?></dd>
                        <dt class="text-info">Status:</dt>
                        <dd class="pl-3">
                            <?php if($status == 1): ?>
                                <span class="badge badge-success rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger rounded-pill">Inactive</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-12">
        <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-dark btn-flat" type="button" id="cancel" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
    

<script>
    $(function(){
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
    })
</script>