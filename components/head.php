<?php 
    require_once("$location_index/config/connect.php");
    session_start();

    include "$location_index/backend/functions/csrf-token.php";
    $token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $location_index?>/src/assets/css/output.css">
    <!-- <link rel="stylesheet" href="<?php echo $location_index?>/node_modules/flowbite/dist/flowbite.min.css"> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" /> -->
    <link rel="shortcut icon" href="<?php echo $location_index?>/src/assets/images/logo/favicon/favicon.ico" type="image/x-icon">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->

    <!-- <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script> -->
    <!-- for chart -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

    <!-- for datatable -->
    <!-- <link rel="stylesheet" href="https://czdn.datatables.net/2.1.4/css/dataTables.dataTables.css" /> -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> -->
    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
    <!-- <script src="https://cdn.tailwindcss.com/3.3.0"></script> -->

    <script src="<?php echo $location_index?>/src/assets/js/selectoption.js"></script>

    <style>
        @media (prefers-color-scheme: light) {
            input{
              color: black !important;
            }
        }

        [class^="select-dropdown-container-"] {
            background-color: white !important; /* Example style */
        }

        .hero-pattern {
            background: radial-gradient(circle, rgba(255,255,255,0) 0%, rgba(240,253,244,1) 100%);
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #16a34a;
            transform: translateY(-2px);
        }
        .btn-primary {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .hero-img {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .recipe-card {
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .recipe-card img {
            transition: transform 0.5s ease;
        }
        
        .recipe-card:hover img {
            transform: scale(1.05);
        }
        
        .feature-icon {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: rotate(10deg) scale(1.1);
            background-color: #fef3c7;
        }
        
        .floating {
            animation: floating 8s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
    </style>
    <title>
        <?php
            if(isset($title)){echo $title;}
            else{echo $webname;}
        ?>
    </title>
    <link rel="manifest" href="<?php echo $location_index?>/manifest.json">

</head>

<?php 

    function formatImagePath($input, $locationRef) {
        // If input is null, empty, or whitespace only, return default placeholder
        if (is_null($input) || trim($input) === '') {
            return rtrim($locationRef, '/') . '/src/assets/images/recipe_placeholder.png';
        }

        // Check if the string is already a full URL (starts with http:// or https://)
        if (preg_match('/^https?:\/\//i', $input)) {
            return $input;
        }

        // Check if the string is already a relative path with directories
        if (strpos($input, '/') !== false) {
            return $input;
        }

        // If it's just a filename, add the location reference and appropriate path
        return rtrim($locationRef, '/') . '/uploads/recipes/' . ltrim($input, '/');
    }

?>