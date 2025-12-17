<?php 
    if(!isset($_GET['id'])){header("Location:../");}
    $location_index = "../.."; include('../../components/head.php');

    $recipe_sql = $connect->prepare("SELECT r.*, u.name_user AS author_name FROM recipes r JOIN users u ON r.id_user = u.id_user WHERE r.id_recipe = ?");
    $recipe_sql->execute([$_GET['id']]);
    $recipe = $recipe_sql->fetch(PDO::FETCH_ASSOC);

    // If no recipe found, redirect
    if(!$recipe) {
        echo '<script>window.location.href = window.location.href.split("?")[0];</script>';
        exit();
    }
    
?>

<body>
    <?php include("../../components/user/header.php")?>

    <main>
        <div class="dashboard-grid">
            <?php include("../../components/user/nav.php")?>
            
            <!-- Main Content -->
            <div class="main-content">
                <?php include("../../components/user/top-bar.php")?>
                
                <?php include("../../components/user/article-recipe.php")?>
            </div>
        </div>
        
        <!-- Mobile overlay -->
        <div class="overlay"></div>
    </main>

    <?php $location_index='../..'; include('../../components/footer.php')?>
    
</body>
</html>