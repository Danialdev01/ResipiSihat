<!-- Side Navigation -->
<div class="sidenav bg-white shadow-sm z-10">
    <div style='padding:1.1rem' class="border-b">
        <div class="flex items-center">
            <!-- <i class="fas fa-utensils text-primary-600 text-3xl mr-2"></i> -->
            <!-- <span class="text-xl font-bold text-gray-900">Resepi<span class="text-primary-600">Sihat</span></span> -->
            <img class="h-10" src="<?php echo $location_index?>/src/assets/images/logo/logo-banner.png" alt="logo">
        </div>
    </div>

    <div class="p-4 border-b">
        <div class="flex items-center space-x-3">
            <a class='flex items-center space-x-3' href="<?php echo $location_index?>/user/akaun.php">
                <div class="relative">

                    <img src="<?php echo !empty($user['pfp_user']) ? $location_index .'/uploads/profiles/'.$user['pfp_user'] : 'https://avatar.iran.liara.run/username?username=' . $user['name_user'] ; ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover border-2 border-primary-200">
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                <div>
                    <div class="font-bold text-gray-900"><?php echo getFirstName($user['name_user'])?></div>
                    <div class="text-sm text-gray-500">Account Percuma</div>
                </div>
            </a>
        </div>
    </div>

    <?php 

        function setActive($file_name){
            if(basename($_SERVER['PHP_SELF']) == $file_name){
                echo 'active';
            }
        }

    ?>
    
    <div class="py-4">
        <div class="px-2 text-xs uppercase text-gray-500 font-semibold mb-2 pl-5">Menu Utama</div>
        <a href="<?php echo $location_index?>/user/" class="<?php setActive('index.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-home text-gray-500 mr-3 w-5 text-center"></i>
            Dashboard
        </a>
        <a href="<?php echo $location_index?>/user/resepi/saya.php" class="<?php setActive('saya.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-book text-gray-500 mr-3 w-5 text-center"></i>
            Resepi Saya
        </a>
        <a href="<?php echo $location_index?>/user/chat.php" class="<?php setActive('chat.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-robot text-gray-500 mr-3 w-5 text-center"></i>
            Nasihat Pemakanan
        </a>
        <a href="<?php echo $location_index?>/user/pembelian/" class="<?php setActive('pembelian.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-shopping-cart text-gray-500 mr-3 w-5 text-center"></i>
            Senarai Belian
        </a>
        <a href="<?php echo $location_index?>/user/resepi/komuniti.php" class="<?php setActive('komuniti.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-users text-gray-500 mr-3 w-5 text-center"></i>
            Komuniti
        </a>
        <a href="<?php echo $location_index?>/user/simpanan.php" class="<?php setActive('simpanan.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-bookmark text-gray-500 mr-3 w-5 text-center"></i>
            Simpanan
        </a>
        
        <div class="px-2 text-xs uppercase text-gray-500 font-semibold mb-2 mt-6 pl-5">Tetapan</div>
        <a href="<?php echo $location_index?>/user/akaun.php" class="<?php setActive('akaun.php'); ?> nav-link flex items-center py-3 px-5 text-gray-700">
            <i class="fas fa-cog text-gray-500 mr-3 w-5 text-center"></i>
            Tetapan Akaun
        </a>
        <form clas="w-full" action="<?php echo $location_index?>/backend/user.php" method="post">
            <input type="hidden" name="token" value="<?php echo $token?>">
            
            <button type="submit" name="signout" class="w-full nav-link flex items-center py-3 px-5 text-gray-700">
                <i class="fas fa-sign-out-alt text-gray-500 mr-3 w-5 text-center"></i>
                Log Keluar
            </button>
        </form>
    </div>
    
</div>