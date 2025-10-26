<?php $location_index = "../.."; include('../../components/head.php');?>

<body>
    <?php include("../../components/user/header.php")?>

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
                        <input type="hidden" name="ingredient_recipe" id="ingredientsData">
                        
                        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <div class="sm:col-span-2">
                                <label for="name_recipe" class="block mb-2 text-sm font-medium text-gray-900">Nama Resepi</label>
                                <input type="text" name="name_recipe" id="name_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Contoh: Ayam masak kicap" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="desc_recipe" class="block mb-2 text-sm font-medium text-gray-900">Tentang Resepi</label>
                                <input type="text" name="desc_recipe" id="desc_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Resepi ayam yang mudah dan sedap untuk dimakan" required>
                            </div>
                            
                            <!-- Recipe Image Upload Section -->
                            <div class="sm:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Gambar Resepi</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="recipeImage" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadPlaceholder">
                                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk muat naik</span> atau seret dan lepaskan</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF (MAX. 5MB)</p>
                                        </div>
                                        <img id="imagePreview" class="hidden w-full h-full object-cover rounded-lg" />
                                        <input id="recipeImage" name="image_recipe" type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div> 
                                <div class="mt-1 flex justify-between">
                                    <p class="text-xs text-gray-500" id="fileInfo"></p>
                                    <button type="button" id="removeImageBtn" class="hidden text-red-600 hover:text-red-800 text-sm">Buang Gambar</button>
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
                                            <tbody>
                                                <!-- Ingredients will be added here dynamically -->
                                            </tbody>
                                        </table>
                                        <p id="noIngredientsMsg" class="mt-2 text-sm text-gray-500 text-center">Tiada bahan ditambah lagi.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="category_recipe" class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                                <select id="category_recipe" name="category_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="sarapan" selected>Sarapan</option>
                                    <option value="snek">Snek</option>
                                    <option value="makan tengahari">Makan Tengahari</option>
                                    <option value="makan malam">Makan Malam</option>
                                </select>
                            </div>
                            <div>
                                <label for="cooking_time_recipe" class="block mb-2 text-sm font-medium text-gray-900">Anggaran Masa Penyediaan (Minit)</label>
                                <input type="number" name="cooking_time_recipe" id="cooking_time_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="30" required>
                            </div> 
                            <div>
                                <label for="url_resource_recipe" class="block mb-2 text-sm font-medium text-gray-900">Pautan video luar (Youtube)</label>
                                <input type="text" name="url_resource_recipe" id="url_resource_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="https://www.youtube.com/embed/tZq7Ws7Yo5o?si=4BngHZJqbUubAyCA">
                            </div>
                            <div>
                                <label for="visibility_recipe" class="block mb-2 text-sm font-medium text-gray-900">Jenis Resepi</label>
                                <select id="visibility_recipe" name="visibility_recipe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="1" selected>Boleh Dilihat awam</option>
                                    <option value="2">Persendirian</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="tutorial_recipe" class="block mb-2 text-sm font-medium text-gray-900">Kaedah Penyediaan</label>
                                <textarea id="tutorial_recipe" name="tutorial_recipe" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500" placeholder="1. Panaskan bahan
2. Potong Bahan
3. Hidang Makanan" required></textarea>
                            </div>
                        </div>
                        <button type="submit" name="create_recipe" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200">
                            Tambah Resepi
                        </button>
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
                        imagePreview.classList.remove('hidden');
                        uploadPlaceholder.classList.add('hidden');
                        removeImageBtn.classList.remove('hidden');
                        
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
                imagePreview.classList.add('hidden');
                uploadPlaceholder.classList.remove('hidden');
                removeImageBtn.classList.add('hidden');
                fileInfo.textContent = '';
            });
            
            // Ingredients Management
            document.addEventListener('DOMContentLoaded', function() {
                const ingredients = [];
                const addIngredientBtn = document.getElementById('addIngredientBtn');
                const ingredientNameInput = document.getElementById('ingredientName');
                const ingredientQuantityInput = document.getElementById('ingredientQuantity');
                const ingredientUnitSelect = document.getElementById('ingredientUnit');
                const customUnitContainer = document.getElementById('customUnitContainer');
                const customUnitInput = document.getElementById('customUnit');
                const ingredientsTable = document.getElementById('ingredientsTable').getElementsByTagName('tbody')[0];
                const noIngredientsMsg = document.getElementById('noIngredientsMsg');
                const ingredientsData = document.getElementById('ingredientsData');
                
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
                function addIngredient(name, quantity, unit) {
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
                        id: Date.now(), // unique identifier
                        name: name.trim(),
                        quantity: unit === 'secukup rasa' ? 'secukup rasa' : parseFloat(quantity),
                        unit: unit.trim()
                    };
                    
                    ingredients.push(ingredient);
                    
                    // Add to table
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
                    
                    // Hide no ingredients message
                    noIngredientsMsg.style.display = 'none';
                    
                    // Add event listener to remove button
                    row.querySelector('.remove-ingredient').addEventListener('click', function() {
                        removeIngredient(ingredient.id);
                    });
                    
                    // Update hidden input
                    updateIngredientsData();
                    
                    // Clear input fields
                    ingredientNameInput.value = '';
                    ingredientQuantityInput.value = '';
                    ingredientUnitSelect.value = '';
                    customUnitContainer.classList.add('hidden');
                    customUnitInput.value = '';
                    ingredientQuantityInput.disabled = false;
                    ingredientQuantityInput.placeholder = 'Kuantiti (contoh: 2)';
                    ingredientNameInput.focus();
                }
                
                // Function to remove ingredient from the list
                function removeIngredient(id) {
                    // Remove from array
                    const index = ingredients.findIndex(ing => ing.id === id);
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
                
                // Event listener for add ingredient button
                addIngredientBtn.addEventListener('click', function() {
                    addIngredient(ingredientNameInput.value, ingredientQuantityInput.value, getSelectedUnit());
                });
                
                // Allow adding ingredient with Enter key in unit field
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
                    }
                    
                    // Validate image is uploaded
                    if (!recipeImageInput.files || recipeImageInput.files.length === 0) {
                        e.preventDefault();
                        alert('Sila muat naik gambar untuk resepi ini.');
                        recipeImageInput.focus();
                    }
                });
            });
        </script>
    </main>

    <?php $location_index='../..'; include('../../components/footer.php')?>