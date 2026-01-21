<?php $location_index = ".."; include('../components/head.php');?>

<body>
    <?php include("../components/user/header.php")?>

    <main>

        <div class="dashboard-grid">

            <?php include("../components/user/nav.php")?>
            
            <!-- Main Content -->
            <div class="main-content">
                <?php include("../components/user/top-bar.php")?>
                
                <!-- Main Dashboard Content -->
                <div class="p-6">
                    <?php include("../components/user/stats-cards.php")?>

                    <div class="flex items-center mb-6">
                        <!-- Current Profile Picture -->
                        <div class="relative mr-6">
                            <img id="current-profile-picture" src="<?php echo !empty($user['pfp_user']) ? '../uploads/profiles/'.$user['pfp_user'] : $location_index . '/src/assets/images/placeholder.jpg'?>" 
                                    alt="Profile Picture" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                            
                            <!-- Change Picture Button -->
                            <button type="button" data-modal-target="profile-picture-modal" data-modal-toggle="profile-picture-modal" 
                                    class="absolute bottom-0 right-0 bg-primary-600 text-white p-2 rounded-full shadow-lg hover:bg-primary-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold"><?php echo $user['name_user']; ?></h3>
                            <p class="text-gray-600"><?php echo $user['email_user']; ?></p>
                        </div>
                    </div>
                    
                    <form action="../backend/user.php" method="POST">
                        <input type="hidden" name="token" value="<?php echo $token?>">
                        <input type="hidden" name="id_user" value="<?php echo $user['id_user']?>">

                        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <div class="sm:col-span-2">
                                <label for="email_user" class="block mb-2 text-sm font-medium text-gray-900">Email Pengguna</label>
                                <input type="text" name="email_user" id="email_user" value='<?php echo $user['email_user']?>' class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Contoh: pengguna@mail.com" required="">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="name_user" class="block mb-2 text-sm font-medium text-gray-900">Nama Pengguna</label>
                                <input type="text" name="name_user" id="name_user" value='<?php echo $user['name_user']?>' class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Contoh: Pengguna" required="">
                            </div>

                        </div>
                        <button type="submit" name='update_user' class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200">
                            Kemaskini Profile
                        </button>
                    </form>

                    <button type="button" data-modal-target="update-password-modal" data-modal-toggle="update-password-modal" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200">
                        Kemaskini Katalaluan
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile overlay -->
        <div class="overlay"></div>
    
        <div id="update-password-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Tukar Katalaluan
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="update-password-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Tutup modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->

                    <div class="p-4 md:p-5">

                        <?php 
                            if($user['type_login_user'] == 2){
                                echo '<div class="mb-4 p-4 text-sm text-primary-700 bg-primary-100 rounded-lg" role="alert">
                                        <span class="font-medium">Makluman!</span> Anda menggunakan akaun Google untuk log masuk. Sila tukar katalaluan melalui Google.
                                    </div>';
                            }
                            else{

                                ?>
                                <form class="space-y-4" action="#">
                                    <div>
                                        <label for="password_user" class="block mb-2 text-sm font-medium text-gray-900">Katalaluan Semasa</label>
                                        <input type="password" name="password_user" id="password_user" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="••••••••" required />
                                    </div>
                                    <div>
                                        <label for="new_password_user" class="block mb-2 text-sm font-medium text-gray-900">Katalaluan Baru</label>
                                        <input type="password" name="new_password_user" id="new_password_user" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required />
                                    </div>
                                    <div>
                                        <label for="new_password_confirm_user" class="block mb-2 text-sm font-medium text-gray-900">Tulis Semula Katalaluan Baru</label>
                                        <input type="password" name="new_password_confirm_user" id="new_password_confirm_user" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required />
                                    </div>
                                    <button type="submit" class="w-full text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Login to your account</button>
                                </form>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Picture Upload Modal -->
        <div id="profile-picture-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Tukar Gambar Profil
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="profile-picture-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Tutup modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5">
                        <form id="profile-picture-form" action="../backend/user.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?php echo $token?>">
                            <input type="hidden" name="id_user" value="<?php echo $user['id_user']?>">

                            <div class="flex flex-col items-center mb-4">
                                <div class="relative mb-4">
                                    <img id="image-preview" src="<?php echo !empty($user['pfp_user']) ? '../uploads/profiles/'.$user['pfp_user'] : $location_index . '/src/assets/images/placeholder.jpg' ?>" 
                                         alt="Preview" class="w-32 h-32 rounded-full object-cover border-2 border-gray-300">
                                </div>
                                
                                <div class="flex items-center justify-center w-full">
                                    <label for="profile-picture" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk memuat naik</span> atau seret dan lepaskan</p>
                                            <p class="text-xs text-gray-500">SVG, PNG, JPG atau GIF (MAX. 5MB)</p>
                                        </div>
                                        <input id="profile-picture" name="profile_picture" type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div> 
                            </div>
                            
                            <div class="flex justify-between">
                                <button type="button" data-modal-toggle="profile-picture-modal" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                                    Batal
                                </button>
                                <button id="upload-button" type="submit" name='change_pfp' class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    Muat Naik
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <?php $location_index='..'; include('../components/footer.php')?>
    
    <script>
    // Image preview functionality
    document.getElementById('profile-picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('image-preview').src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    </script>
</body>
</html>