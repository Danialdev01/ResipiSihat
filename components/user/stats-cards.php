<?php

function getUserRecipeLikes($userId, $connect) {
    $sql = "SELECT SUM(num_likes_recipe) as total_likes FROM recipes WHERE id_user = :user_id AND status_recipe = 1";
    $stmt = $connect->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total_likes'] ? $result['total_likes'] : 0;
}

?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <a href="<?php echo $location_index?>/user/resipi/saya.php">
        <div class="card bg-white rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-500">Jumlah Resipi</div>

                    <?php 
                        $bil_resepi_sql = $connect->prepare("SELECT COUNT(*) AS bil_resepi, created_date_recipe FROM recipes WHERE id_user = :id_user");
                        $bil_resepi_sql->execute([
                            ":id_user" => $user['id_user']
                        ]);
                        $bil_resepi = $bil_resepi_sql->fetch(PDO::FETCH_ASSOC);

                    ?>
                    <div class="text-2xl font-bold mt-1"><?php echo $bil_resepi['bil_resepi'] ?></div>
                </div>
                <div class="bg-primary-100 p-3 rounded-lg">
                    <i class="fas fa-book text-primary-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </a>
    
    <div class="card bg-white rounded-xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500">Pesanan Aktif</div>
                <?php
                    $bil_pesanan_sql = $connect->prepare("SELECT COUNT(*) AS bil_pesanan FROM orderings WHERE id_user = :id_user");
                    $bil_pesanan_sql->execute([
                        ":id_user" => $user['id_user']
                    ]);
                    $bil_pesanan = $bil_pesanan_sql->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="text-2xl font-bold mt-1"><?php echo $bil_pesanan['bil_pesanan'] ?></div>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-calendar-check text-green-700 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="card bg-white rounded-xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500">Resipi disuka orang lain</div>
                <div class="text-2xl font-bold mt-1"><?php echo getUserRecipeLikes($user['id_user'], $connect)?></div>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-heart text-blue-700 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="card bg-white rounded-xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-gray-500">Pengikut</div>
                <?php 

                    $bil_follow_sql = $connect->prepare("SELECT COUNT(*) AS bil_follow FROM follows WHERE id_user = :id_user");
                    $bil_follow_sql->execute([
                        "id_user" => $user['id_user']
                    ]);

                    $bil_follow = $bil_follow_sql->fetch(PDO::FETCH_ASSOC);

                ?>
                <div class="text-2xl font-bold mt-1"><?php echo $bil_follow['bil_follow']?></div>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-user-group text-purple-700 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<br>