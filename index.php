<?php 
require_once('./config.php');
session_start();
if(isset($_SESSION['userdata']) && !empty($_SESSION['userdata']['client_code'])) {
    require_once('inc/header.php');
    $client_code = $_SESSION['userdata']['client_code'];

    // Assuming $conn is your database connection variable from config.php
    $clientInfoQuery = $conn->query("SELECT * FROM client_list WHERE client_code = '$client_code'");
    if($clientInfo = $clientInfoQuery->fetch_assoc()){
        $client_id = $clientInfo['id']; // Fetch client ID for meta information

        $clientMetaQuery = $conn->query("SELECT meta_field, meta_value FROM client_meta WHERE client_id = '$client_id'");
        $clientMeta = [];
        while($meta = $clientMetaQuery->fetch_assoc()){
            $clientMeta[$meta['meta_field']] = $meta['meta_value'];
        }
    } else {
        // Handle case where client_code does not match any records
        echo "<script>alert('Client not found.'); location.href='logout.php';</script>";
        exit;
    }
} else {
    // Redirect to login page if client_code is not set in the session
    echo "<script>location.href='login.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <?php require_once('inc/header.php'); ?>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed sidebar-mini-md sidebar-mini-xs" style="height: auto;">
    <div class="wrapper">
        <?php require_once('inc/topBarNav.php'); ?>
        <?php require_once('inc/navigation.php'); ?>
        <div class="content-wrapper pt-3" style="min-height: 567.854px;">
            <section class="content">
                <div class="container-fluid">
                    <!-- Welcome Message -->
<div class="alert alert-success" role="alert">
    Welcome to KH Telemedicine Platform!
</div>

<!-- Client Information Card -->
<div class="card">
    <div class="card-header">
        Client Information
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Client Code:</strong> <?php echo htmlspecialchars($client_code); ?></div>
            <?php foreach($clientMeta as $field => $value): ?>
            <div class="col-md-4"><strong><?php echo htmlspecialchars(ucfirst($field)); ?>:</strong> <?php echo htmlspecialchars($value); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$servicesQuery = $conn->query("SELECT id, name, description, price FROM services_list WHERE status = 1");
?>
<!-- Services List -->
<div class="card mt-4">
    <div class="card-header">
        Available Services
    </div>
    <div class="card-body">
        <div class="row">
            <?php while($service = $servicesQuery->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                        <p class="card-text"><strong>Price:</strong> <?php echo htmlspecialchars($service['price']); ?></p>
                        <form action="process_service_selection.php" method="post">
                          <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                          <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($client_id); ?>">
                          <input type="hidden" name="service_price" value="<?php echo htmlspecialchars($service['price']); ?>">
                          <button type="submit" class="btn btn-primary">Select Service</button>
                      </form>



                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

                </div>
            </section>
        </div>
        <?php require_once('inc/footer.php'); ?>
    </div>
</body>
</html>
