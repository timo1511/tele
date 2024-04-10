<?php

// Include the configuration file
require_once '../config.php';

// Start the HTML document
?>
<!DOCTYPE html>
<html lang="en">
<?php
// Include the header file
require_once 'inc/header.php';
?>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed" style="height: auto;">
    <div class="wrapper">
        <?php
        // Include the top bar navigation
        require_once 'inc/topBarNav.php';
        ?>
        <?php
        // Include the navigation
        require_once 'inc/navigation.php';
        ?>
        <?php
        // Check if there's a success flash message
        if ($_settings->chk_flashdata('success')) :
        ?>
            <script>
                // Display a toast message with the success flash message
                alert_toast("<?php echo htmlspecialchars($_settings->flashdata('success'), ENT_QUOTES); ?>", 'success');
            </script>
        <?php endif; ?>
        <?php
        // Determine the page to be included based on the 'page' parameter in the URL
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        ?>
        <div class="content-wrapper pt-3" style="min-height: 567.854px;">
            <section class="content">
                <div class="container-fluid">
                    <?php
                    // Include the requested page or display a 404 error if the page doesn't exist
                    if (!file_exists($page . ".php") && !is_dir($page)) {
                        include '404.html';
                    } else {
                        if (is_dir($page))
                            include $page . '/index.php';
                        else
                            include $page . '.php';
                    }
                    ?>
                </div>
            </section>
            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirm_modal" role='dialog'>
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                        </div>
                        <div class="modal-body">
                            <div id="delete_content"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Other Modals -->
            <div class="modal fade" id="uni_modal" role='dialog'>
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add more modals here -->
        </div>
        <?php
        // Include the footer
        require_once 'inc/footer.php';
        ?>
    </div>
</body>

</html>
