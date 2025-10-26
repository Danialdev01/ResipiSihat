<?php


use Dompdf\Dompdf;
use Dompdf\Options;

require __DIR__ . "../../../vendor/autoload.php";

$connect = new PDO('mysql:host=localhost;dbname=resepisihat', 'root', 'danialdev');

// Dapatkan jumlah orang dan user_id dari GET request
$jumlah_orang = isset($_GET['jumlah_orang']) ? intval($_GET['jumlah_orang']) : 4;
$user_id = isset($_GET['id_user']) ? intval($_GET['id_user']) : null;

// Semak jika user_id disediakan
if (!$user_id) {
    die("Error: User ID diperlukan");
}


// Dapatkan semua resepi yang di-bookmark oleh pengguna
$sql = "SELECT r.*, b.created_date_bookmark 
        FROM recipes r 
        INNER JOIN bookmarks b ON r.id_recipe = b.id_recipe 
        WHERE b.id_user = :user_id 
        ORDER BY b.created_date_bookmark DESC";

$stmt = $connect->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$bookmarked_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses bahan-bahan dan gabungkan yang sama
$all_ingredients = [];
$total_recipes = count($bookmarked_recipes);

foreach ($bookmarked_recipes as $recipe) {
    $ingredients = json_decode(html_entity_decode($recipe['ingredient_recipe'], ENT_QUOTES, 'UTF-8'), true);
    
    if (is_array($ingredients)) {
        foreach ($ingredients as $ingredient) {
            $key = strtolower(trim($ingredient['name'])) . '_' . $ingredient['unit'];
            
            // Adjust quantity based on number of people
            $adjusted_quantity = $ingredient['quantity'];
            if ($ingredient['unit'] !== 'secukup rasa' && is_numeric($ingredient['quantity'])) {
                $adjusted_quantity = $ingredient['quantity'] * $jumlah_orang;
            }
            
            if (isset($all_ingredients[$key])) {
                // Jika bahan sudah wujud, tambah kuantiti
                if ($ingredient['unit'] !== 'secukup rasa') {
                    $all_ingredients[$key]['quantity'] += $adjusted_quantity;
                }
            } else {
                $all_ingredients[$key] = [
                    'name' => $ingredient['name'],
                    'quantity' => $adjusted_quantity,
                    'unit' => $ingredient['unit']
                ];
            }
        }
    }
}

// Generate HTML content for PDF berdasarkan template
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senarai Belian Bahan Masakan</title>
    <style>
        * {
            font-family: Calibri, sans-serif;
            padding: 0;
            margin: 0;
        }
        td{
            padding: 2px;
            font-size: 1rem;
        }
        #bahan table,#bahan th,#bahan td {
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        #info table,#info th,#info td {
            font-size: 0.85rem;
        }
        #bahan td{
            padding: 2px;
            padding-left: 15px;
            padding-right: 15px;
            text-align: center;
        }
        #bahan table{
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        hr{
            width: 200px;
            border-style: dashed;
        }
        .main-container {
            padding: 20px;
        }
        .header-container {
            text-align: center;
            padding: 40px;
        }
        .header h2 {
            border: 1px solid black;
            padding-top: 3px;
            padding-bottom: 3px;
            width: 220px !important;
            margin: 0 auto;
        }
        .table-container {
            padding: 40px;
        }
        .bahan-table-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-container">
            <div class="header">
                <h2>SENARAI BELIAN</h2>
            </div>
        </div>

        <center>
            <p>Senarai Bahan Masakan untuk ' . $jumlah_orang . ' Orang</p>
        </center>

        <div id="info" class="table-container">
            <table>
                <tr>
                    <td><b>TARIKH CETAK</b></td>
                    <td><b>:</b></td>
                    <td><b>' . date('d/m/Y H:i') . '</b></td>
                </tr>
                <tr>
                    <td><b>JUMLAH RESEPI</b></td>
                    <td><b>:</b></td>
                    <td><b>' . $total_recipes . ' resepi</b></td>
                </tr>
                <tr>
                    <td><b>JUMLAH ORANG</b></td>
                    <td><b>:</b></td>
                    <td><b>' . $jumlah_orang . ' orang</b></td>
                </tr>
            </table>
        </div>

        <div id="bahan" class="bahan-table-container">
            <table style="border-collapse: collapse; border: 1px solid black;">
                <tbody>
                    <tr>
                        <td><b>BIL</b></td>
                        <td><b>BAHAN / ALATAN</b></td>
                        <td><b>KUANTITI</b></td>
                        <td><b>UNIT</b></td>
                        <td><b>CATATAN</b></td>
                    </tr>';

$bil = 1;
foreach ($all_ingredients as $ingredient) {
    $catatan = '';
    
    if ($ingredient['unit'] === 'secukup rasa') {
        $kuantiti = 'Secukup Rasa';
        $unit = '-';
        $catatan = 'Gunakan mengikut citarasa';
    } else {
        $kuantiti = $ingredient['quantity'];
        $unit = $ingredient['unit'];
        
        // Format quantity jika nombor perpuluhan
        if (is_numeric($kuantiti) && floor($kuantiti) != $kuantiti) {
            $kuantiti = number_format($kuantiti, 2);
        }
    }
    
    $html .= '
                    <tr>
                        <td>' . $bil++ . '</td>
                        <td>' . htmlspecialchars(ucwords($ingredient['name'])) . '</td>
                        <td>' . $kuantiti . '</td>
                        <td>' . $unit . '</td>
                        <td>' . $catatan . '</td>
                    </tr>';
}

$html .= '
                </tbody>
            </table>
        </div>

        <br>
        
        <div style="padding: 40px;">
            <h3>Senarai Resepi:</h3>
            <ul>';
foreach ($bookmarked_recipes as $recipe) {
    $html .= '<li>' . htmlspecialchars($recipe['name_recipe']) . ' (' . $recipe['cooking_time_recipe'] . ' minit)</li>';
}
$html .= '
            </ul>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <hr>
            <p style="font-size: 0.8rem; color: #666; margin-top: 10px;">
                Dihasilkan oleh Sistem Resepi Masakan pada ' . date('d/m/Y') . '
            </p>
        </div>
    </div>
</body>
</html>';

// Setup Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Calibri');

$dompdf = new Dompdf($options);
$dompdf->setPaper("A4", "portrait");

$dompdf->loadHtml($html);
$dompdf->render();

// Output PDF
$dompdf->stream("senarai_belian_" . date('Y-m-d') . ".pdf", [
    "Attachment" => 0
]);

exit;
?>