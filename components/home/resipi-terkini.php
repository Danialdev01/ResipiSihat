<?php
// Initialize variables
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$recipesPerPage = 9;  // Changed to 9 to match grid layout
$offset = ($page - 1) * $recipesPerPage;

// Prepare base SQL queries
$baseSql = "SELECT * FROM recipes WHERE 1=1 AND status_recipe = 1";
$countSql = "SELECT COUNT(*) AS total_recipes FROM recipes WHERE 1=1";

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
        $baseSql .= " AND category_recipe = 'sarapan'";
        $countSql .= " AND category_recipe = 'sarapan'";
        break;
    case 'lunch':
        $baseSql .= " AND category_recipe = 'makan tengahari'";
        $countSql .= " AND category_recipe = 'makan tengahari'";
        break;
    case 'dinner':
        $baseSql .= " AND category_recipe = 'makan malam'";
        $countSql .= " AND category_recipe = 'makan malam'";
        break;
    case 'snack':
        $baseSql .= " AND category_recipe = 'snek'";
        $countSql .= " AND category_recipe = 'snek'";
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

// Get total recipes count
try {
    $countStmt = $connect->prepare($countSql);
    foreach ($countParams as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecipes = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecipes / $recipesPerPage);
} catch (PDOException $e) {
    $totalRecipes = 0;
    $totalPages = 1;
}

// Get recipes
$recipes = [];
if ($totalRecipes > 0) {
    // Ensure page is within valid range
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $recipesPerPage;

    // Add pagination to query
    $baseSql .= " LIMIT :limit OFFSET :offset";

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
        error_log("Database error: " . $e->getMessage());
    }
}
?>

<div class="py-12 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search Bar -->
        <form method="GET" action="">
            <div class="search-container">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                        name="search"
                        id="searchInput" 
                        placeholder="Cari resipi, bahan, kategori..." 
                        class="w-full px-6 py-4 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700"
                        value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                    <button type="submit" id="searchButton" class="text-white absolute end-2.5 bottom-2.5 bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2">Search</button>
                </div>
                
                <!-- Quick Search Suggestions -->
                <div class="flex flex-wrap justify-center gap-3 mt-4">
                    <a href="?search=Sarapan&filter=all" class="quick-search bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-blue-200 transition-colors">
                        Sarapan
                    </a>
                    <a href="?search=Makan+Tengahari&filter=all" class="quick-search bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-green-200 transition-colors">
                        Makan Tengahari
                    </a>
                    <a href="?search=Makan+Malam&filter=all" class="quick-search bg-purple-100 text-purple-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-purple-200 transition-colors">
                        Makan Malam
                    </a>
                    <a href="?search=Rendah+Kalori&filter=low-cal" class="quick-search bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-yellow-200 transition-colors">
                        Rendah Kalori
                    </a>
                </div>
            </div>
        </form>
        
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
                    Kami tidak menemui sebarang resipi yang sepadan dengan carian anda. Cuba kata kunci lain.
                </p>
            </center>
        </div>
        <?php endif; ?>
        
        <br>
        <!-- Recipe Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="recipeGrid">
            <?php if (count($recipes) > 0): ?>
                <?php foreach($recipes as $recipe): ?>
                    <a href="<?php echo $location_index?>/user/resipi/?id=<?php echo $recipe['id_recipe']?>">
                        <div class="recipe-card rounded border border-gray-200">
                            <div class="image-container">
                                <div class="h-48 overflow-hidden">
                                    <img src="<?php echo htmlspecialchars(formatImagePath($recipe['image_recipe'], $location_index ."/"))?>" 
                                            alt="<?php echo htmlspecialchars($recipe['name_recipe'] ?? ''); ?>" 
                                            class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="px-6 pb-4">
                                <h3 class="text-xl font-bold text-gray-900 mt-3 mb-2">
                                    <?php echo htmlspecialchars($recipe['name_recipe'] ?? ''); ?>
                                </h3>
                                <?php

                                    $name_user_sql = $connect->prepare("SELECT name_user FROM users WHERE id_user = :id_user");
                                    $name_user_sql->execute([":id_user" => $recipe['id_user']]);
                                    $name_user = $name_user_sql->fetch(PDO::FETCH_ASSOC);

                                ?>
                                <p class="text-gray-600 mb-4">
                                    <?php echo htmlspecialchars($recipe['desc_recipe'] ?? ''); ?>
                                </p>
                                <div class="flex justify-between items-start">
                                    <div class="mb-4">
                                        <?php 
                                        $categoryClasses = [
                                            'sarapan' => 'bg-blue-100 text-blue-800',
                                            'makan tengahari' => 'bg-green-100 text-green-800',
                                            'makan malam' => 'bg-purple-100 text-purple-800',
                                            'snek' => 'bg-orange-100 text-orange-800'
                                        ];
                                        $class = $categoryClasses[$recipe['category_recipe']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm <?php echo $class; ?>">
                                            <?php echo htmlspecialchars($recipe['category_recipe'] ?? ''); ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center text-yellow-500">
                                        <span class="ml-1 text-gray-700"><?php echo htmlspecialchars($recipe['rating_recipe'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-800 font-bold"><?php echo explode(' ', trim($name_user['name_user']))[0]?></div>
                                    <div class="flex items-center text-gray-500">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span class="cooking-time"><?php echo htmlspecialchars($recipe['cooking_time_recipe'] ?? ''); ?> minit</span>
                                        <i class="fas fa-fire ml-4 mr-2"></i>
                                        <span class="calories"><?php echo htmlspecialchars($recipe['calories_recipe'] ?? ''); ?> kalori</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-12 space-x-1">
            <!-- Previous button -->
            <a href="?search=<?php echo urlencode($searchQuery); ?>&filter=<?php echo $filter; ?>&page=<?php echo max(1, $page - 1); ?>" 
               class="<?php echo $page == 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'; ?> flex items-center justify-center w-10 h-10 rounded-full">
                <i class="fas fa-chevron-left text-gray-600"></i>
            </a>
            
            <!-- Page buttons -->
            <?php 
            $maxVisiblePages = 5;
            $startPage = max(1, $page - floor($maxVisiblePages / 2));
            $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
            
            if ($endPage - $startPage < $maxVisiblePages - 1) {
                $startPage = max(1, $endPage - $maxVisiblePages + 1);
            }
            
            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
                <a href="?search=<?php echo urlencode($searchQuery); ?>&filter=<?php echo $filter; ?>&page=<?php echo $i; ?>" 
                   class="<?php echo $i == $page ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-200'; ?> flex items-center justify-center w-10 h-10 rounded-full">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <!-- Next button -->
            <a href="?search=<?php echo urlencode($searchQuery); ?>&filter=<?php echo $filter; ?>&page=<?php echo min($totalPages, $page + 1); ?>" 
               class="<?php echo $page == $totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'; ?> flex items-center justify-center w-10 h-10 rounded-full">
                <i class="fas fa-chevron-right text-gray-600"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
// Close connection
$connect = null;
?>