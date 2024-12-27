<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Campus Navigation Kiosk</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <script src="../orgChart/orgchart.js"></script>
    <!-- Alertify JS -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
    <style>
    .BlueBtn{
    color: white; 
    font-size: 13px; 
    font-family: 'Poppins';
    font-size: 500;
    background-color: #598D66;
    }
    .BlueBtn:hover{
        background-color: #064918 !important;
        color: white; 
    }
    
    .RedBtn{
        color: white; 
        font-size: 13px; 
        font-family: 'Poppins';
        font-size: 500;
        background-color: #f43737dd;
    }
    .RedBtn:hover{
        background-color: #f90000;
        color: white; 
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main p-1">