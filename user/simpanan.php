<?php $location_index = ".."; include('../components/head.php');?>

<body>
    <?php include("../components/user/header.php")?>

    <main>

        <div class="dashboard-grid">

            <?php include("../components/user/nav.php")?>
            <?php

            // Initialize variables
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $recipesPerPage = 6;
            $offset = ($page - 1) * $recipesPerPage;

            // Prepare base SQL queries to get bookmarked recipes
            $id_user = $user['id_user'];
            $baseSql = "SELECT r.* FROM recipes r 
                       INNER JOIN bookmarks b ON r.id_recipe = b.id_recipe 
                       WHERE b.id_user = :id_user";
            $countSql = "SELECT COUNT(*) AS total_recipes FROM recipes r 
                        INNER JOIN bookmarks b ON r.id_recipe = b.id_recipe 
                        WHERE b.id_user = :id_user";

            // Initialize parameters array
            $params = [':id_user' => $id_user];
            $countParams = [':id_user' => $id_user];

            // Add search conditions
            if (!empty($searchQuery)) {
                $searchTerm = "%$searchQuery%";
                $baseSql .= " AND (r.name_recipe LIKE :search OR r.desc_recipe LIKE :search OR r.category_recipe LIKE :search)";
                $countSql .= " AND (r.name_recipe LIKE :search OR r.desc_recipe LIKE :search OR r.category_recipe LIKE :search)";
                $params[':search'] = $searchTerm;
                $countParams[':search'] = $searchTerm;
            }

            // Add filter conditions
            switch($filter) {
                case 'breakfast':
                    $baseSql .= " AND r.category_recipe = 'Sarapan'";
                    $countSql .= " AND r.category_recipe = 'Sarapan'";
                    break;
                case 'lunch':
                    $baseSql .= " AND r.category_recipe = 'Makan Tengahari'";
                    $countSql .= " AND r.category_recipe = 'Makan Tengahari'";
                    break;
                case 'dinner':
                    $baseSql .= " AND r.category_recipe = 'Makan Malam'";
                    $countSql .= " AND r.category_recipe = 'Makan Malam'";
                    break;
                case 'snack':
                    $baseSql .= " AND r.category_recipe = 'Snek'";
                    $countSql .= " AND r.category_recipe = 'Snek'";
                    break;
                case 'low-cal':
                    $baseSql .= " AND r.calories_recipe < 200";
                    $countSql .= " AND r.calories_recipe < 200";
                    break;
                case 'high-rating':
                    $baseSql .= " AND r.rating_recipe >= 4.5";
                    $countSql .= " AND r.rating_recipe >= 4.5";
                    break;
            }

            // Add ordering and pagination
            $baseSql .= " ORDER BY b.created_date_bookmark DESC LIMIT :limit OFFSET :offset";

            // Get total bookmarked recipes count
            try {
                $countStmt = $connect->prepare($countSql);
                foreach ($countParams as $key => $value) {
                    $countStmt->bindValue($key, $value);
                }
                $countStmt->execute();
                $totalRecipes = $countStmt->fetchColumn();
                $totalPages = ceil($totalRecipes / $recipesPerPage);
            } catch (PDOException $e) {
                // Handle error
                $totalRecipes = 0;
                $totalPages = 1;
            }

            // Get bookmarked recipes
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
                    $stmt->bindValue(':limit', $recipesPerPage, PDO::PARAM_INT);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    
                    $stmt->execute();
                    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    // Handle error
                    error_log("Database error: " . $e->getMessage());
                }
            }
            ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <?php include("../components/user/top-bar.php")?>
            
            <!-- Main Dashboard Content -->
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6">Resipi Disimpan Saya</h1>

                <form method="GET" action="" class="flex items-center max-w-lg mx-auto">   
                    <label for="voice-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-primary-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.011 13H20c-.367 2.5551-2.32 4.6825-4.9766 5.6162V20H8.97661v-1.3838C6.31996 17.6825 4.36697 15.5551 4 13h14.011Zm0 0c1.0995-.0059 1.989-.8991 1.989-2 0-.8637-.5475-1.59948-1.3143-1.87934M18.011 13H18m0-3.99997c.2409 0 .4718.04258.6857.12063m0 0c.8367-1.0335.7533-2.67022-.2802-3.50694-1.0335-.83672-2.5496-.6772-3.3864.35631-.293-1.50236-1.7485-2.15377-3.2509-1.8607-1.5023.29308-2.48263 1.74856-2.18956 3.25092C8.9805 6.17263 7.6182 5.26418 6.15462 6.00131 4.967 6.59945 4.45094 8.19239 5.04909 9.38002m0 0C4.37083 9.66467 4 10.3357 4 11.1174 4 12.1571 4.84288 13 5.88263 13m-.83354-3.61998c.2866-.12029 1.09613-.40074 2.04494.3418m5.27497-.89091c1.0047-.4589 2.1913-.01641 2.6502.98832"/>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" class="bg-primary-50 border border-gray-300 text-primary-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Cari resipi disimpan...." value="<?php echo htmlspecialchars($searchQuery); ?>" />
                    </div>
                    <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-primary-700 rounded-lg border border-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>Cari
                    </button>
                </form>
                <br><br>

                <!-- Filter Options -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <a href="?filter=all" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'all') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Semua
                    </a>
                    <a href="?filter=breakfast" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'breakfast') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Sarapan
                    </a>
                    <a href="?filter=lunch" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'lunch') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Makan Tengahari
                    </a>
                    <a href="?filter=dinner" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'dinner') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Makan Malam
                    </a>
                    <a href="?filter=snack" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'snack') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Snek
                    </a>
                    <a href="?filter=low-cal" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'low-cal') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Rendah Kalori
                    </a>
                    <a href="?filter=high-rating" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($filter == 'high-rating') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Rating Tinggi
                    </a>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-3">

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
                                <h3 class="text-xl font-bold text-gray-700 mb-2">Tiada Resipi Disimpan Ditemui</h3>
                                <p class="text-gray-600 max-w-md mx-auto">
                                    <?php if (!empty($searchQuery) || $filter != 'all'): ?>
                                    Kami tidak menemui sebarang resipi disimpan yang sepadan dengan carian anda. Cuba kata kunci lain atau lihat semua resipi disimpan.
                                    <?php else: ?>
                                    Anda belum menyimpan sebarang resipi. Mula menyimpan resipi kegemaran anda untuk melihatnya di sini.
                                    <?php endif; ?>
                                </p>
                            </center>
                        </div>
                        <?php endif; ?>
                        
                        <br>
                        <!-- Recipe Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" id="recipeGrid">
                            <?php if (count($recipes) > 0): ?>
                                <?php foreach($recipes as $recipe): ?>
                                    <a href="./resipi/?id=<?php echo $recipe['id_recipe'] ?>">
                                        <!-- <div data-modal-target="resipi-modal-<?php echo $recipe['id_recipe']?>" data-modal-toggle="resipi-modal-<?php echo $recipe['id_recipe']?>" class="meal-card bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300"> -->
                                            <div class="h-32 relative">
                                                <img src="<?php echo htmlspecialchars(formatImagePath($recipe['image_recipe'], "../"))?>" 
                                                    alt="<?php echo htmlspecialchars($recipe['name_recipe'] ?? '')?>" 
                                                    class="w-full h-full object-cover">
                                                <div class="absolute top-2 right-2">
                                                    <span class="bg-primary-600 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                                        Disimpan
                                                    </span>
                                                </div>
                                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                                    <div class="text-white font-semibold"><?php echo htmlspecialchars($recipe['category_recipe'] ?? '')?></div>
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <div class="font-bold mb-1"><?php echo htmlspecialchars($recipe['name_recipe'] ?? '')?></div>
                                                <div class="text-sm text-gray-600 flex justify-between">
                                                    <span><i class="fas fa-clock mr-1"></i><?php echo htmlspecialchars($recipe['cooking_time_recipe'] ?? '')?> min</span>
                                                    <span><i class="fas fa-fire mr-1"></i><?php echo htmlspecialchars($recipe['calories_recipe'] ?? '')?> kalori</span>
                                                </div>
                                            </div>
                                        <!-- </div> -->
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="flex justify-center mt-8">
                            <nav class="inline-flex">
                                <a href="?page=<?php echo max(1, $page-1); ?>&search=<?php echo urlencode($searchQuery); ?>&filter=<?php echo $filter; ?>" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 <?php echo ($page == 1) ? 'pointer-events-none opacity-50' : ''; ?>">
                                    Sebelumnya
                                </a>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>&filter=<?php echo $filter; ?>" class="px-3 py-2 leading-tight border border-gray-300 <?php echo ($i == $page) ? 'text-primary-600 bg-primary-50' : 'text-gray-500 bg-white hover:bg-gray-100'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <a href="?page=<?php echo min($totalPages, $page+1); ?>&search=<?php echo urlencode($searchQuery); ?>&filter=<?php echo $filter; ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 <?php echo ($page == $totalPages) ? 'pointer-events-none opacity-50' : ''; ?>">
                                    Seterusnya
                                </a>
                            </nav>
                        </div>
                        <?php endif; ?>
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
        
        // Show loading spinner when searching
        const searchForm = document.querySelector('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                document.getElementById('loadingSpinner').style.display = 'block';
                document.getElementById('recipeGrid').style.display = 'none';
            });
        }
    </script>

    </main>

    <?php $location_index='..'; include('../components/footer.php')?>
    
</body>
</html>