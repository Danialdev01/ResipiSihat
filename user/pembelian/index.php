<?php 
$location_index = "../.."; 
include('../../components/head.php');

?>

<body>
    <?php include("../../components/user/header.php")?>
    <?php 
    
    // Dapatkan resipi yang di-bookmark oleh pengguna
    $sql = "SELECT r.*, b.created_date_bookmark 
            FROM recipes r 
            INNER JOIN bookmarks b ON r.id_recipe = b.id_recipe 
            WHERE b.id_user = :user_id 
            ORDER BY b.created_date_bookmark DESC";
    $stmt = $connect->prepare($sql);
    $stmt->execute([':user_id' => $user['id_user']]);
    $bookmarked_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <main>
        <div class="dashboard-grid">
            <?php include("../../components/user/nav.php")?>
            
            <!-- Main Content -->
            <div class="main-content">
                <?php include("../../components/user/top-bar.php")?>
                
                <!-- Main Dashboard Content -->
                <div class="p-6">
                    <!-- Form Pilihan Jumlah Orang -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Tetapan Pembelian</h2>
                        <form method="GET" action="cetak.php" target="_blank" class="flex items-end gap-4">
                            <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                            <div>
                                <label for="jumlah_orang" class="block mb-2 text-sm font-medium text-gray-900">
                                    Jumlah Orang
                                </label>
                                <input type="number" name="jumlah_orang" id="jumlah_orang" 
                                    min="1" max="20" value="4" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-2.5" 
                                    required>
                            </div>
                            <button type="submit" 
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                Cetak Senarai Belian
                            </button>
                        </form>
                        <p class="text-sm text-gray-600 mt-2">
                            Kuantiti bahan akan dilaraskan secara automatik berdasarkan jumlah orang.
                        </p>
                    </div>

                    <!-- Senarai Resipi Bookmark -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">Resipi yang Disimpan</h2>
                        
                        <?php if (count($bookmarked_recipes) > 0): ?>
                            <table id="default-table" class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th>
                                            <span class="flex items-center">
                                                Nama Resipi
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/>
                                                </svg>
                                            </span>
                                        </th>
                                        <th>
                                            <span class="flex items-center">
                                                Bilangan Bahan
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/>
                                                </svg>
                                            </span>
                                        </th>
                                        <th>
                                            <span class="flex items-center">
                                                Masa Masak
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/>
                                                </svg>
                                            </span>
                                        </th>
                                        <th>
                                            <span class="flex items-center">
                                                Aktiviti
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/>
                                                </svg>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookmarked_recipes as $recipe): 
                                        $ingredients = json_decode(html_entity_decode($recipe['ingredient_recipe'], ENT_QUOTES, 'UTF-8'), true);
                                        $num_ingredients = is_array($ingredients) ? count($ingredients) : 0;
                                    ?>
                                        <tr class="bg-white border-b">
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                <?php echo htmlspecialchars($recipe['name_recipe']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo $num_ingredients; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo $recipe['cooking_time_recipe']; ?> minit
                                            </td>
                                            <td class="px-6 py-4">
                                                <form action="../../backend/recipe.php" method="post">
                                                    <input type="hidden" name="id_recipe" value="<?php echo $recipe['id_recipe']; ?>">
                                                    <input type="hidden" name="token" value="<?php echo $token?>">
                                                    <button type="submit" name="delete_bookmark_recipe"  
                                                            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2">
                                                        Buang
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <p class="text-gray-500">Tiada resipi yang disimpan lagi.</p>
                                <a href="<?php echo $location_index; ?>/user/resipi/" 
                                   class="inline-block mt-4 text-blue-600 hover:underline">
                                    Cari Resipi
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile overlay -->
        <div class="overlay"></div>
    </main>

    <script>
        // Initialize DataTable
        if (document.getElementById("default-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#default-table", {
                searchable: true,
                perPageSelect: true,
                perPage: 10
            });
        }

        // Fungsi untuk buang bookmark
        function removeBookmark(recipeId) {
            if (confirm('Adakah anda pasti ingin membuang resipi ini dari bookmark?')) {
                const formData = new FormData();
                formData.append('recipe_id', recipeId);
                formData.append('remove_bookmark', 'true');
                formData.append('token', '<?php echo $token ?? ""; ?>');

                fetch('<?php echo $location_index; ?>/backend/recipe.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Resipi berjaya dikeluarkan dari bookmark.');
                        location.reload();
                    } else {
                        alert('Gagal mengeluarkan resipi: ' + (data.error || 'Sila cuba lagi.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ralat berlaku ketika mengeluarkan resipi.');
                });
            }
        }
    </script>

    <?php $location_index='../..'; include('../../components/footer.php')?>
</body>
</html>