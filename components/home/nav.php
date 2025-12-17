<?php

    if(isset($_SESSION[$token_name]) || isset($_COOKIE[$token_name])){

        include("./backend/functions/system.php");
        include("./backend/functions/user.php");
        include("./backend/models/user.php");

        $verify = verifySessionUser($token_name, $secret_key, $connect);

        $verify = json_decode($verify, true);

        if($verify['status'] == "success"){

            $user_value = decryptUser($_SESSION[$token_name], $secret_key);
            $id_user = $user_value['id_user'];

            $user_sql = $connect->prepare("SELECT * FROM users WHERE id_user = :id_user");
            $user_sql->execute([
                ":id_user" => $id_user
            ]);
            $user = $user_sql->fetch(PDO::FETCH_ASSOC);

            if($user['status_user'] == 2){
                // header("Location:./user/");
                $_SESSION[$token_name . "type"] = "admin";
            }

            elseif($user['status_user'] == 1){
                // header("Location:./guest/");
                $_SESSION[$token_name . "type"] = "user";
            }
        }
    }

?>

<style>
    .featured-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(to right, #f59e0b, #d97706);
        color: white;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<nav class="bg-white shadow-md fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-10" src="<?php echo $location_index?>/src/assets/images/logo/logo-banner.png" alt="logo">
                </div>
                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <a href="<?php echo $location_index?>/" class="nav-link text-gray-500 hover:text-primary-600 px-3 py-2 font-medium">Utama</a>
                    <a href="<?php echo $location_index?>/resipi-terkini.php" class="nav-link text-gray-500 hover:text-primary-600 px-3 py-2 font-medium">Resipi Terkini</a>
                    <a href="<?php echo $location_index?>/tentang-kami.php" class="nav-link text-gray-500 hover:text-primary-600 px-3 py-2 font-medium">Tentang Kami</a>
                </div>
            </div>
            <div class="flex items-center">
                <button data-modal-target="signin-modal" data-modal-toggle="signin-modal" class="hidden md:block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg font-medium transition btn-primary">
                    Log Masuk
                </button>
                <button id="mobile-menu-button" class="md:hidden ml-4 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div id="mobile-menu" class="md:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white shadow-lg">
            <a href="<?php echo $location_index?>/" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-primary-600">Utama</a>
            <a href="<?php echo $location_index?>/resipi-terkini.php" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-primary-600">Resipi Terkini</a>
            <a href="<?php echo $location_index?>/tentang-kami.php" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-primary-600">Tentang Kami</a>
        </div>
    </div>
</nav>

 <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>

<?php include  $location_index ."/components/alert.php";?>
<?php include  $location_index ."/components/home/modals.php";?>