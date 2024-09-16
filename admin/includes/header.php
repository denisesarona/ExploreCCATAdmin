<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> 
    <title>Header</title>
    <link rel="stylesheet" href="admin/assets/js/sidebar.js">
    <style>
        /* ===== Google Font Import - Poppins ===== */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap');

/* Global settings */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

:root {
    --primary-color: #064918;
    --panel-color: #FFF;
    --text-color: #000;
    --border-color: #e6e5e5;
    --toggle-color: #DDD;
    --box1-color: #4DA3FF;
    --box2-color: #FFE6AC;
    --box3-color: #E7D1FC;
    --title-icon-color: #fff;
    --tran-05: all 0.5s ease;
}

body {
    min-height: 100vh;
    background-color: var(--primary-color);
    display: flex;
}

body.dark {
    --primary-color: #3A3B3C;
    --panel-color: #242526;
    --text-color: #CCC;
    --border-color: #4D4C4C;
    --toggle-color: #FFF;
    --box1-color: #3A3B3C;
    --box2-color: #3A3B3C;
    --box3-color: #3A3B3C;
    --title-icon-color: #064918;
}

/* Ensure full height for both navbar and dashboard */
nav, .dashboard {
    height: 100vh;
}

/* Container Settings */
.container {
    display: flex;
    height: 100vh;
}

/* Navbar Styling */
nav {
    width: 350px;
    background-color: var(--primary-color);
    padding: 10px;
    transition: var(--tran-05);
    box-sizing: border-box;
}

nav.close {
    width: 73px;
}

nav .logo-name {
    margin-top: 10px;
    display: flex;
    align-items: center;
}

nav .logo_name {
    font-size: 30px;
    font-weight: 600;
    color: #fff;
    margin-left: 60px;
    margin-bottom: 35px;
    transition: var(--tran-05);
}

nav.close .logo_name {
    opacity: 0;
    pointer-events: none;
}

.menu-items li a .link-name {
    font-size: 15px;
    color: #064918;
    white-space: nowrap;
}

/* Adjust font size for smaller screens */
@media (max-width: 1000px) {
    .menu-items li a .link-name {
        font-size: 14px;
    }
}

@media (max-width: 768px) {
    .menu-items li a .link-name {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .menu-items li a .link-name {
        font-size: 10px;
    }
}

@media (max-width: 400px) {
    .menu-items li a .link-name {
        font-size: 8px;
    }
}

nav.close li a .link-name {
    opacity: 0;
    pointer-events: none;
}

/* Top dashboard bar */
.dashboard .top {
    position: fixed;
    top: 0;
    left: 350px;
    width: calc(100% - 350px);
    background-color: var(--panel-color);
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 10;
    transition: var(--tran-05);
}

nav.close ~ .top {
    left: 73px;
    width: calc(100% - 73px);
}

.top .sidebar-toggle {
    font-size: 26px;
    color: #fff;
    cursor: pointer;
}

/* Menu Styling */
.menu-items ul {
    list-style: none;
}

.menu-items li a {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 2px solid transparent;
    margin-bottom: 15px;
    border-radius: 5px;
    background-color: #fff;
    text-decoration: none;
    transition: var(--tran-05);
    width: 100%;
}

.menu-items li a i {
    margin-right: 10px;
    color: #064918;
}

.menu-items li a:hover {
    background-color: rgba(6, 252, 14, 0.1);
    border-color: #3a8d3b;
}

/* Logout Button Styling */
.menu-items .btn {
    width: 100%;
    padding: 10px 0;
    background-color: #fff;
    color: #064918;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
    margin-top: 270px;
}

.menu-items .btn:hover {
    background-color: #e0e0e0;
}

/* Responsive adjustments for .btn */
@media (max-width: 768px) {
    .menu-items .btn {
        font-size: 14px;
        padding: 8px 0;
    }
}

@media (max-width: 480px) {
    .menu-items .btn {
        font-size: 12px;
        padding: 6px 0;
    }
}

/* Responsive Navbar adjustments */
@media (max-width: 1000px) {
    nav {
        width: 250px;
    }
    nav.close {
        width: 73px;
    }
}

@media (max-width: 768px) {
    nav {
        width: 200px;
    }
}

@media (max-width: 480px) {
    nav {
        width: 150px;
    }
}

@media (max-width: 400px) {
    nav {
        width: 0px;
    }
    nav.close {
        width: 73px;
    }
}
    </style>

</head>
<body>
    <?php include('sidebar.php');?>

