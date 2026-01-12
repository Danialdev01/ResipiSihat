<?php
session_start();

use Dompdf\Dompdf;
use Dompdf\Options;


$connect = new PDO('mysql:host=localhost;dbname=danialir_resipisihat', 'danialir_danial', 'laksjdlasAdjasl@!');
// $connect = new PDO('mysql:host=localhost;dbname=resipisihat', 'root', 'danialdev');

require __DIR__ . "../../../vendor/autoload.php";

// Dapatkan ID resipi dari URL
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$recipe_id) {
    die("Error: ID resipi diperlukan");
}

// Dapatkan data resipi
$sql = "SELECT r.*, u.name_user 
        FROM recipes r 
        LEFT JOIN users u ON r.id_user = u.id_user 
        WHERE r.id_recipe = :recipe_id";
$stmt = $connect->prepare($sql);
$stmt->execute([':recipe_id' => $recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

// Semak jika resipi wujud
if (!$recipe) {
    die("Error: Resipi tidak ditemukan");
}

// Decode bahan-bahan
$ingredients = json_decode(html_entity_decode($recipe['ingredient_recipe'], ENT_QUOTES, 'UTF-8'), true);

// Function untuk format tarikh
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d/m/Y');
}

// Generate HTML content untuk PDF
$html = '
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($recipe['name_recipe']) . '</title>
    <style>
        * {
            font-family: Calibri, Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .header h1 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .header p {
            font-size: 16px;
            color: #7f8c8d;
            font-style: italic;
        }
        .recipe-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .meta-item {
            text-align: center;
        }
        .meta-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
        .meta-value {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            color: #e74c3c;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ecf0f1;
            text-transform: uppercase;
        }
        .ingredients-list {
            list-style: none;
            padding: 0;
        }
        .ingredient-item {
            padding: 8px 0;
            border-bottom: 1px dotted #ecf0f1;
            display: flex;
            align-items: center;
        }
        .ingredient-item:before {
            content: "•";
            color: #e74c3c;
            font-weight: bold;
            margin-right: 10px;
        }
        .quantity {
            font-weight: bold;
            margin-right: 5px;
        }
        .unit {
            margin-right: 5px;
        }
        .instructions {
            white-space: pre-line;
            line-height: 1.8;
            font-size: 14px;
        }
        .instructions ol {
            margin-left: 20px;
        }
        .instructions li {
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #7f8c8d;
        }
        .recipe-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        .author {
            font-style: italic;
            color: #6c757d;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>' . htmlspecialchars($recipe['name_recipe']) . '</h1>
            <p>' . htmlspecialchars($recipe['desc_recipe']) . '</p>
        </div>

        <div class="recipe-info">
            <p><strong>Dihasilkan oleh:</strong> ' . htmlspecialchars($recipe['name_user']) . '</p>
            <p><strong>Tarikh Dihasilkan:</strong> ' . formatDate($recipe['created_date_recipe']) . '</p>
        </div>

        <div class="recipe-meta">
            <div class="meta-item">
                <div class="meta-label">Masa Masak</div>
                <div class="meta-value">' . $recipe['cooking_time_recipe'] . ' minit</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Kategori</div>
                <div class="meta-value">' . ucfirst($recipe['category_recipe']) . '</div>
            </div>';
            
if (!empty($recipe['calories_recipe'])) {
    $html .= '
            <div class="meta-item">
                <div class="meta-label">Kalori</div>
                <div class="meta-value">' . $recipe['calories_recipe'] . ' kcal</div>
            </div>';
}

$html .= '
        </div>

        <div class="section">
            <h2 class="section-title">📝 Bahan-bahan</h2>';
            
if (is_array($ingredients) && count($ingredients) > 0) {
    $html .= '<ul class="ingredients-list">';
    foreach ($ingredients as $ingredient) {
        $quantity = ($ingredient['unit'] === 'secukup rasa') ? 'Secukup rasa' : $ingredient['quantity'];
        $unit = ($ingredient['unit'] === 'secukup rasa') ? '' : $ingredient['unit'];
        
        $html .= '
                <li class="ingredient-item">
                    <span class="quantity">' . $quantity . '</span>
                    <span class="unit">' . $unit . '</span>
                    <span class="name">' . htmlspecialchars($ingredient['name']) . '</span>
                </li>';
    }
    $html .= '</ul>';
} else {
    $html .= '<p>Tiada bahan-bahan disenaraikan.</p>';
}

$html .= '
        </div>

        <div class="section">
            <h2 class="section-title">👨‍🍳 Cara Penyediaan</h2>
            <div class="instructions">' . nl2br(htmlspecialchars($recipe['tutorial_recipe'])) . '</div>
        </div>';

// Tambahkan nota jika ada video
if (!empty($recipe['url_resource_recipe'])) {
    $html .= '
        <div class="section">
            <h2 class="section-title">🎥 Video Tutorial</h2>
            <p>Video tutorial tersedia di: ' . htmlspecialchars($recipe['url_resource_recipe']) . '</p>
        </div>';
}

$html .= '
        <div class="footer">
            <p>Dicetak dari ResipiSihat pada ' . date('d/m/Y H:i') . '</p>
            <p>© ' . date('Y') . ' ResipiSihat - Semua hak cipta terpelihara</p>
        </div>
    </div>
</body>
</html>';

// Setup Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Calibri');
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->setPaper("A4", "portrait");

$dompdf->loadHtml($html);
$dompdf->render();

// Output PDF
$filename = "resipi_" . preg_replace('/[^a-zA-Z0-9]/', '_', $recipe['name_recipe']) . "_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, [
    "Attachment" => 0
]);

exit;
?>