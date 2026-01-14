<?php 

function createRecipe($id_user, $name_recipe, $image_recipe, $desc_recipe, $category_recipe, $tutorial_recipe, $ingredient_recipe, $cooking_time_recipe, $url_resource_recipe, $visibility_recipe, $connect){

    try{
        
        $file = uploadFile(uniqid(), $image_recipe, 'recipes');
        
        $tag_recipe = "makanan";

        $calories_recipe = 400;

        $file = json_decode($file, true);

        if($file['status'] == "success"){

            $create_recipe_sql = $connect->prepare("
                INSERT INTO recipes(id_recipe, id_user, name_recipe, desc_recipe, image_recipe, category_recipe, tutorial_recipe, ingredient_recipe, calories_recipe, cooking_time_recipe, url_resource_recipe, visibility_recipe, num_likes_recipe, tags_recipe, created_date_recipe, status_recipe) 
                VALUES 
                (NULL, :id_user, :name_recipe, :desc_recipe, :image_recipe, :category_recipe, :tutorial_recipe, :ingredient_recipe, :calories_recipe, :cooking_time_recipe, :url_resource_recipe, :visibility_recipe, 0, :tags_recipe, NOW(), 1)
            ");

            $create_recipe_sql->execute([
                ":id_user" => $id_user,
                ":name_recipe" => $name_recipe,
                ":desc_recipe" => $desc_recipe,
                ":image_recipe" => $file['file_name'],
                ":category_recipe" => $category_recipe,
                ":tutorial_recipe" => $tutorial_recipe,
                ":ingredient_recipe" => $ingredient_recipe,
                ":calories_recipe" => $calories_recipe,
                ":cooking_time_recipe" => $cooking_time_recipe,
                ":url_resource_recipe" => $url_resource_recipe,
                ":visibility_recipe" => $visibility_recipe,
                ":tags_recipe" => $tag_recipe
            ]);


            $status = encodeObj("200", "Loggin Success", "success");

            $recipe_value = [
                "id_recipe" => $connect->lastInsertId(),
                "id_user" => $id_user,
                "name_recipe" => $name_recipe,
            ];
                
            $recipe_value = json_encode($recipe_value);
            return addJson($status, $recipe_value);

        }
        else{
            return encodeObj("400", "Ralat gambar", "error");

        }

    }
    catch(Exception $e){

        return encodeObj("400", "Ralat hasilkan resipi. $e", "error");

    }

}

function editRecipe($id_recipe, $id_user, $name_recipe, $image_recipe, $desc_recipe, $category_recipe, $tutorial_recipe, $ingredient_recipe, $calories_recipe, $cooking_time_recipe, $url_resource_recipe, $visibility_recipe, $tags_recipe, $current_image, $connect){
    
    try{
        // First, check if the recipe exists and belongs to the user
        $check_sql = $connect->prepare("SELECT id_user, image_recipe FROM recipes WHERE id_recipe = ?");
        $check_sql->execute([$id_recipe]);
        $recipe = $check_sql->fetch(PDO::FETCH_ASSOC);
        
        if(!$recipe) {
            return encodeObj("404", "Resipi tidak ditemui", "error");
        }
        
        // Check if user owns this recipe
        if($recipe['id_user'] != $id_user) {
            return encodeObj("403", "Anda tidak dibenarkan mengedit resipi ini!", "error");
        }
        
        $image_filename = $current_image; // Default to current image
        
        // Check if a new image was uploaded
        if($image_recipe && isset($image_recipe['error']) && $image_recipe['error'] == 0 && $image_recipe['size'] > 0) {
            // Upload new image
            $file = uploadFile(uniqid(), $image_recipe, 'recipes');
            $file = json_decode($file, true);
            
            if($file['status'] == "success") {
                // Delete old image if exists
                if($current_image && $current_image != '' && file_exists("../../uploads/recipes/" . $current_image)) {
                    unlink("../../uploads/recipes/" . $current_image);
                }
                $image_filename = $file['file_name'];
            } else {
                return encodeObj("400", "Ralat gambar: " . $file['message'], "error");
            }
        }
        
        // Validate and clean the ingredient_recipe JSON
        if (!empty($ingredient_recipe)) {
            // Check if it's already a JSON string
            $decoded_ingredients = json_decode($ingredient_recipe, true);
            
            if ($decoded_ingredients === null && json_last_error() !== JSON_ERROR_NONE) {
                // If not valid JSON, try to fix encoding issues
                $ingredient_recipe = html_entity_decode($ingredient_recipe, ENT_QUOTES, 'UTF-8');
                
                // If there's still encoding issues, decode again
                if (strpos($ingredient_recipe, '&amp;') !== false) {
                    $ingredient_recipe = html_entity_decode($ingredient_recipe, ENT_QUOTES, 'UTF-8');
                }
                
                // Now try to decode again
                $decoded_ingredients = json_decode($ingredient_recipe, true);
                
                if ($decoded_ingredients === null) {
                    // If still not valid, create empty array
                    $ingredient_recipe = json_encode([]);
                } else {
                    // Re-encode as clean JSON
                    $ingredient_recipe = json_encode($decoded_ingredients, JSON_UNESCAPED_UNICODE);
                }
            } else {
                // It's already valid JSON, ensure it's properly encoded
                $ingredient_recipe = json_encode($decoded_ingredients, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $ingredient_recipe = json_encode([]);
        }
        
        // Clean up tags if they exist
        if (empty($tags_recipe)) {
            $tags_recipe = "makanan"; // Default tag
        }
        
        // Update recipe in database
        $update_recipe_sql = $connect->prepare("
            UPDATE recipes SET 
                name_recipe = :name_recipe,
                desc_recipe = :desc_recipe,
                image_recipe = :image_recipe,
                category_recipe = :category_recipe,
                tutorial_recipe = :tutorial_recipe,
                ingredient_recipe = :ingredient_recipe,
                calories_recipe = :calories_recipe,
                cooking_time_recipe = :cooking_time_recipe,
                url_resource_recipe = :url_resource_recipe,
                visibility_recipe = :visibility_recipe,
                tags_recipe = :tags_recipe
            WHERE id_recipe = :id_recipe AND id_user = :id_user
        ");
        
        $success = $update_recipe_sql->execute([
            ":id_recipe" => $id_recipe,
            ":id_user" => $id_user,
            ":name_recipe" => $name_recipe,
            ":desc_recipe" => $desc_recipe,
            ":image_recipe" => $image_filename,
            ":category_recipe" => $category_recipe,
            ":tutorial_recipe" => $tutorial_recipe,
            ":ingredient_recipe" => $ingredient_recipe,
            ":calories_recipe" => $calories_recipe,
            ":cooking_time_recipe" => $cooking_time_recipe,
            ":url_resource_recipe" => $url_resource_recipe,
            ":visibility_recipe" => $visibility_recipe,
            ":tags_recipe" => $tags_recipe
        ]);
        
        if($success) {
            $status = encodeObj("200", "Resipi berjaya dikemaskini", "success");
            
            $recipe_value = [
                "id_recipe" => $id_recipe,
                "id_user" => $id_user,
                "name_recipe" => $name_recipe,
            ];
                
            $recipe_value = json_encode($recipe_value);
            return addJson($status, $recipe_value);
        } else {
            // Get error info for debugging
            $errorInfo = $update_recipe_sql->errorInfo();
            return encodeObj("400", "Gagal mengemaskini resipi. Error: " . $errorInfo[2], "error");
        }
        
    } catch(Exception $e) {
        return encodeObj("400", "Ralat mengemaskini resipi. " . $e->getMessage(), "error");
    }
}

//@ Update num 
function updateNumLikesRecipe($id_recipe, $num, $connect){

    $recipe_sql = $connect->prepare("SELECT num_likes_recipe FROM recipes WHERE id_recipe = :id_recipe");
    $recipe_sql->execute([":id_recipe" => $id_recipe]);
    $recipe = $recipe_sql->fetch(PDO::FETCH_ASSOC);
    $new_num = $recipe['num_likes_recipe'] + $num;
    
    // update like counter recipe
    $update_like_recipe_sql = $connect->prepare("UPDATE recipes SET num_likes_recipe = :num_likes_recipe WHERE id_recipe = :id_recipe");
    $update_like_recipe_sql->execute([
        ":num_likes_recipe" => $new_num,
        ":id_recipe" => $id_recipe
    ]);

}

//@ Like Recipe
function likeRecipe($id_recipe, $id_user, $type, $connect){


    try{

        if($type == "like"){

            // add new like
            $like_recipe_sql = $connect->prepare("INSERT INTO likes (id_user, id_recipe, id_comment, created_date_like) VALUES (:id_user, :id_recipe, NULL, NOW())");
            $like_recipe_sql->execute([
                ":id_user" => $id_user,
                ":id_recipe" => $id_recipe
            ]);

            updateNumLikesRecipe($id_recipe, 1 , $connect);

            return encodeObj("200", "Like resipi", "success");

        }
        else if($type == "dislike"){

            $delete_like_recipe_sql = $connect->prepare("DELETE FROM likes WHERE id_recipe = :id_recipe AND id_user = :id_user"); 
            $delete_like_recipe_sql->execute([
                ":id_recipe" => $id_recipe,
                ":id_user" => $id_user
            ]);

            updateNumLikesRecipe($id_recipe, -1, $connect);

            return encodeObj("200", "Dislike resipi", "success");

        }
        else{
            $connect->rollBack();
            return encodeObj("400", "Error type like", "error");

        }


    }
    catch(Exception $e){

        return encodeObj("400", "Ralat like resipi", "error");

    }

}

//@ Bookmark Recipe
function bookmarkRecipe($id_recipe, $id_user, $type, $connect){


    try{

        if($type == "bookmark"){

            // add new like
            $bookmark_recipe_sql = $connect->prepare("INSERT INTO bookmarks (id_user, id_recipe, created_date_bookmark) VALUES (:id_user, :id_recipe, NOW())");
            $bookmark_recipe_sql->execute([
                ":id_user" => $id_user,
                ":id_recipe" => $id_recipe
            ]);

            return encodeObj("200", "Simpan resipi", "success");

        }
        else if($type == "disbookmark"){

            $delete_like_recipe_sql = $connect->prepare("DELETE FROM bookmarks WHERE id_recipe = :id_recipe AND id_user = :id_user"); 
            $delete_like_recipe_sql->execute([
                ":id_recipe" => $id_recipe,
                ":id_user" => $id_user
            ]);

            return encodeObj("200", "Buang simpanan resipi", "success");

        }
        else{
            $connect->rollBack();
            return encodeObj("400", "Error type like", "error");

        }


    }
    catch(Exception $e){

        return encodeObj("400", "Ralat simpan resipi. $e", "error");

    }

}

//delete bookmark
function deleteBookmarkRecipe($id_recipe, $id_user, $connect){

    try{

        $delete_bookmark_sql = $connect->prepare("DELETE FROM bookmarks WHERE id_recipe = :id_recipe AND id_user = :id_user"); 
        $delete_bookmark_sql->execute([
            ":id_recipe" => $id_recipe,
            ":id_user" => $id_user
        ]);

        return encodeObj("200", "Berjaya buang simpanan resipi", "success");

    }
    catch(Exception $e){

        return encodeObj("400", "Ralat buang simpanan resipi. $e", "error");

    }

}

//@ Comment Recipe
function commentRecipe($id_recipe, $id_user, $text_comment, $connect){

    try{

        if(!empty($text_comment)){

            $insert_comment = $connect->prepare("
                INSERT INTO comments (id_user, id_recipe, text_comment, created_date_comment, status_comment) 
                VALUES (:user_id, :recipe_id, :comment_text, NOW(), 1)
            ");

            $insert_comment->execute([
                ':user_id' => $id_user,
                ':recipe_id' => $id_recipe,
                ':comment_text' => $text_comment
            ]);

            return encodeObj("200", "Berjaya komen resipi", "success");


        }
        else{

            return encodeObj("400", "Sila isi komen sebelum menghantar.", "error");
        }

    }
    catch(Exception $e){
        return encodeObj("400", "Ralat komen resipi. $e", "error");

    }

}