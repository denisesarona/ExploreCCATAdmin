<?php 
    session_start();
    include('includes/header.php');
?>

<link rel="stylesheet" href="assets/css/addFacultyMember.css">

<section class="dashboard">
    <div class="register-container">
        <div class="title">
            <i class='bx bx-group'></i>
            <span class="text">FACULTY MEMBERS</span>
        </div>
        <form class="AdminReg">
                
        <div class="form-row">
            <div class="form-group">
                <label for="department" class="form-label">Department</label>
                    <select id="department" name="department" class="form-control" placeholder="Choose depeartment">
                        <option value="" >DCS</option>
                        <option value="" >DMS</option>
                        <option value="" >DTE</option>
                        <option value="" >DIT</option>
                        <option value="" >DE</option>
                    </select>
                </div>
            </div>
        
            <div class="form-row">
                <div class="form-group">
                    <label for="firstn" class="form-label">Name</label>
                    <input type="text" name="facultyname" class="form-control" id="facultyname" placeholder="Enter faculty name">
                </div>
                <div class="form-group">
                    <label for="em" class="form-label">Position</label>
                    <input type="text" name="Position" class="form-control" id="Position" placeholder="Enter faculty position">
                </div>
            </div>

            <div class="form-group">
                <label for="image">Upload Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
        
            <button type="submit" class="btn">SAVE</button>
        </form>
    </div>
</section>
