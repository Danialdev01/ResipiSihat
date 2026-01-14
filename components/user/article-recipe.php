<?php 
    function formatDate($dateString) {
        $date = new DateTime($dateString);
        return $date->format('F j, Y');
    }
    
    // Fetch approved comments for this recipe
    $comments_sql = $connect->prepare("
        SELECT c.*, u.name_user 
        FROM comments c 
        JOIN users u ON c.id_user = u.id_user 
        WHERE c.id_recipe = :recipe_id AND c.status_comment = 1
        ORDER BY c.created_date_comment DESC
    ");
    $comments_sql->execute([':recipe_id' => $recipe['id_recipe']]);
    $comments = $comments_sql->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Recipe Content -->
<main class="container mx-auto px-4 py-8">
    <!-- Breadcrumb
    <div class="mb-6 text-sm text-gray-500">
        <a href="#" class="hover:text-blue-600">Laman Utama</a> > 
        <a href="#" class="hover:text-blue-600">Resipi</a> > 
        <span class="text-gray-700">Nasi Goreng Cina</span>
    </div> -->

    <!-- Recipe Header -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-wrap gap-2 mb-4">
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                <?php echo htmlspecialchars(ucfirst($recipe['category_recipe'])) ?>
            </span>
        </div>

        <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
            <?php echo htmlspecialchars($recipe['name_recipe']) ?>
        </h1>
        
        <p class="text-xl text-gray-600 mb-6 leading-relaxed">
            <?php echo htmlspecialchars($recipe['desc_recipe']) ?>
        </p>

        <!-- Recipe Meta -->
        <div class="flex flex-wrap items-center gap-6 text-gray-500 mb-6">
            <div class="flex items-center gap-1">
                <i class="far fa-clock text-primary-600"></i>
                <span class="text-sm"><?php echo html_entity_decode($recipe['cooking_time_recipe'])?> minit</span>
            </div>
            <div class="flex items-center gap-1">
                <i class="fa fa-fire-alt text-primary-600"></i>
                <span class="text-sm"><?php echo html_entity_decode($recipe['calories_recipe'])?> kalori/hidangan</span>
            </div>
            <div class="flex items-center gap-1">
                <i class="far fa-calendar text-primary-600"></i>
                <span class="text-sm"><?php echo formatDate($recipe['created_date_recipe']) ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="md:flex md:flex-wrap lg:gap-3 gap-2">

            <?php

                $user_recipe_sql = $connect->prepare("SELECT pfp_user, name_user, created_date_user, id_user FROM users WHERE id_user = :id_user");
                $user_recipe_sql->execute([
                    ":id_user" => $recipe['id_user']
                ]);
                $user_recipe = $user_recipe_sql->fetch(PDO::FETCH_ASSOC);

                $created_date_user = date_create($user_recipe['created_date_user']);
                
                if($user['id_user'] != $user_recipe['id_user']){
                    ?>
                    <div class="flex items-center gap-4">
                        <img class="w-10 h-10 rounded-full" src="<?php echo !empty($user_recipe['pfp_user']) ? $location_index .'/uploads/profiles/'.$user_recipe['pfp_user'] : 'https://avatar.iran.liara.run/username?username=' . $user_recipe['name_user'] ; ?>" alt="Profile Picture">
                        <div class="font-medium">
                            <div><?php echo htmlspecialchars($user_recipe['name_user'])?></div>
                            <div class="text-sm text-gray-500">Joined in <?php echo date_format($created_date_user ,"M Y")?></div>
                        </div>
                    </div>
                    <?php
                }

            ?>
            <br>

                <div class="flex flex-wrap lg:gap-3 gap-2">

                    <!-- likes -->
                    <form method="POST" action="<?php echo $location_index?>/backend/recipe.php">
        
                        <?php 
        
                            $likes_sql = $connect->prepare("SELECT id_like FROM likes WHERE id_user = :id_user AND id_recipe = :id_recipe LIMIT 1");
                            $likes_sql->execute([
                                ":id_user" => $user['id_user'],
                                ":id_recipe" => $recipe['id_recipe']
                            ]);
                            
                            if($likes_sql->rowCount() > 0){
                                $user_has_liked = true;
                                $status = "dislike";
                                $icon = "fa";
                            }
                            else{
                                
                                $status = "like";
                                $icon = "far";
                            }
        
                        ?>
                        <input type="hidden" name="token" value='<?php echo $token?>'>
                        <input type="hidden" name="id_recipe" value="<?php echo $recipe['id_recipe']?>">
                        <input type="hidden" name="type" value="<?php echo $status?>">
        
                        <button 
                            type="submit" 
                            name="like_recipe"
                            class="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium <?php echo $user_has_liked ? 'bg-red-50 text-red-600 border-red-200' : 'bg-white text-gray-700 border-gray-300 hover:bg-red-50 hover:text-red-600 hover:border-red-200'; ?>"
                        >
                            <i class="<?php echo $icon?> fa-heart mr-2"></i>
                            Suka (<?php echo $recipe['num_likes_recipe']?>)
                        </button>
                    </form>
        
                    <!-- bookmark -->
                    <form method="POST" action="<?php echo $location_index?>/backend/recipe.php">
        
                        <?php 
        
                            $bookmarks_sql = $connect->prepare("SELECT id_bookmark FROM bookmarks WHERE id_user = :id_user AND id_recipe = :id_recipe LIMIT 1");
                            $bookmarks_sql->execute([
                                ":id_user" => $user['id_user'],
                                ":id_recipe" => $recipe['id_recipe']
                            ]);
                            
                            if($bookmarks_sql->rowCount() > 0){
                                $user_has_bookmarked = true;
                                $status = "disbookmark";
                                $icon = "fa";
                            }
                            else{
                                $status = "bookmark";
                                $icon = "far";
                            }
        
                        ?>
                        <input type="hidden" name="token" value='<?php echo $token?>'>
                        <input type="hidden" name="id_recipe" value="<?php echo $recipe['id_recipe']?>">
                        <input type="hidden" name="type" value="<?php echo $status?>">
        
                        <button 
                            type="submit" 
                            name="bookmark_recipe"
                            class="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium <?php echo $user_has_bookmarked ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200'; ?>"
                        >
                            <i class="<?php echo $icon?> fa-bookmark mr-2"></i>
                            Simpan Resipi
                        </button>
                    </form>
        
                    <button data-tooltip-target="tooltip-url-shortener" data-copy-to-clipboard-target="url-shortener" class="focus:ring-4 focus:ring-primary-300 focus:bg-primary-50 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-green-50 hover:text-green-600 hover:border-green-200">
                        <i class="far fa-share-square mr-2"></i>
                        <input id="url-shortener" type="text" aria-describedby="helper-text-explanation" class="hidden" value="<?php echo $domain . "/user/resipi/?id=" . $recipe['id_recipe'] ?>" readonly disabled />
                        Kongsi
                    </button>
                    <div id="tooltip-url-shortener" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                        <span id="default-tooltip-message">Copy link</span>
                        <span id="success-tooltip-message" class="hidden">Copied!</span>
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                    <a href="<?php echo $location_index?>/user/resipi/cetak.php?id=<?php echo $recipe['id_recipe']?>">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200">
                            <i class="fa fa-print mr-2"></i>
                            Cetak
                        </button>
                    </a>

                    <?php 

                        if($user['id_user'] == $user_recipe['id_user']){
                            ?>
                            <a href="<?php echo $location_index?>/user/resipi/kemaskini.php?id=<?php echo $recipe['id_recipe'] ?>">
                                <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-primary-600">
                                    <i class="fa fa-pencil mr-2"></i>
                                    Kemaskini
                                </button>
                            </a>

                            <form action="<?php echo $location_index?>/backend/recipe.php" method="post">
                                <input type="hidden" name="token" value="<?php echo $token?>">
                                <input type="hidden" name="id_recipe" value="<?php echo $recipe['id_recipe']?>">

                                <button type="submit" name="delete_recipe" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-red-600">
                                    <i class="fa fa-trash mr-2"></i>
                                    Hapuskan
                                </button>
                            </form>
                            <?php
                        }

                    ?>
                </div>
            
        </div>

    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Ingredients Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-8">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-primary-600">
                    Bahan-bahan
                </h2>


                <div class="space-y-3">
                    <?php

                        // Decode the JSON string into PHP array
                        $ingredientData = $recipe['ingredient_recipe'];

                        // First, decode HTML entities to convert &quot; back to regular quotes
                        $decodedData = html_entity_decode($ingredientData, ENT_QUOTES, 'UTF-8');

                        $ingredients = json_decode($decodedData, true);

                        if ($ingredients && is_array($ingredients)) {
                            
                            foreach ($ingredients as $ingredient) {
                                echo '<div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">';
                                echo '<div class="w-2 h-2 rounded-full bg-primary-600 mt-2 shrink-0"></div>';
                                echo '<span class="text-sm leading-relaxed">'. htmlspecialchars($ingredient['quantity']) .' '. htmlspecialchars($ingredient['unit']) .' '. htmlspecialchars($ingredient['name']) .'</span>';
                                echo '</div>';
                            }
                            
                        } 
                        else {
                            echo "<p>Tidak ada bahan-bahan yang ditemukan.</p>";
                            // Debug: check for JSON errors
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                echo "<p>Error: " . json_last_error_msg() . "</p>";
                            }
                        }

                    ?>
                </div>

                <!-- <hr class="my-6 border-gray-200"> -->

                <!-- Nutrition Info -->
                <!-- <div class="space-y-3">
                    <h3 class="font-semibold flex items-center gap-2 text-amber-600">
                        <i class="far fa-star"></i>
                        Maklumat Pemakanan (setiap hidangan)
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-100 rounded-lg p-3 text-center nutrition-card">
                            <div class="font-semibold text-lg">350</div>
                            <div class="text-gray-500 text-xs">Kalori</div>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-3 text-center nutrition-card">
                            <div class="font-semibold text-lg">12g</div>
                            <div class="text-gray-500 text-xs">Protein</div>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-3 text-center nutrition-card">
                            <div class="font-semibold text-lg">45g</div>
                            <div class="text-gray-500 text-xs">Karbohidrat</div>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-3 text-center nutrition-card">
                            <div class="font-semibold text-lg">15g</div>
                            <div class="text-gray-500 text-xs">Lemak</div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Recipe Image -->
            <div class="mb-8">
                <div class="relative rounded-2xl overflow-hidden shadow-lg">
                    <img 
                        src="<?php echo htmlspecialchars(formatImagePath($recipe['image_recipe'], "../../"))?>" 
                        alt="<?php echo htmlspecialchars($recipe['name_recipe'])?>"
                        class="w-full h-[350px] object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-primary-600">
                    <i class="far fa-chef-hat"></i>
                    Arahan Memasak
                </h2>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <p class="text-gray-800 leading-relaxed">
                                <?php echo htmlspecialchars($recipe['tutorial_recipe'])?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-700 rounded-xl shadow-md p-6 text-center mb-8">
                <div class="w-full h-[350px] object-cover">
                    <iframe 
                        src="<?php echo htmlspecialchars($recipe['url_resource_recipe'])?>" 
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                        class="w-full h-[350px] object-cover"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>

            
            <!-- Comments Section -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-primary-600">
                    <i class="far fa-comments"></i>
                    Komen
                    <span class="text-gray-500 text-lg font-normal">(<?php echo count($comments); ?>)</span>
                </h2>
                
                <!-- Comment Form -->
                <div class="mb-8">
                    <?php if (isset($user['id_user'])): ?>
                        <form method="POST" action="<?php echo $location_index?>/backend/recipe.php">
                            <input type="hidden" name="token" value="<?php echo $token?>">
                            <input type="hidden" name="id_recipe" value="<?php echo $recipe['id_recipe']?>">

                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <?php 
                                        // Get current user's avatar
                                        $user_sql = $connect->prepare("SELECT pfp_user, name_user FROM users WHERE id_user = :id");
                                        $user_sql->execute([':id' => $user['id_user']]);
                                        $current_user = $user_sql->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <img src="<?php echo !empty($current_user['pfp_user']) ? $location_index .'/uploads/profiles/'.$current_user['pfp_user'] : 'https://avatar.iran.liara.run/username?username=' . $current_user['name_user'] ; ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <textarea 
                                        name="text_comment" 
                                        rows="3" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                        placeholder="Tulis komen anda di sini..."
                                        required
                                    ></textarea>
                                    <?php if (isset($comment_error)): ?>
                                        <p class="text-red-500 text-sm mt-1"><?php echo $comment_error; ?></p>
                                    <?php endif; ?>
                                    <div class="mt-2 flex justify-end">
                                        <button 
                                            type="submit" 
                                            name="comment_recipe"
                                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
                                        >
                                            Hantar Komen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4 border border-gray-200 rounded-lg">
                            <p class="text-gray-600 mb-2">Anda perlu log masuk untuk memberikan komen.</p>
                            <a href="/login.php" class="text-primary-600 hover:underline font-medium">Log Masuk</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Comments List -->
                <div class="space-y-6">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <img 
                                        src="<?php echo !empty($comment['pfp_user']) ? $location_index .'/uploads/profiles/'.$comment['pfp_user'] : 'https://avatar.iran.liara.run/username?username=' . $comment['name_user'] ; ?>" 
                                        alt="<?php echo htmlspecialchars($comment['name_user']); ?>" 
                                        class="w-10 h-10 rounded-full object-cover"
                                    >
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($comment['name_user']); ?></h4>
                                            <span class="text-sm text-gray-500"><?php echo formatDate($comment['created_date_comment']); ?></span>
                                        </div>
                                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($comment['text_comment'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fa fa-comment-slash text-4xl mb-3"></i>
                            <p>Tiada komen lagi. Jadilah yang pertama untuk memberikan komen!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Related Recipes -->
<section class="container mx-auto px-4 py-12">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Resipi Lain yang Mungkin Anda Suka</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <?php

            $recommended_recipe_sql = $connect->prepare("
                SELECT * 
                FROM recipes
                WHERE 
                    (EXISTS (
                        SELECT 1 
                        FROM recipes
                        WHERE 
                            name_recipe LIKE :search OR 
                            desc_recipe LIKE :search OR 
                            category_recipe LIKE :search
                    ) AND (
                        name_recipe LIKE :search OR 
                        desc_recipe LIKE :search OR 
                        category_recipe LIKE :search
                    ))
                    OR
                    (NOT EXISTS (
                        SELECT 1 
                        FROM recipes
                        WHERE 
                            name_recipe LIKE :search OR 
                            desc_recipe LIKE :search OR 
                            category_recipe LIKE :search
                    ))
                ORDER BY 
                    CASE 
                        WHEN (name_recipe LIKE :search OR desc_recipe LIKE :search OR category_recipe LIKE :search) THEN 0 
                        ELSE 1 
                    END,
                    num_likes_recipe DESC
                LIMIT 3;
            ");

            $recommended_recipe_sql->execute([
                ':search' => $recipe['tags_recipe']
            ]);

            while ($recommended_recipe = $recommended_recipe_sql->fetch(PDO::FETCH_ASSOC)) {
                ?>

                <a href="./?id=<?php echo $recommended_recipe['id_recipe']?>">

                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <img src="<?php echo htmlspecialchars(formatImagePath($recommended_recipe['image_recipe'], "../../"))?>" alt="Resipi 1" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($recommended_recipe['name_recipe']) ?></h3>
                            <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($recommended_recipe['desc_recipe']) ?></p>
                            <div class="flex items-center text-sm text-gray-500">
                                <span><i class="far fa-clock mr-1"></i><?php echo htmlspecialchars($recommended_recipe['cooking_time_recipe']) ?> minit</span>
                                <span class="mx-2">•</span>
                                <span><i class="far fa-heart mr-2"></i><?php echo htmlspecialchars($recommended_recipe['num_likes_recipe']) ?></span>
                            </div>
                        </div>
                    </div>
                </a>

                <?php

            }

        ?>
        
    </div>
</section>

<style>
    .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background-color: #3B82F6;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        flex-shrink: 0;
    }
    
    .nutrition-card {
        transition: transform 0.2s;
    }
    
    .nutrition-card:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    // Function to handle like button
    function handleLike() {
        alert('Anda menyukai resipi ini!');
    }

    // Function to handle save button
    function handleSave() {
        alert('Resipi telah disimpan ke kegemaran anda!');
    }

    // Function to handle share button
    function handleShare() {
        alert('Pautan resipi telah disalin ke papan klip!');
    }

    window.addEventListener('load', function() {
        const clipboard = FlowbiteInstances.getInstance('CopyClipboard', 'url-shortener');
        const tooltip = FlowbiteInstances.getInstance('Tooltip', 'tooltip-url-shortener');

        const $defaultIcon = document.getElementById('default-icon');
        const $successIcon = document.getElementById('success-icon');

        const $defaultTooltipMessage = document.getElementById('default-tooltip-message');
        const $successTooltipMessage = document.getElementById('success-tooltip-message');

        clipboard.updateOnCopyCallback((clipboard) => {
            showSuccess();

            // reset to default state
            setTimeout(() => {
                resetToDefault();
            }, 2000);
        })

        const showSuccess = () => {
            $defaultIcon.classList.add('hidden');
            $successIcon.classList.remove('hidden');
            $defaultTooltipMessage.classList.add('hidden');
            $successTooltipMessage.classList.remove('hidden');    
            tooltip.show();
        }

        const resetToDefault = () => {
            $defaultIcon.classList.remove('hidden');
            $successIcon.classList.add('hidden');
            $defaultTooltipMessage.classList.remove('hidden');
            $successTooltipMessage.classList.add('hidden');
            tooltip.hide();
        }
    })

</script>