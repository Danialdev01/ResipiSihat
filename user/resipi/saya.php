<?php $location_index = "../.."; include('../../components/head.php');?>


<body>
    <?php include("../../components/user/header.php")?>


    <main>

        <div class="dashboard-grid">

            <?php include("../../components/user/nav.php")?>
            <?php

            // Initialize variables
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $recipesPerPage = 6;
            $offset = ($page - 1) * $recipesPerPage;

            // Prepare base SQL queries
            $id_user = $user['id_user'];
            $baseSql = "SELECT * FROM recipes WHERE 1=1 AND id_user = $id_user AND status_recipe = 1";
            $countSql = "SELECT COUNT(*) AS total_recipes FROM recipes WHERE 1=1 AND id_user = $id_user";

            // Initialize parameters array
            $params = [];
            $countParams = [];

            // Add search conditions
            if (!empty($searchQuery)) {
                $searchTerm = "%$searchQuery%";
                $baseSql .= " AND (name_recipe LIKE :search OR desc_recipe LIKE :search OR category_recipe LIKE :search)";
                $countSql .= " AND (name_recipe LIKE :search OR desc_recipe LIKE :search OR category_recipe LIKE :search)";
                $params[':search'] = $searchTerm;
                $countParams[':search'] = $searchTerm;
            }

            // Add filter conditions
            switch($filter) {
                case 'breakfast':
                    $baseSql .= " AND category_recipe = 'Sarapan'";
                    $countSql .= " AND category_recipe = 'Sarapan'";
                    break;
                case 'lunch':
                    $baseSql .= " AND category_recipe = 'Makan Tengahari'";
                    $countSql .= " AND category_recipe = 'Makan Tengahari'";
                    break;
                case 'dinner':
                    $baseSql .= " AND category_recipe = 'Makan Malam'";
                    $countSql .= " AND category_recipe = 'Makan Malam'";
                    break;
                case 'snack':
                    $baseSql .= " AND category_recipe = 'Snek'";
                    $countSql .= " AND category_recipe = 'Snek'";
                    break;
                case 'low-cal':
                    $baseSql .= " AND calories_recipe < 200";
                    $countSql .= " AND calories_recipe < 200";
                    break;
                case 'high-rating':
                    $baseSql .= " AND rating_recipe >= 4.5";
                    $countSql .= " AND rating_recipe >= 4.5";
                    break;
            }

            // Add ordering and pagination
            // $baseSql .= " ORDER BY rating_recipe DESC LIMIT :limit OFFSET :offset";

            // Get total recipes count
            try {
                $countStmt = $connect->prepare($countSql);
                foreach ($countParams as $key => $value) {
                    $countStmt->bindValue($key, $value);
                }
                $countStmt->execute();
                $totalRecipes = $countStmt->fetchColumn();
                $totalPages = ceil($totalRecipes / $recipesPerPage);
                // var_dump($totalRecipes);
            } catch (PDOException $e) {
                // Handle error
                $totalRecipes = 0;
                $totalPages = 1;
            }

            // Get recipes
            $recipes = [];
            if ($totalRecipes > 0) {
                // Ensure page is within valid range
                $page = max(1, min($page, $totalPages));
                $offset = ($page - 1) * $recipesPerPage;

                try {
                    $stmt = $connect->prepare($baseSql);
                    
                    // Bind parameters
                    foreach ($params as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    
                    // Bind pagination parameters
                    // $stmt->bindValue(':limit', $recipesPerPage, PDO::PARAM_INT);
                    // $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    
                    $stmt->execute();
                    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // var_dump($recipes);
                } catch (PDOException $e) {
                    // Handle error
                    error_log("Database error: " . $e->getMessage());
                }
            }
            ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <?php include("../../components/user/top-bar.php")?>
            
            <!-- Main Dashboard Content -->
            <div class="p-6">


                <div class="flex items-center max-w-lg mx-auto">

                    <form method="GET" action="" class="flex items-center max-w-lg mx-auto">   
                        <label for="voice-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-primary-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.011 13H20c-.367 2.5551-2.32 4.6825-4.9766 5.6162V20H8.97661v-1.3838C6.31996 17.6825 4.36697 15.5551 4 13h14.011Zm0 0c1.0995-.0059 1.989-.8991 1.989-2 0-.8637-.5475-1.59948-1.3143-1.87934M18.011 13H18m0-3.99997c.2409 0 .4718.04258.6857.12063m0 0c.8367-1.0335.7533-2.67022-.2802-3.50694-1.0335-.83672-2.5496-.6772-3.3864.35631-.293-1.50236-1.7485-2.15377-3.2509-1.8607-1.5023.29308-2.48263 1.74856-2.18956 3.25092C8.9805 6.17263 7.6182 5.26418 6.15462 6.00131 4.967 6.59945 4.45094 8.19239 5.04909 9.38002m0 0C4.37083 9.66467 4 10.3357 4 11.1174 4 12.1571 4.84288 13 5.88263 13m-.83354-3.61998c.2866-.12029 1.09613-.40074 2.04494.3418m5.27497-.89091c1.0047-.4589 2.1913-.01641 2.6502.98832"/>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" class="bg-primary-50 border border-gray-300 text-primary-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Cari resipi saya...." required />
                        </div>
                        <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-primary-700 rounded-lg border border-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>Cari
                        </button>
                    </form>
                    <a href="./baru.php">
                        <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-primary-700 rounded-lg border border-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                            </svg>
                            <span style=''>Baru</span>
                            
                        </button>
                    </a>
                </div>
                <br><br>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2">

                        <!-- Loading Spinner -->
                        <div class="loading-spinner" id="loadingSpinner" style="display: none;">
                            <div class="spinner"></div>
                            <p class="mt-4 text-gray-600">Mencari resipi dalam pangkalan data...</p>
                        </div>
                        
                        <!-- No Results Message -->
                        <?php if ($totalRecipes == 0): ?>
                        <div class="no-results" id="noResults">
                            <br><br>
                            <center>
                                <h3 class="text-xl font-bold text-gray-700 mb-2">Tiada Resipi Ditemui</h3>
                                <p class="text-gray-600 max-w-md mx-auto">
                                    Kami tidak menemui sebarang resipi yang anda telah hasilkan. <a class="text-primary-700" href="./baru.php">Cipta resipi baru.</a>
                                </p>
                            </center>
                        </div>
                        <?php endif; ?>
                        
                        <br>
                        <!-- Recipe Grid -->
                        <div  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="recipeGrid">
                            <?php if (count($recipes) > 0): ?>
                                <?php foreach($recipes as $recipe): ?>
                                    <div data-modal-target="resipi-modal-<?php echo $recipe['id_recipe']?>" data-modal-toggle="resipi-modal-<?php echo $recipe['id_recipe']?>" class="meal-card bg-gray-50 rounded-lg overflow-hidden">
                                        <div class="h-32 relative">
                                            <img src="<?php echo htmlspecialchars(formatImagePath($recipe['image_recipe'], "../../"))?>" 
                                                alt="<?php echo htmlspecialchars($recipe['name_recipe'] ?? '')?>" 
                                                class="w-full h-full object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                                <div class="text-white font-semibold"><?php echo htmlspecialchars($recipe['category_recipe'] ?? '')?></div>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <div class="font-bold mb-1"><?php echo htmlspecialchars($recipe['name_recipe'] ?? '')?></div>
                                            <div class="text-sm text-gray-600 flex justify-between">
                                                <span><i class="fas fa-clock mr-1"></i><?php echo htmlspecialchars($recipe['cooking_time_recipe'] ?? '')?></span>
                                                <span><i class="fas fa-fire mr-1"></i><?php echo htmlspecialchars($recipe['calories_recipe'] ?? '')?></span>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                    
                    <!-- Right Column -->
                    <div>
                        
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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidenav = document.querySelector('.sidenav');
        const overlay = document.querySelector('.overlay');
        
        mobileMenuButton.addEventListener('click', function() {
            sidenav.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidenav.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        // Simulate progress bar animations
        document.querySelectorAll('.progress-fill').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    </script>

    </main>

    <?php $location_index='../..'; include('../../components/footer.php')?>
    
</body>
</html>