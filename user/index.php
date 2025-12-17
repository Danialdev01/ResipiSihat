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
                
                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2">
                        <!-- Meal Plan -->
                        <div class="card bg-white rounded-xl p-6 mb-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-900">Terkini</h3>
                                <a href="<?php echo $location_index?>/user/resipi/komuniti.php">
                                    <button class="text-primary-600 hover:text-primary-800 font-medium">
                                        Lihat <i class="fas fa-arrow-right ml-1"></i>
                                    </button>
                                </a>
                            </div>

                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <?php 

                                    $resepi_terkini_sql = $connect->prepare("SELECT * FROM recipes WHERE id_user = :id_user AND status_recipe = 1 ORDER BY created_date_recipe LIMIT 3");
                                    $resepi_terkini_sql->execute([
                                        ":id_user" => $user['id_user']
                                    ]);

                                    while($resepi_terkini = $resepi_terkini_sql->fetch(PDO::FETCH_ASSOC)){
                                        ?>

                                        <a href="./resipi/?id=<?php echo $resepi_terkini['id_recipe']?>" class="meal-card bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-lg transition-shadow duration-300">

                                            <div class="h-32 relative">
                                                <img src="<?php echo htmlspecialchars(formatImagePath($resepi_terkini['image_recipe'], "../"))?>" 
                                                    alt="<?php echo htmlspecialchars($resepi_terkini['name_recipe'] ?? '')?>" 
                                                    class="w-full h-full object-cover">
                                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                                    <div class="text-white font-semibold"><?php echo htmlspecialchars($resepi_terkini['category_recipe'] ?? '')?></div>
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <div class="font-bold mb-1"><?php echo htmlspecialchars($resepi_terkini['name_recipe'] ?? '')?></div>
                                                <div class="text-sm text-gray-600 flex justify-between">
                                                    <span><i class="fas fa-clock mr-1"></i><?php echo htmlspecialchars($resepi_terkini['cooking_time_recipe'] ?? '')?></span>
                                                    <span><i class="fas fa-fire mr-1"></i><?php echo htmlspecialchars($resepi_terkini['calories_recipe'] ?? '')?></span>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- <div data-modal-target="resipi-modal-<?php echo $resepi_terkini['id_recipe']?>" data-modal-toggle="resipi-modal-<?php echo $resepi_terkini['id_recipe']?>" class="meal-card bg-gray-50 rounded-lg overflow-hidden"> -->
                                        <!-- </div> -->
                                        
                                        <?php
                                    }

                                ?>
                            </div>

                            <br>
                            <a href="./resipi/baru.php">
                                <button type="button" class="text-white bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2">
                                    <svg class="w-4.5 h-4.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                                    </svg>
    
                                    Resipi Baru
                                </button>
                            </a>
                        </div>
                        
                        <!-- Recent Activity
                        <div class="card bg-white rounded-xl p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Aktiviti Terkini</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="bg-primary-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-book text-primary-700"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">Anda menambah resipi baru</div>
                                        <div class="text-gray-600">Salad Ayam Madu</div>
                                        <div class="text-sm text-gray-500 mt-1">Hari ini, 10:24 AM</div>
                                    </div>
                                    <span class="badge badge-primary">Resipi</span>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-green-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-shopping-cart text-green-700"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">Anda menyelesaikan senarai belian</div>
                                        <div class="text-gray-600">Senarai belian mingguan</div>
                                        <div class="text-sm text-gray-500 mt-1">Semalam, 4:30 PM</div>
                                    </div>
                                    <span class="badge badge-secondary">Belian</span>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-yellow-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-heart text-yellow-700"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">Resipi ditambah ke kegemaran</div>
                                        <div class="text-gray-600">Smoothie Hijau Pagi</div>
                                        <div class="text-sm text-gray-500 mt-1">2 hari lalu, 9:15 AM</div>
                                    </div>
                                    <span class="badge badge-primary">Kegemaran</span>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-utensils text-purple-700"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">Anda menyelesaikan rancangan makanan</div>
                                        <div class="text-gray-600">Rancangan untuk minggu 15-21 Julai</div>
                                        <div class="text-sm text-gray-500 mt-1">3 hari lalu, 11:45 AM</div>
                                    </div>
                                    <span class="badge badge-secondary">Rancangan</span>
                                </div>
                            </div>
                            
                            <button class="mt-6 w-full text-center py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Lihat Semua Aktiviti
                            </button>
                        </div> -->
                    </div>
                    
                    <!-- Right Column -->
                    <div>
                        <!-- Shopping List -->
                        <div class="card bg-white rounded-xl p-6 mb-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-900">Senarai Belian</h3>
                                <button class="text-primary-600 hover:text-primary-800">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded text-primary-600 mr-3">
                                    <div class="flex-1">
                                        <div class="text-gray-900">Sayur Bayam</div>
                                        <div class="text-sm text-gray-500">1 ikat</div>
                                    </div>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded text-primary-600 mr-3" checked>
                                    <div class="flex-1">
                                        <div class="text-gray-900 line-through">Dada Ayam</div>
                                        <div class="text-sm text-gray-500">500g</div>
                                    </div>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded text-primary-600 mr-3">
                                    <div class="flex-1">
                                        <div class="text-gray-900">Beri Campuran</div>
                                        <div class="text-sm text-gray-500">1 paket</div>
                                    </div>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded text-primary-600 mr-3">
                                    <div class="flex-1">
                                        <div class="text-gray-900">Oat Rolled</div>
                                        <div class="text-sm text-gray-500">500g</div>
                                    </div>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded text-primary-600 mr-3" checked>
                                    <div class="flex-1">
                                        <div class="text-gray-900 line-through">Telur</div>
                                        <div class="text-sm text-gray-500">6 biji</div>
                                    </div>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button class="mt-6 w-full text-center py-3 bg-primary-600 hover:bg-primary-700 rounded-lg text-white font-medium">
                                Simpan Senarai
                            </button>
                        </div>
                        
                        <!-- Health Goals -->
                        <div class="card bg-white rounded-xl p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Maklumat Terkini</h3>
                            
                            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-lightbulb text-yellow-500 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-yellow-800">Petua Hari Ini</h4>
                                        <div class="mt-1 text-sm text-yellow-700">
                                            Minum segelas air 30 minit sebelum makan untuk mengawal selera.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile overlay -->
    <div class="overlay"></div>
    
    </main>

    <?php $location_index='..'; include('../components/footer.php')?>
    
</body>
</html>