<?php
// Check if the DBConnection class is already defined, if not, include the configuration file and the DBConnection class file
if (!class_exists('DBConnection')) {
    require_once('../config.php');
    require_once('DBConnection.php');
}

// Define the SystemSettings class which extends DBConnection
class SystemSettings extends DBConnection {
    // Constructor function to initialize the database connection
    public function __construct() {
        parent::__construct();
    }

    // Function to check the database connection
    function check_connection() {
        return ($this->conn);
    }

    // Function to load system information from the database into the session
    function load_system_info() {
        // Retrieve system information from the database and store it in the session
        $sql = "SELECT * FROM system_info";
        $qry = $this->conn->query($sql);
        while ($row = $qry->fetch_assoc()) {
            $_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
        }
    }

    // Function to update system information from the database into the session
    function update_system_info() {
        // Retrieve system information from the database and update the session
        $sql = "SELECT * FROM system_info";
        $qry = $this->conn->query($sql);
        while ($row = $qry->fetch_assoc()) {
            if (isset($_SESSION['system_info'][$row['meta_field']])) {
                unset($_SESSION['system_info'][$row['meta_field']]);
            }
            $_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
        }
        return true;
    }

    // Function to update system settings based on POST data
    function update_settings_info() {
        // Iterate through POST data and update system settings accordingly
        foreach ($_POST as $key => $value) {
            // Exclude 'content' field from updating
            if (!in_array($key, array("content"))) {
                // Sanitize value and prepare SQL query
                $value = str_replace("'", "&apos;", $value);
                $qry = $this->conn->query("UPDATE system_info SET meta_value = '{$value}' WHERE meta_field = '{$key}'");
                if (!$qry) {
                    // Handle query error
                }
            }
        }

        // Handle file updates for content items
        //if (isset($_POST['content'])) {
        //    foreach ($_POST['content'] as $k => $v) {
        //        file_put_contents("../{$k}.html", $v);
        //   }
        //}

        // Handle file updates for content items
			if (isset($_POST['content'])) {
			    foreach ($_POST['content'] as $k => $v) {
			        // Sanitize the filename to remove any path information
			        $safeFileName = basename($k);
			        
			        // Append .html to ensure the file is an HTML file
			        $safeFilePath = "../" . $safeFileName . ".html";
			        
			        // Proceed to write the sanitized content to the sanitized file path
			        file_put_contents($safeFilePath, $v);
			    }
			}

        // Handle image uploads for logo
        if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
            // Define file name and directory path
            $fname = 'uploads/logo-' . time() . '.png';
            $dir_path = base_app . $fname;
            $upload = $_FILES['img']['tmp_name'];
            $type = mime_content_type($upload);
            $allowed = array('image/png', 'image/jpeg');
            if (!in_array($type, $allowed)) {
                $resp['msg'] .= " But Image failed to upload due to invalid file type.";
            } else {
                // Define new image dimensions
                $new_height = 200;
                $new_width = 200;

                // Create a resized image
                list($width, $height) = getimagesize($upload);
                $t_image = imagecreatetruecolor($new_width, $new_height);
                imagealphablending($t_image, false);
                imagesavealpha($t_image, true);
                $gdImg = ($type == 'image/png') ? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                if ($gdImg) {
                    // Save the resized image
                    if (is_file($dir_path))
                        unlink($dir_path);
                    $uploaded_img = imagepng($t_image, $dir_path);
                    imagedestroy($gdImg);
                    imagedestroy($t_image);
                } else {
                    $resp['msg'] .= " But Image failed to upload due to unknown reason.";
                }
            }
            if (isset($uploaded_img) && $uploaded_img == true) {
                // Update system information in the database
                if (isset($_SESSION['system_info']['logo'])) {
                    $qry = $this->conn->query("UPDATE system_info SET meta_value = '{$fname}' WHERE meta_field = 'logo'");
                    if (is_file(base_app . $_SESSION['system_info']['logo'])) unlink(base_app . $_SESSION['system_info']['logo']);
                } else {
                    $qry = $this->conn->query("INSERT INTO system_info SET meta_value = '{$fname}', meta_field = 'logo'");
                }
                unset($uploaded_img);
            }
        }

        // Handle image uploads for cover
        if (isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != '') {
            // Define file name and directory path
            $fname = 'uploads/cover-' . time() . '.png';
            $dir_path = base_app . $fname;
            $upload = $_FILES['cover']['tmp_name'];
            $type = mime_content_type($upload);
            $allowed = array('image/png', 'image/jpeg');
            if (!in_array($type, $allowed)) {
                $resp['msg'] .= " But Image failed to upload due to invalid file type.";
            } else {
                // Define new image dimensions
                $new_height = 720;
                $new_width = 1280;

                // Create a resized image
                list($width, $height) = getimagesize($upload);
                $t_image = imagecreatetruecolor($new_width, $new_height);
                $gdImg = ($type == 'image/png') ? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                if ($gdImg) {
                    // Save the resized image
                    if (is_file($dir_path))
                        unlink($dir_path);
                    $uploaded_img = imagepng($t_image, $dir_path);
                    imagedestroy($gdImg);
                    imagedestroy($t_image);
                } else {
                    $resp['msg'] .= " But Image failed to upload due to unknown reason.";
                }
            }
            if (isset($uploaded_img) && $uploaded_img == true) {
                // Update system information in the database
                if (isset($_SESSION['system_info']['cover'])) {
                    $qry = $this->conn->query("UPDATE system_info SET meta_value = '{$fname}' WHERE meta_field = 'cover'");
                    if (is_file(base_app . $_SESSION['system_info']['cover'])) unlink(base_app . $_SESSION['system_info']['cover']);
                } else {
                    $qry = $this->conn->query("INSERT INTO system_info SET meta_value = '{$fname}', meta_field = 'cover'");
                }
                unset($uploaded_img);
            }
        }

        // Update system information in the session
        $update = $this->update_system_info();
        // Set flash data
        $flash = $this->set_flashdata('success', 'System Info Successfully Updated.');
        if ($update && $flash) {
            return true;
        }
    }

    // Function to set user data in the session
    function set_userdata($field = '', $value = '') {
        if (!empty($field) && !empty($value)) {
            $_SESSION['userdata'][$field] = $value;
        }
    }

    // Function to get user data from the session
    function userdata($field = '') {
        if (!empty($field)) {
            if (isset($_SESSION['userdata'][$field]))
                return $_SESSION['userdata'][$field];
            else
                return null;
        } else {
            return false;
        }
    }

    // Function to set flash data in the session
    function set_flashdata($flash = '', $value = '') {
        if (!empty($flash) && !empty($value)) {
            $_SESSION['flashdata'][$flash] = $value;
            return true;
        }
    }

    // Function to check if flash data exists in the session
    function chk_flashdata($flash = '') {
        if (isset($_SESSION['flashdata'][$flash])) {
            return true;
        } else {
            return false;
        }
    }

    // Function to get flash data from the session
    function flashdata($flash = '') {
        if (!empty($flash)) {
            $_tmp = $_SESSION['flashdata'][$flash];
            unset($_SESSION['flashdata']);
            return $_tmp;
        } else {
            return false;
        }
    }

    // Function to destroy session data
    function sess_des() {
        if (isset($_SESSION['userdata'])) {
            unset($_SESSION['userdata']);
            return true;
        }
        return true;
    }

    // Function to get system information from the session
    function info($field = '') {
        if (!empty($field)) {
            if (isset($_SESSION['system_info'][$field]))
                return $_SESSION['system_info'][$field];
            else
                return false;
        } else {
            return false;
        }
    }

    // Function to set system information in the session
    function set_info($field = '', $value = '') {
        if (!empty($field) && !empty($value)) {
            $_SESSION['system_info'][$field] = $value;
        }
    }
}

// Instantiate SystemSettings object and load system information into the session
$_settings = new SystemSettings();
$_settings->load_system_info();

// Determine the action based on the 'f' parameter in the URL
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);

// Create a new SystemSettings object
$sysset = new SystemSettings();

// Perform actions based on the requested action
switch ($action) {
    case 'update_settings':
        // Update system settings and output the result
        echo $sysset->update_settings_info();
        break;
    default:
        // Handle default action or no action
        // echo $sysset->index();
        break;
}
?>
