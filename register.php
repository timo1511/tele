<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);



// Define base URL
define('base_url', 'http://localhost/cms/');


?>
<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: scale-down;
        object-position: center center;
        border-radius: 100% 100%;
    }

    /* General styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        margin-top: 20px;
    }

    .card {
        margin-bottom: 20px;
        border: none;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #f0f0f0;
        border-bottom: 1px solid #ddd;
    }

    .card-title {
        font-size: 1.25rem;
        margin-bottom: 0;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: 500;
        color: #333;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        outline: none;
    }

    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        resize: vertical;
        outline: none;
    }

    .custom-file-label {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        cursor: pointer;
    }

    /* Image styles */
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: scale-down;
        object-position: center center;
        border-radius: 100% 100%;
    }

    /* Button styles */
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-dark {
        background-color: #343a40;
        color: #fff;
    }

    .btn-dark:hover {
        background-color: #1d2124;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .card {
            margin-left: 10px;
            margin-right: 10px;
        }
    }

    .card-footer button,
    .card-footer a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .card-header {
        background-color: #f0f0f0;
        border-bottom: 1px solid #ddd;
        text-align: center; /* Center the content horizontally */
    }

    .card-footer {
        text-align: center; /* Center the content horizontally */
    }

    .card-footer button,
    .card-footer a {
        margin: 0 5px; /* Adjust spacing between buttons */
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header text-center">
        <h5 class="card-title">Register</h5>
    </div>

    <div class="card-body">
        <div class="container-fluid">
            <form action="process_registration.php" id="client-form" method="post" enctype="multipart/form-data">
                <div class="col-md-12">
                    <fieldset class="border-bottom border-info">
                        <legend class="text-info">Personal Information</legend>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="lastname" class="control-label text-info">Last Name</label>
                                <input type="text" class="form-control form-control-sm rounded-0" id="lastname" name="lastname" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="firstname" class="control-label text-info">First Name</label>
                                <input type="text" class="form-control form-control-sm rounded-0" id="firstname" name="firstname" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="middlename" class="control-label text-info">Middle Name</label>
                                <input type="text" class="form-control form-control-sm rounded-0" id="middlename" name="middlename" placeholder="optional">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="gender" class="control-label text-info">Gender</label>
                                <select name="gender" id="gender" class="custom-select custom-select-sm rounded-0" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="dob" class="control-label text-info">Date of Birth</label>
                                <input type="date" class="form-control form-control-sm rounded-0" id="dob" name="dob" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="contact" class="control-label text-info">Contact #</label>
                                <input type="text" class="form-control form-control-sm rounded-0" id="contact" name="contact" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label for="address" class="control-label text-info">Address</label>
                                <textarea type="text" class="form-control form-control-sm rounded-0" id="address" name="address" required placeholder="Street, Apartment Unit #/Building, City, State/Province, ZIP Code"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="email" class="control-label text-info">Email</label>
                                <input type="email" class="form-control form-control-sm rounded-0" id="email" name="email" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="password" class="control-label text-info">Password</label>
                                <input type="password" class="form-control form-control-sm rounded-0" id="password" name="password" required>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="border-bottom border-info">
                        <legend class="text-info">Client Image</legend>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="custom-file rounded-0">
                                    <input type="file" class="custom-file-input rounded-0" id="avatar" name="avatar" onchange="displayImg(this,$(this))">
                                    <label class="custom-file-label rounded-0" for="avatar">Choose file</label>
                                </div>
                            </div>
                            <div class="form-group col-sm-4 text-center">
                                <img src="" alt="" id="cimg" class="img-fluid img-thumbnail">
                            </div>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer text-center">
        <button class="btn btn-flat btn-sn btn-primary" type="submit" form="client-form">Register</button>
        <a class="btn btn-flat btn-sn btn-dark" href="<?php echo base_url . "login" ?>">Back to Login</a>
    </div>
</div>

