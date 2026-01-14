<?php $location_index = "../.."; include('../../components/head.php');?>

<body>
    <?php include("../../components/user/header.php")?>
    <?php 
        $recipe_sql = $connect->prepare("SELECT r.*, u.name_user AS author_name FROM recipes r JOIN users u ON r.id_user = u.id_user WHERE r.id_recipe = ?");
        $recipe_sql->execute([$_GET['id']]);
        $recipe = $recipe_sql->fetch(PDO::FETCH_ASSOC);

        // If no recipe found, redirect
        if(!$recipe) {
            echo '<script>window.location.href = window.location.href.split("?")[0];</script>';
            exit();
        }

        // Decode ingredients JSON - fix the HTML entities issue
        $ingredients_json = $recipe['ingredient_recipe'];
        // First, decode HTML entities if they exist
        $ingredients_json = html_entity_decode($ingredients_json, ENT_QUOTES, 'UTF-8');
        // Then decode JSON
        $ingredients = json_decode($ingredients_json, true);
        
        if(!$ingredients) {
            $ingredients = [];
        }
    ?>

    <main>
        <div class="dashboard-grid">
            <?php include("../../components/user/nav.php")?>
        
            <!-- Main Content -->
            <div class="main-content">
                <?php include("../../components/user/top-bar.php")?>
                
                <!-- Main Dashboard Content -->
                <div class="p-6">
                    <form action="../../backend/recipe.php" method="post" id="recipeForm" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?php echo $token?>">
                        <input type="hidden" name="id_recipe" value="<?php echo $recipe['id_recipe']?>">
                        <input type="hidden" name="current_image" value="<?php echo $recipe['image_recipe']?>">
                        <input type="hidden" name="ingredient_recipe" id="ingredientsData" value='<?php echo htmlspecialchars($recipe['ingredient_recipe'], ENT_QUOTES, 'UTF-8')?>'>
                        
                        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <div class="sm:col-span-2">
                                <label for="name_recipe" class="block mb-2 text-sm font-medium text-gray-900">Nama Resipi</label>
                                <input type="text" name="name_recipe" value="<?php echo htmlspecialchars($recipe['name_recipe'])?>" id="name_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Contoh: Ayam masak kicap" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="desc_recipe" class="block mb-2 text-sm font-medium text-gray-900">Tentang Resipi</label>
                                <input type="text" name="desc_recipe" value="<?php echo htmlspecialchars($recipe['desc_recipe'])?>" id="desc_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Resipi ayam yang mudah dan sedap untuk dimakan" required>
                            </div>
                            
                            <!-- Recipe Image Upload Section -->
                            <div class="sm:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Gambar Resipi</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="recipeImage" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadPlaceholder" style="<?php echo $recipe['image_recipe'] ? 'display: none;' : '' ?>">
                                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk muat naik</span> atau seret dan lepaskan</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF (MAX. 5MB)</p>
                                        </div>
                                        <img id="imagePreview" class="w-full h-full object-cover rounded-lg" 
                                             src="../../uploads/recipes/<?php echo $recipe['image_recipe'] ?>" 
                                             alt="Preview"
                                             style="<?php echo $recipe['image_recipe'] ? '' : 'display: none;' ?>" />
                                        <input id="recipeImage" name="image_recipe" type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div> 
                                <div class="mt-1 flex justify-between">
                                    <p class="text-xs text-gray-500" id="fileInfo">
                                        <?php if($recipe['image_recipe']): ?>
                                            Current: <?php echo $recipe['image_recipe'] ?>
                                        <?php endif; ?>
                                    </p>
                                    <button type="button" id="removeImageBtn" class="text-red-600 hover:text-red-800 text-sm" style="<?php echo $recipe['image_recipe'] ? '' : 'display: none;' ?>">Buang Gambar</button>
                                </div>
                            </div>
                            
                            <!-- Ingredient Management Section -->
                            <div class="sm:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Bahan-bahan</label>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-300">
                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-4 md:flex">
                                        <div class="md:col-span-2 w-full">
                                            <input type="text" id="ingredientName" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Nama bahan (contoh: Bawang)">
                                        </div>
                                        <div class="md:col-span-1 w-full">
                                            <input type="number" step="0.01" id="ingredientQuantity" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Kuantiti (contoh: 2)" min="0">
                                        </div>
                                        <div class="md:col-span-1 w-full">
                                            <select id="ingredientUnit" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                                <option value="">Pilih Unit</option>
                                                <option value="secukup rasa">secukup rasa</option>
                                                <option value="biji">biji</option>
                                                <option value="ekor">ekor</option>
                                                <option value="cawan">cawan</option>
                                                <option value="sudu">sudu</option>
                                                <option value="sendok">sendok</option>
                                                <option value="kg">kg</option>
                                                <option value="g">g</option>
                                                <option value="ml">ml</option>
                                                <option value="liter">liter</option>
                                                <option value="keping">keping</option>
                                                <option value="helai">helai</option>
                                                <option value="ulas">ulas</option>
                                                <option value="batang">batang</option>
                                                <option value="ketul">ketul</option>
                                                <option value="cubit">cubit</option>
                                                <option value="lain-lain">lain-lain</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-1 hidden" id="customUnitContainer">
                                            <input type="text" id="customUnit" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Unit lain">
                                        </div>
                                        <div class="md:col-span-1">
                                            <button type="button" id="addIngredientBtn" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Ingredients List -->
                                    <div class="mt-4">
                                        <table class="w-full text-sm text-left text-gray-500" id="ingredientsTable">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3">Bahan</th>
                                                    <th scope="col" class="px-4 py-3">Kuantiti</th>
                                                    <th scope="col" class="px-4 py-3">Unit</th>
                                                    <th scope="col" class="px-4 py-3">Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ingredientsTableBody">
                                                <!-- Ingredients will be added here dynamically -->
                                                <?php if(!empty($ingredients)): ?>
                                                    <?php foreach($ingredients as $ingredient): ?>
                                                        <tr data-id="<?php echo $ingredient['id']; ?>">
                                                            <td class="px-4 py-2 font-medium text-gray-900"><?php echo htmlspecialchars($ingredient['name']); ?></td>
                                                            <td class="px-4 py-2"><?php echo htmlspecialchars($ingredient['quantity']); ?></td>
                                                            <td class="px-4 py-2"><?php echo htmlspecialchars($ingredient['unit']); ?></td>
                                                            <td class="px-4 py-2">
                                                                <button type="button" class="remove-ingredient text-red-600 hover:text-red-800">Buang</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                        <p id="noIngredientsMsg" class="mt-2 text-sm text-gray-500 text-center" style="<?php echo count($ingredients) > 0 ? 'display: none;' : '' ?>">Tiada bahan ditambah lagi.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="category_recipe" class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                                <select id="category_recipe" name="category_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="sarapan" <?php echo $recipe['category_recipe'] == 'sarapan' ? 'selected' : '' ?>>Sarapan</option>
                                    <option value="snek" <?php echo $recipe['category_recipe'] == 'snek' ? 'selected' : '' ?>>Snek</option>
                                    <option value="makan tengahari" <?php echo $recipe['category_recipe'] == 'makan tengahari' ? 'selected' : '' ?>>Makan Tengahari</option>
                                    <option value="makan malam" <?php echo $recipe['category_recipe'] == 'makan malam' ? 'selected' : '' ?>>Makan Malam</option>
                                </select>
                            </div>
                            <div>
                                <label for="cooking_time_recipe" class="block mb-2 text-sm font-medium text-gray-900">Anggaran Masa Penyediaan (Minit)</label>
                                <input type="number" name="cooking_time_recipe" value="<?php echo $recipe['cooking_time_recipe']?>" id="cooking_time_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="30" required>
                            </div> 
                            <div>
                                <label for="calories_recipe" class="block mb-2 text-sm font-medium text-gray-900">Kalori (kcal)</label>
                                <input type="number" name="calories_recipe" value="<?php echo $recipe['calories_recipe']?>" id="calories_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="400" required>
                            </div>
                            <div>
                                <label for="url_resource_recipe" class="block mb-2 text-sm font-medium text-gray-900">Pautan video luar (Youtube)</label>
                                <input type="text" name="url_resource_recipe" value="<?php echo $recipe['url_resource_recipe']?>" id="url_resource_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="https://www.youtube.com/embed/tZq7Ws7Yo5o?si=4BngHZJqbUubAyCA">
                            </div>
                            <div>
                                <label for="visibility_recipe" class="block mb-2 text-sm font-medium text-gray-900">Jenis Resipi</label>
                                <select id="visibility_recipe" name="visibility_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="1" <?php echo $recipe['visibility_recipe'] == 1 ? 'selected' : '' ?>>Boleh Dilihat awam</option>
                                    <option value="2" <?php echo $recipe['visibility_recipe'] == 2 ? 'selected' : '' ?>>Persendirian</option>
                                </select>
                            </div>
                            <div>
                                <label for="tags_recipe" class="block mb-2 text-sm font-medium text-gray-900">Tag (pisahkan dengan koma)</label>
                                <input type="text" name="tags_recipe" value="<?php echo htmlspecialchars($recipe['tags_recipe'])?>" id="tags_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="ayam, pedas, melayu">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="tutorial_recipe" class="block mb-2 text-sm font-medium text-gray-900">Kaedah Penyediaan</label>
                                <textarea id="tutorial_recipe" name="tutorial_recipe" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500" placeholder="1. Panaskan bahan
2. Potong Bahan
3. Hidang Makanan" required><?php echo htmlspecialchars($recipe['tutorial_recipe'])?></textarea>
                            </div>
                        </div>
                        <div class="flex gap-4 mt-6">
                            <button type="submit" name="update_recipe" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 hover:bg-primary-800">
                                Kemaskini Resipi
                            </button>
                            <a href="?delete=<?php echo $recipe['id_recipe']?>" onclick="return confirm('Adakah anda pasti mahu memadam resipi ini?')" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">
                                Padam Resipi
                            </a>
                        </div>
                    </form>
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
            
            // Image Upload Preview
            const recipeImageInput = document.getElementById('recipeImage');
            const imagePreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const removeImageBtn = document.getElementById('removeImageBtn');
            const fileInfo = document.getElementById('fileInfo');
            
            recipeImageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Check file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Fail terlalu besar. Sila pilih fail yang kurang daripada 5MB.');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        uploadPlaceholder.style.display = 'none';
                        removeImageBtn.style.display = 'block';
                        
                        // Display file info
                        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                        fileInfo.textContent = `${file.name} (${fileSize} MB)`;
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Remove image
            removeImageBtn.addEventListener('click', function() {
                recipeImageInput.value = '';
                imagePreview.style.display = 'none';
                uploadPlaceholder.style.display = 'flex';
                this.style.display = 'none';
                fileInfo.textContent = 'Tiada gambar dipilih';
            });
            
            // Ingredients Management
            document.addEventListener('DOMContentLoaded', function() {
                // Parse existing ingredients from hidden input
                const ingredientsData = document.getElementById('ingredientsData');
                let ingredients = [];
                try {
                    // First decode HTML entities, then parse JSON
                    let jsonStr = ingredientsData.value;
                    // Create a temporary element to decode HTML entities
                    const txt = document.createElement("textarea");
                    txt.innerHTML = jsonStr;
                    jsonStr = txt.value;
                    ingredients = JSON.parse(jsonStr) || [];
                } catch (e) {
                    console.error('Error parsing ingredients:', e);
                    ingredients = [];
                }
                
                const addIngredientBtn = document.getElementById('addIngredientBtn');
                const ingredientNameInput = document.getElementById('ingredientName');
                const ingredientQuantityInput = document.getElementById('ingredientQuantity');
                const ingredientUnitSelect = document.getElementById('ingredientUnit');
                const customUnitContainer = document.getElementById('customUnitContainer');
                const customUnitInput = document.getElementById('customUnit');
                const ingredientsTable = document.getElementById('ingredientsTableBody');
                const noIngredientsMsg = document.getElementById('noIngredientsMsg');
                
                // Show custom unit input when "lain-lain" is selected
                ingredientUnitSelect.addEventListener('change', function() {
                    if (this.value === 'lain-lain') {
                        customUnitContainer.classList.remove('hidden');
                        customUnitInput.focus();
                    } else {
                        customUnitContainer.classList.add('hidden');
                        customUnitInput.value = '';
                    }
                    
                    // Handle "secukup rasa" selection
                    if (this.value === 'secukup rasa') {
                        ingredientQuantityInput.value = '';
                        ingredientQuantityInput.disabled = true;
                        ingredientQuantityInput.placeholder = 'secukup rasa';
                    } else {
                        ingredientQuantityInput.disabled = false;
                        ingredientQuantityInput.placeholder = 'Kuantiti (contoh: 2)';
                    }
                });
                
                // Function to get the selected unit
                function getSelectedUnit() {
                    if (ingredientUnitSelect.value === 'lain-lain') {
                        return customUnitInput.value.trim();
                    } else {
                        return ingredientUnitSelect.value;
                    }
                }
                
                // Function to update the hidden input with ingredients data
                function updateIngredientsData() {
                    ingredientsData.value = JSON.stringify(ingredients);
                }
                
                // Function to add ingredient to the list
                function addIngredient(name, quantity, unit, id = null) {
                    if (!name.trim()) {
                        alert('Sila isi nama bahan.');
                        return;
                    }
                    
                    // Handle "secukup rasa" case
                    if (unit === 'secukup rasa') {
                        quantity = 'secukup rasa';
                    } else if (!quantity || !unit.trim()) {
                        alert('Sila isi kuantiti dan unit bahan.');
                        return;
                    }
                    
                    const ingredient = {
                        id: id || Date.now(), // use existing id or create new one
                        name: name.trim(),
                        quantity: unit === 'secukup rasa' ? 'secukup rasa' : parseFloat(quantity),
                        unit: unit.trim()
                    };
                    
                    if (!id) {
                        ingredients.push(ingredient);
                    }
                    
                    // Add to table only if it's a new ingredient
                    if (!id) {
                        const row = ingredientsTable.insertRow();
                        row.setAttribute('data-id', ingredient.id);
                        row.innerHTML = `
                            <td class="px-4 py-2 font-medium text-gray-900">${ingredient.name}</td>
                            <td class="px-4 py-2">${ingredient.quantity}</td>
                            <td class="px-4 py-2">${ingredient.unit}</td>
                            <td class="px-4 py-2">
                                <button type="button" class="remove-ingredient text-red-600 hover:text-red-800">Buang</button>
                            </td>
                        `;
                        
                        // Add event listener to remove button
                        row.querySelector('.remove-ingredient').addEventListener('click', function() {
                            removeIngredient(ingredient.id);
                        });
                    }
                    
                    // Hide no ingredients message
                    noIngredientsMsg.style.display = 'none';
                    
                    // Update hidden input
                    updateIngredientsData();
                    
                    // Clear input fields if adding new ingredient
                    if (!id) {
                        ingredientNameInput.value = '';
                        ingredientQuantityInput.value = '';
                        ingredientUnitSelect.value = '';
                        customUnitContainer.classList.add('hidden');
                        customUnitInput.value = '';
                        ingredientQuantityInput.disabled = false;
                        ingredientQuantityInput.placeholder = 'Kuantiti (contoh: 2)';
                        ingredientNameInput.focus();
                    }
                }
                
                // Function to remove ingredient from the list
                function removeIngredient(id) {
                    // Remove from array
                    const index = ingredients.findIndex(ing => ing.id == id);
                    if (index !== -1) {
                        ingredients.splice(index, 1);
                    }
                    
                    // Remove from table
                    const row = ingredientsTable.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        ingredientsTable.removeChild(row);
                    }
                    
                    // Show no ingredients message if empty
                    if (ingredients.length === 0) {
                        noIngredientsMsg.style.display = 'block';
                    }
                    
                    // Update hidden input
                    updateIngredientsData();
                }
                
                // Add event listeners to existing remove buttons
                const existingRemoveButtons = document.querySelectorAll('.remove-ingredient');
                existingRemoveButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const id = row.getAttribute('data-id');
                        removeIngredient(id);
                    });
                });
                
                // Event listener for add ingredient button
                addIngredientBtn.addEventListener('click', function() {
                    addIngredient(ingredientNameInput.value, ingredientQuantityInput.value, getSelectedUnit());
                });
                
                // Allow adding ingredient with Enter key in name field
                ingredientNameInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addIngredient(ingredientNameInput.value, ingredientQuantityInput.value, getSelectedUnit());
                    }
                });
                
                ingredientUnitSelect.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addIngredient(ingredientNameInput.value, ingredientQuantityInput.value, getSelectedUnit());
                    }
                });
                
                customUnitInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addIngredient(ingredientNameInput.value, ingredientQuantityInput.value, getSelectedUnit());
                    }
                });
                
                // Form submission validation
                document.getElementById('recipeForm').addEventListener('submit', function(e) {
                    if (ingredients.length === 0) {
                        e.preventDefault();
                        alert('Sila tambah sekurang-kurangnya satu bahan.');
                        ingredientNameInput.focus();
                        return false;
                    }
                    
                    return true;
                });
            });
        </script>
    </main>

    <?php $location_index='../..'; include('../../components/footer.php')?>