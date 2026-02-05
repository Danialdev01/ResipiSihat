<?php
// Start session if not already started

include('../config/connect.php');

// API Key Groq (ganti dengan API key Anda)
$groqApiKey = $ai_api_key;

// Fungsi untuk berinteraksi dengan Groq AI
function chatWithAI($messages) {
    global $groqApiKey;
    $url = 'https://api.groq.com/openai/v1/chat/completions';
    
    $data = [
        "model" => "llama-3.1-8b-instant",
        "messages" => $messages,
        "temperature" => 0.7,
        "max_tokens" => 1024,
        "top_p" => 1,
        "frequency_penalty" => 0,
        "presence_penalty" => 0
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $groqApiKey
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Fungsi BARU: Ekstrak kata kunci dari percakapan
function extractKeywords($conversation) {
    global $groqApiKey;
    $url = 'https://api.groq.com/openai/v1/chat/completions';
    
    // Format percakapan untuk analisis
    $formattedConv = "";
    foreach ($conversation as $msg) {
        if ($msg['role'] == 'user') {
            $formattedConv .= "Pengguna: " . $msg['content'] . "\n";
        } else if ($msg['role'] == 'assistant') {
            $formattedConv .= "AI: " . $msg['content'] . "\n";
        }
    }
    
    $messages = [
        [
            "role" => "system",
            "content" => '
                        Pastikan output dalam Bahasa Melayu.
                        Berdasarkan input pengguna, pastikan kata kunci tersebut mempunyai kaitan dengan percakapan pengguna.
                        Fokus pada: bahan utama, jenis masakan, kategori (contoh: ayam, sayur, rendah kalori, vegetarian, makanan Itali). 
                        Keluarkan HANYA kata kunci penting dalam bentuk senarai dipisahkan koma.
                        Berikan jenis bahan yang terlibat, jenis kategori makanan, kata kunci makanan, dan jenis makanan.

                        Contoh sekiranya makanan tinggi kalori ("berikan saya contoh resipi yang berasaskan ayam"): MAKA output tersebut akan menjadi "ayam, tinggi protein, makanan tengahhari, protein"

                        Contoh sekiranya makanan rendah kalori ("berikan saya contoh resipi yang boleh merendahkan berat badan"): "salad, rendah kalori, makanan tengahhari, sayur"

                        Contoh sekiranya makanan mengikut kaedah masakan ("berikan saya contoh resipi melibatkan kaedah goreng"): "goreng, makanan tengahhari"

                        Contoh sekiranya makanan mengikut jenis bahan ("berikan saya contoh resipi nasi"): "nasi, makanan tengahhari, kabohidrat"

                        Sekiranya pengguna masukkan input "Saya hendak kuruskan badan, apa contoh resipi yang sesuai" MAKA output "salad, sayuran, rendah kalori"

                        Sekiranya pengguna masukkan input "Saya hendak bina badan, apa contoh resipi yang sesuai" MAKA output "ayam, ikan, tinggi kalori"

                        PENTING: Berikan kata kunci dalam bentuk perkataan SAHAJA.
                        JANGAN libatkan cara pemasakan KECUALI sekiranya pengguna menyatakan ia secara spesifik.
                        PENTING: Hanya berikan output dalam bentuk perkataan kecil seperti contoh : "ayam, tinggi kalori, ikan,"
                        PASTIKAN Bahan utama terdapat dalam kata kunci utama.
                        SEKIRANYA pengguna tidak menyatakan spesifik kaedah masakan seperti goreng MAKA JANGAN letakkan kaedah masakan sebagai kata kunci seperti goreng, rebus.
                        CONTOH YANG SALAH : "rendah kalori * protein" ATAU " makanan tengahhari * nasi " pastikan kata kunci tersebut dalam bentuk perkataan seperti "nasi, makanan tengahhari, kabohidrat"
                        PASTIKAN di dalam output tiada simbol lain seperti * atau \ atau "

                        JANGAN BUAT AYAT PENDAHULUAN, TERUS BAGI KATA KUNCI.
                        '
        ],
        [
            "role" => "user",
            "content" => 'Dari percakapan berikut, ekstrak 3-5 kata kunci utama untuk pencarian resipi: Pastikan output dalam Bahasa Melayu.
                        Berdasarkan input pengguna, pastikan kata kunci tersebut mempunyai kaitan dengan percakapan pengguna.
                        Fokus pada: bahan utama, jenis masakan, kategori (contoh: ayam, sayur, rendah kalori, vegetarian, makanan Itali). 
                        Keluarkan HANYA kata kunci penting dalam bentuk senarai dipisahkan koma.
                        Berikan jenis bahan yang terlibat, jenis kategori makanan, kata kunci makanan, dan jenis makanan.

                        Contoh sekiranya makanan tinggi kalori ("berikan saya contoh resipi yang berasaskan ayam"): MAKA output tersebut akan menjadi "ayam, tinggi protein, makanan tengahhari, protein"

                        Contoh sekiranya makanan rendah kalori ("berikan saya contoh resipi yang boleh merendahkan berat badan"): "salad, rendah kalori, makanan tengahhari, sayur"

                        Contoh sekiranya makanan mengikut kaedah masakan ("berikan saya contoh resipi melibatkan kaedah goreng"): "goreng, makanan tengahhari"

                        Contoh sekiranya makanan mengikut jenis bahan ("berikan saya contoh resipi nasi"): "nasi, makanan tengahhari, kabohidrat"

                        Sekiranya pengguna masukkan input "Saya hendak kuruskan badan, apa contoh resipi yang sesuai" MAKA output "salad, sayuran, rendah kalori"

                        Sekiranya pengguna masukkan input "Saya hendak bina badan, apa contoh resipi yang sesuai" MAKA output "ayam, ikan, tinggi kalori"

                        PENTING: Berikan kata kunci dalam bentuk perkataan SAHAJA.
                        JANGAN libatkan cara pemasakan KECUALI sekiranya pengguna menyatakan ia secara spesifik.
                        PENTING: Hanya berikan output dalam bentuk perkataan kecil seperti contoh : "ayam, tinggi kalori, ikan,"
                        PASTIKAN Bahan utama terdapat dalam kata kunci utama.
                        SEKIRANYA pengguna tidak menyatakan spesifik kaedah masakan seperti goreng MAKA JANGAN letakkan kaedah masakan sebagai kata kunci seperti goreng, rebus.
                        CONTOH YANG SALAH : "rendah kalori * protein" ATAU " makanan tengahhari * nasi " pastikan kata kunci tersebut dalam bentuk perkataan seperti "nasi, makanan tengahhari, kabohidrat"
                        PASTIKAN di dalam output tiada simbol lain seperti * atau \ atau "

                        JANGAN BUAT AYAT PENDAHULUAN, TERUS BAGI KATA KUNCI.' . $formattedConv
        ]
    ];

    $data = [
        "model" => "llama-3.1-8b-instant",
        "messages" => $messages,
        "temperature" => 0.333,
        "max_tokens" => 100,
        "top_p" => 1,
        "frequency_penalty" => 0,
        "presence_penalty" => 0
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $groqApiKey
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $keywords = $result['choices'][0]['message']['content'] ?? '';
    
    // Bersihkan dan format kata kunci
    $keywords = str_replace(['.', '"', "'"], '', $keywords);
    $keywords = explode(',', $keywords);
    $keywords = array_map('trim', $keywords);
    $keywords = array_filter($keywords);
    
    return array_slice($keywords, 0, 5); // Ambil maksimal 5 kata kunci
}

// Fungsi untuk mencari resipi berdasarkan kata kunci
function searchRecipesByKeywords($keywords) {
    global $connect;
    
    if (empty($keywords)) return [];
    
    $where = [];
    $params = [];
    
    foreach ($keywords as $keyword) {
        if (strlen($keyword) > 2) {
            $where[] = "(name_recipe LIKE ? OR desc_recipe LIKE ? OR ingredient_recipe LIKE ? OR category_recipe LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
    }
    
    if (empty($where)) return [];
    
    $sql = "SELECT * FROM recipes WHERE (" . implode(' OR ', $where) . ") LIMIT 10";
    $stmt = $connect->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Inisialisasi percakapan jika belum ada
if (!isset($_SESSION['conversation'])) {
    $_SESSION['conversation'] = [
        [
            'role' => 'system',
            'content' => '
                     **Peranan Anda:** 
                        Anda adalah Pakar Pemakanan Berlesen dan AI Assistant Resipi Sihat. Tugas anda adalah membantu pengguna mencari resipi yang paling sesuai berdasarkan keperluan pemakanan, citarasa, dan gaya hidup mereka.

                        **Langkah Analisis Wajib:**
                        1. **Kenal pasti Profil Pengguna** (tanya jika tidak lengkap):
                        - "Bolehkah anda kongsikan umur, jantina, dan aktiviti harian?"
                        - "Adakah anda mempunyai keadaan kesihatan tertentu? (Contoh: diabetes, darah tinggi, kolesterol)"
                        - "Apa matlamat pemakanan anda? (Contoh: turun berat badan, tambah jisim otot, kekal sihat)"

                        2. **Kaji Keperluan Pemakanan:**
                        - Kira keperluan kalori harian berdasarkan profil pengguna
                        - Tentukan makronutrien (karbohidrat, protein, lemak) yang sesuai
                        - Pertimbangkan keperluan mikronutrien (zat besi, kalsium, vitamin)

                        3. **Tanya Preferensi Makanan:**
                        - "Bahan utama apakah yang anda suka atau ada sekarang?"
                        - "Adakah anda mengikuti diet khusus? (Contoh: halal, vegetarian, rendah gluten)"
                        - "Berapa lama masa memasak yang anda ada?"

                        4. **Cadangkan Resipi Berdasarkan:**
                        - Kesesuaian dengan profil kesihatan pengguna
                        - Keseimbangan nutrisi (gunakan Piramid Makanan Malaysia)
                        - Kemudahan penyediaan dan bahan

                        **Format Respons:**
                        [PENILAIAN NUTRISI] 
                        "Resipi ini sesuai kerana: 
                        - Rendah karbohidrat (hanya 35g) sesuai untuk pesakit diabetes 
                        - Tinggi protein (25g) membantu pembinaan otot 
                        - Menggunakan minyak zaitun yang baik untuk jantung"

                        [CADANGAN RESEPI UTAMA]
                        "Nasi Ayam Kunyit dengan Salad Mangga:
                        - Kalori: 420kcal
                        - Masa: 30 minit
                        - Bahan utama: dada ayam, kunyit, beras perang"

                        [PILIHAN ALTERNATIF]
                        1. Sup Sayur Campur (Vegetarian) - 280kcal
                        2. Ikan Bakar Sambal - 350kcal

                        [TIPS PEMAKANAN]
                        "Untuk lebih seimbang, tambah 1 hidangan sayur hijau. Elakkan pengambilan garam berlebihan jika ada sejarah darah tinggi."

                        **Contoh Interaksi:**
                        Pengguna: "Saya nak makan siang yang cepat, ada ayam dan sayur dalam peti sejuk"
                        AI: "Berdasarkan bahan anda, saya cadangkan:
                        1. **Stir-fry Ayam Brokoli** (350kcal): 
                        - Sediakan dalam 15 minit 
                        - Tinggi protein & serat
                        - Gunakan minyak canola untuk lemak sihat
                        
                        2. **Salad Ayam Thai** (300kcal):
                        - Campurkan ayam rebus dengan salad, sos kacang
                        - Rendah kalori tapi mengenyangkan
                        
                        Tip: Gunakan perahan limau sebagai pengganti sos untuk kurangkan kalori!"
                        Pastikan jawapan dalam hanya BAHASA Melayu
            '
        ]
    ];
}

// Tangani pengiriman pesan via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'send_message' && isset($_POST['message'])) {
        $userMessage = trim($_POST['message']);
        
        if (!empty($userMessage)) {
            // Simpan pesan pengguna ke sesi
            $_SESSION['conversation'][] = ['role' => 'user', 'content' => $userMessage];
            
            // Kirim pesan ke AI
            $response = chatWithAI($_SESSION['conversation']);
            
            // Dapatkan respons AI
            if (isset($response['choices'][0]['message']['content'])) {
                $aiResponse = $response['choices'][0]['message']['content'];
                
                // Simpan respons AI ke sesi
                $_SESSION['conversation'][] = ['role' => 'assistant', 'content' => $aiResponse];
                
                // EKSTRAK KATA KUNCI DARI PERCAKAPAN
                $keywords = extractKeywords($_SESSION['conversation']);
                
                // Cari resipi berdasarkan kata kunci
                $recipes = searchRecipesByKeywords($keywords);
                
                // Simpan ke database
                try {
                    // Buat chat baru jika belum ada
                    if (!isset($_SESSION['id_chat'])) {
                        $stmt = $connect->prepare("INSERT INTO chats (id_user, title_chat, created_date_chat, status_chat) 
                                               VALUES (?, ?, NOW(), 'active')");
                        $title = "Pencarian Resipi: " . substr($userMessage, 0, 50);
                        $stmt->execute([1, $title]); // id_user sementara = 1
                        $_SESSION['id_chat'] = $connect->lastInsertId();
                    }
                    
                    // Simpan pesan dan respons
                    $stmt = $connect->prepare("INSERT INTO responses (id_chat, text_user_response, text_ai_response, created_date_response, status_response) 
                                           VALUES (?, ?, ?, NOW(), 'active')");
                    $stmt->execute([$_SESSION['id_chat'], $userMessage, $aiResponse]);
                    
                } catch (PDOException $e) {
                    // Tangani error
                    // $aiResponse .= "\n\n[Error: Gagal menyimpan ke database]";
                }
                
                // Return response dalam format JSON
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'aiResponse' => nl2br(htmlspecialchars($aiResponse)),
                    'keywords' => $keywords,
                    'recipes' => $recipes
                ]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Maaf, terjadi kesalahan saat memproses permintaan Anda.'
                ]);
                exit;
            }
        }
    }
    elseif ($action === 'reset_chat') {
        // Reset percakapan
        unset($_SESSION['conversation']);
        unset($_SESSION['id_chat']);
        
        // Reinitialize conversation
        $_SESSION['conversation'] = [
            [
                'role' => 'system',
                'content' => '
                     **Peranan Anda:** 
                        Anda adalah Pakar Pemakanan Berlesen dan AI Assistant Resipi Sihat. Tugas anda adalah membantu pengguna mencari resipi yang paling sesuai berdasarkan keperluan pemakanan, citarasa, dan gaya hidup mereka.

                        **Langkah Analisis Wajib:**
                        1. **Kenal pasti Profil Pengguna** (tanya jika tidak lengkap):
                        - "Bolehkah anda kongsikan umur, jantina, dan aktiviti harian?"
                        - "Adakah anda mempunyai keadaan kesihatan tertentu? (Contoh: diabetes, darah tinggi, kolesterol)"
                        - "Apa matlamat pemakanan anda? (Contoh: turun berat badan, tambah jisim otot, kekal sihat)"

                        2. **Kaji Keperluan Pemakanan:**
                        - Kira keperluan kalori harian berdasarkan profil pengguna
                        - Tentukan makronutrien (karbohidrat, protein, lemak) yang sesuai
                        - Pertimbangkan keperluan mikronutrien (zat besi, kalsium, vitamin)

                        3. **Tanya Preferensi Makanan:**
                        - "Bahan utama apakah yang anda suka atau ada sekarang?"
                        - "Adakah anda mengikuti diet khusus? (Contoh: halal, vegetarian, rendah gluten)"
                        - "Berapa lama masa memasak yang anda ada?"

                        4. **Cadangkan Resipi Berdasarkan:**
                        - Kesesuaian dengan profil kesihatan pengguna
                        - Keseimbangan nutrisi (gunakan Piramid Makanan Malaysia)
                        - Kemudahan penyediaan dan bahan

                        **Format Respons:**
                        [PENILAIAN NUTRISI] 
                        "Resipi ini sesuai kerana: 
                        - Rendah karbohidrat (hanya 35g) sesuai untuk pesakit diabetes 
                        - Tinggi protein (25g) membantu pembinaan otot 
                        - Menggunakan minyak zaitun yang baik untuk jantung"

                        [CADANGAN RESEPI UTAMA]
                        "Nasi Ayam Kunyit dengan Salad Mangga:
                        - Kalori: 420kcal
                        - Masa: 30 minit
                        - Bahan utama: dada ayam, kunyit, beras perang"

                        [PILIHAN ALTERNATIF]
                        1. Sup Sayur Campur (Vegetarian) - 280kcal
                        2. Ikan Bakar Sambal - 350kcal

                        [TIPS PEMAKANAN]
                        "Untuk lebih seimbang, tambah 1 hidangan sayur hijau. Elakkan pengambilan garam berlebihan jika ada sejarah darah tinggi."

                        **Contoh Interaksi:**
                        Pengguna: "Saya nak makan siang yang cepat, ada ayam dan sayur dalam peti sejuk"
                        AI: "Berdasarkan bahan anda, saya cadangkan:
                        1. **Stir-fry Ayam Brokoli** (350kcal): 
                        - Sediakan dalam 15 minit 
                        - Tinggi protein & serat
                        - Gunakan minyak canola untuk lemak sihat
                        
                        2. **Salad Ayam Thai** (300kcal):
                        - Campurkan ayam rebus dengan salad, sos kacang
                        - Rendah kalori tapi mengenyangkan
                        
                        Tip: Gunakan perahan limau sebagai pengganti sos untuk kurangkan kalori!"
                        Pastikan jawapan dalam hanya BAHASA Melayu
                '
            ]
        ];
        
        // Return response reset
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Percakapan telah direset'
        ]);
        exit;
    }
}

// Ambil riwayat percakapan untuk ditampilkan
$conversation = isset($_SESSION['conversation']) ? array_slice($_SESSION['conversation'], 1) : []; // Lewati pesan sistem
?>

<?php $location_index = ".."; include('../components/head.php');?>

<body class="bg-gray-50">
    <?php include("../components/user/header.php")?>

    <main class="min-h-screen">
        <div class="dashboard-grid">
            <?php include("../components/user/nav.php")?>
        
            <!-- Main Content -->
            <div class="main-content">
                <?php include("../components/user/top-bar.php")?>
                
                <!-- Main Dashboard Content -->
                <div class="p-4 md:p-6">
                    
                    <?php include("../components/user/stats-cards.php")?>
                    
                    <!-- Two Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                        <!-- Left Column - AI Chat & Recipe Results -->
                        <div class="lg:col-span-2">
                            <!-- AI Chat Assistant -->
                            <div class="card bg-white rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6">
                                <div class="flex justify-between items-center mb-4 md:mb-6">
                                    <div class="flex items-center">
                                        <div class="bg-primary-100 p-2 md:p-3 rounded-full mr-3">
                                            <i class="fas fa-robot text-primary-700 text-lg md:text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg md:text-xl font-bold text-gray-900">AI Penasihat Pemakanan</h3>
                                            <p class="text-gray-600 text-sm md:text-base">Tanya apa-apa tentang resipi sihat</p>
                                        </div>
                                    </div>
                                    <button id="reset-chat" class="text-sm bg-red-100 text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-200 transition">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </button>
                                </div>

                                <!-- Chat Container -->
                                <div class="bg-gray-100 rounded-xl p-3 md:p-4">
                                    <div class="chat-container overflow-y-auto scrollbar-hidden max-h-[350px] md:max-h-[400px] pr-2" id="chat-messages">
                                        <?php if (empty($conversation)): ?>
                                            <div class="text-center py-6 md:py-8">
                                                <div class="inline-block bg-gray-200 rounded-full p-3 md:p-4 mb-3 md:mb-4">
                                                    <i class="fas fa-robot text-2xl md:text-3xl text-primary"></i>
                                                </div>
                                                <h3 class="text-base md:text-lg font-bold text-gray-700">Hai, saya AI Penasihat pemakanan anda !</h3>
                                                <p class="text-gray-600 text-sm md:text-base mt-2 mb-4">Sila sampaikan keperluan resipi anda. Contoh:</p>
                                                <div class="mt-3 md:mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 md:gap-3">
                                                    <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                        "Resipi sarapan tinggi protein"
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                        "Makan tengah hari rendah kalori dengan ayam"
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                        "Resipi vegetarian untuk pemula"
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                        "Makanan penutup sihat tanpa gula"
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($conversation as $msg): ?>
                                                <div class="mb-3 md:mb-4">
                                                    <?php if ($msg['role'] === 'user'): ?>
                                                        <div class="flex justify-end mb-2">
                                                            <div class="bg-primary-500 text-white px-3 md:px-4 py-2 md:py-3 rounded-lg max-w-[85%] md:max-w-[80%] text-sm md:text-base">
                                                                <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="flex items-start mb-2">
                                                            <div class="bg-gray-200 rounded-full p-1 md:p-2 mr-2 md:mr-3">
                                                                <i class="fas fa-robot text-primary text-lg md:text-xl"></i>
                                                            </div>
                                                            <div class="bg-white px-3 md:px-4 py-2 md:py-3 rounded-lg shadow-sm max-w-[85%] md:max-w-[80%] text-sm md:text-base">
                                                                <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <form id="chat-form" class="mt-4 md:mt-6">
                                        <div class="flex">
                                            <input type="text" id="user-message" name="message" placeholder="Ketik permintaan resipi anda..." 
                                                   class="flex-grow px-3 md:px-4 py-2 md:py-3 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm md:text-base" required>
                                            <button type="submit" id="send-button" class="bg-primary-500 hover:bg-secondary-500 text-white px-4 md:px-6 rounded-r-lg transition">
                                                <i class="fas fa-paper-plane"></i>
                                                <span class="sr-only">Hantar</span>
                                            </button>
                                        </div>
                                        <p class="text-gray-500 text-xs md:text-sm mt-2">Contoh: "Resipi makan tengah hari rendah kalori dengan ayam"</p>
                                    </form>
                                </div>

                                <!-- Keywords Section -->
                                <div id="keywords-section" class="mt-4 md:mt-6"></div>
                            </div>

                            <!-- Recipe Results Section -->
                        </div>
                        <div class="">
                            <div class="card bg-white rounded-xl shadow-lg p-4 md:p-6">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-4 md:mb-6">Resipi Disyorkan</h3>
                                <div id="recipe-results" class="recipe-results">
                                    <?php if (!empty($recipes)): ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                                            <?php foreach ($recipes as $recipe): ?>
                                                <a href="./resipi/?id=<?php echo $recipe['id_recipe']?>" class="block">
                                                    <div class="recipe-card border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow duration-300 h-full">
                                                        <div class="relative overflow-hidden rounded-xl w-full h-40 md:h-48">
                                                            <img 
                                                                src="<?php echo htmlspecialchars(formatImagePath($recipe['image_recipe'], "../"))?>" 
                                                                alt="<?php echo htmlspecialchars($recipe['name_recipe'])?>"
                                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                                                loading="lazy"
                                                            />
                                                            <div class="absolute top-2 right-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                                                <?= htmlspecialchars($recipe['calories_recipe']) ?> kkal
                                                            </div>
                                                        </div>
                                                        <div class="p-3 md:p-4">
                                                            <h3 class="font-bold text-base md:text-lg text-gray-800 line-clamp-1 mb-2">
                                                                <?= htmlspecialchars($recipe['name_recipe']) ?>
                                                            </h3>
                                                            <p class="text-gray-600 text-xs md:text-sm line-clamp-2 mb-3">
                                                                <?= htmlspecialchars(substr($recipe['desc_recipe'], 0, 80)) ?>...
                                                            </p>
                                                            
                                                            <div class="flex justify-between text-xs md:text-sm text-gray-500 mb-3">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    <span><?= htmlspecialchars($recipe['cooking_time_recipe']) ?> min</span>
                                                                </div>
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-heart text-red-500 mr-1"></i>
                                                                    <span><?= htmlspecialchars($recipe['num_likes_recipe']) ?> suka</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <button class="w-full bg-primary hover:bg-secondary text-white py-2 rounded-lg transition flex items-center justify-center text-sm md:text-base">
                                                                <i class="fas fa-book-open mr-2"></i> Lihat Resipi
                                                            </button>
                                                        </div>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-8 md:py-12">
                                            <div class="inline-block bg-gray-100 rounded-full p-4 md:p-5 mb-4">
                                                <i class="fas fa-utensils text-2xl md:text-3xl text-primary"></i>
                                            </div>
                                            <h3 class="text-base md:text-lg font-bold text-gray-700">Rekomendasi Resipi</h3>
                                            <p class="text-gray-600 text-sm md:text-base mt-2">Resipi yang disyorkan akan muncul di sini</p>
                                            <p class="text-gray-500 text-xs md:text-sm mt-1">Cuba tanya AI untuk cadangan resipi</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                        
                        <!-- Right Column -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php $location_index='..'; include('../components/footer.php')?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk scroll ke bawah di chat container
            function scrollChatToBottom() {
                const chatContainer = $('#chat-messages');
                chatContainer.animate({ scrollTop: chatContainer[0].scrollHeight }, 300);
            }
            
            // Fungsi untuk menampilkan animasi loading
            function showLoading() {
                return `
                    <div class="flex items-start mb-3 md:mb-4">
                        <div class="bg-gray-200 rounded-full p-1 md:p-2 mr-2 md:mr-3">
                            <i class="fas fa-robot text-primary text-lg md:text-xl"></i>
                        </div>
                        <div class="bg-white px-3 md:px-4 py-2 md:py-3 rounded-lg shadow-sm max-w-[85%] md:max-w-[80%]">
                            <div class="loading-dots flex space-x-1">
                                <div class="w-2 h-2 bg-gray-300 rounded-full animate-pulse"></div>
                                <div class="w-2 h-2 bg-gray-300 rounded-full animate-pulse delay-150"></div>
                                <div class="w-2 h-2 bg-gray-300 rounded-full animate-pulse delay-300"></div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Fungsi untuk menampilkan pesan pengguna
            function appendUserMessage(message) {
                const messageHtml = `
                    <div class="mb-3 md:mb-4">
                        <div class="flex justify-end mb-2">
                            <div class="bg-primary-500 text-white px-3 md:px-4 py-2 md:py-3 rounded-lg max-w-[85%] md:max-w-[80%] text-sm md:text-base">
                                ${message}
                            </div>
                        </div>
                    </div>
                `;
                $('#chat-messages').append(messageHtml);
                scrollChatToBottom();
            }
            
            // Fungsi untuk menampilkan pesan AI
            function appendAiMessage(message) {
                const messageHtml = `
                    <div class="mb-3 md:mb-4">
                        <div class="flex items-start mb-2">
                            <div class="bg-gray-200 rounded-full p-1 md:p-2 mr-2 md:mr-3">
                                <i class="fas fa-robot text-primary text-lg md:text-xl"></i>
                            </div>
                            <div class="bg-white px-3 md:px-4 py-2 md:py-3 rounded-lg shadow-sm max-w-[85%] md:max-w-[80%] text-sm md:text-base">
                                ${message}
                            </div>
                        </div>
                    </div>
                `;
                $('#chat-messages').append(messageHtml);
                scrollChatToBottom();
            }
            
            // Fungsi untuk menampilkan kata kunci
            function updateKeywords(keywords) {
                if (keywords.length === 0) {
                    $('#keywords-section').html('');
                    return;
                }
                
                let badges = '';
                keywords.forEach(keyword => {
                    badges += `
                        <a href="./resipi/komuniti.php?search=${encodeURIComponent(keyword)}">
                            <span class="keyword-badge bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1.5 rounded-full text-sm flex items-center transition-colors">
                                <i class="fas fa-tag mr-2 text-xs"></i> ${keyword}
                            </span>
                        </a>
                    `;
                });
                
                const html = `
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-base font-bold text-gray-800">Kata Kunci Pencarian</h4>
                            <span class="bg-primary-500 text-white text-xs px-2 py-1 rounded-full">
                                ${keywords.length} ditemui
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            ${badges}
                        </div>
                    </div>
                `;
                
                $('#keywords-section').html(html);
            }
            
            // Fungsi untuk menampilkan resipi
            function updateRecipes(recipes) {
                if (recipes.length === 0) {
                    const emptyHtml = `
                        <div class="text-center py-8 md:py-12">
                            <div class="inline-block bg-gray-100 rounded-full p-4 md:p-5 mb-4">
                                <i class="fas fa-search text-2xl md:text-3xl text-primary"></i>
                            </div>
                            <h3 class="text-base md:text-lg font-bold text-gray-700">Resipi tidak ditemui</h3>
                            <p class="text-gray-600 text-sm md:text-base mt-2">Sila cuba dengan kata kunci yang berbeza</p>
                            <p class="text-gray-500 text-xs md:text-sm mt-1">Tanya AI untuk cadangan spesifik</p>
                        </div>
                    `;
                    
                    $('#recipe-results').html(emptyHtml);
                    return;
                }
                
                let recipeCards = '';
                recipes.forEach(recipe => {
                    const desc = recipe.desc_recipe ? recipe.desc_recipe.substring(0, 80) + '...' : '';
                    recipeCards += `
                        <a href="./resipi/?id=${recipe.id_recipe}" class="block">
                            <div class="recipe-card border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow duration-300 h-full">
                                <div class="relative overflow-hidden rounded-xl w-full h-40 md:h-48">
                                    <img 
                                        src="../uploads/recipes/${recipe.image_recipe}" 
                                        alt="${recipe.name_recipe}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                        loading="lazy"
                                    />
                                    <div class="absolute top-2 right-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                        ${recipe.calories_recipe} kkal
                                    </div>
                                </div>
                                <div class="p-3 md:p-4">
                                    <h3 class="font-bold text-base md:text-lg text-gray-800 line-clamp-1 mb-2">
                                        ${recipe.name_recipe}
                                    </h3>
                                    <p class="text-gray-600 text-xs md:text-sm line-clamp-2 mb-3">
                                        ${desc}
                                    </p>
                                    
                                    <div class="flex justify-between text-xs md:text-sm text-gray-500 mb-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span>${recipe.cooking_time_recipe} min</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-heart text-red-500 mr-1"></i>
                                            <span>${recipe.num_likes_recipe} suka</span>
                                        </div>
                                    </div>
                                    
                                    <button class="w-full bg-primary hover:bg-secondary text-white py-2 rounded-lg transition flex items-center justify-center text-sm md:text-base">
                                        <i class="fas fa-book-open mr-2"></i> Lihat Resipi
                                    </button>
                                </div>
                            </div>
                        </a>
                    `;
                });
                
                $('#recipe-results').html(`
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        ${recipeCards}
                    </div>
                `);
            }
            
            // Event untuk mengirim pesan
            $('#chat-form').on('submit', function(e) {
                e.preventDefault();
                const message = $('#user-message').val().trim();
                if (!message) return;
                
                // Tampilkan pesan pengguna
                appendUserMessage(message);
                
                // Tampilkan animasi loading
                const loadingHtml = showLoading();
                $('#chat-messages').append(loadingHtml);
                scrollChatToBottom();
                
                // Reset input
                $('#user-message').val('');
                
                // Disable button while sending
                const sendButton = $('#send-button');
                sendButton.prop('disabled', true).addClass('opacity-50');
                
                // Kirim permintaan AJAX
                $.ajax({
                    url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                    type: 'POST',
                    data: {
                        action: 'send_message',
                        message: message
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Enable button
                        sendButton.prop('disabled', false).removeClass('opacity-50');
                        
                        // Hapus animasi loading
                        $('.loading-dots').closest('.mb-3, .mb-4').remove();
                        
                        if (response.success) {
                            // Tampilkan respons AI
                            appendAiMessage(response.aiResponse);
                            
                            // Tampilkan kata kunci
                            updateKeywords(response.keywords);
                            
                            // Tampilkan resipi
                            updateRecipes(response.recipes);
                        } else {
                            appendAiMessage(response.error);
                        }
                    },
                    error: function() {
                        // Enable button
                        sendButton.prop('disabled', false).removeClass('opacity-50');
                        
                        // Hapus animasi loading
                        $('.loading-dots').closest('.mb-3, .mb-4').remove();
                        
                        appendAiMessage('Maaf, terjadi kesalahan saat memproses permintaan Anda.');
                    }
                });
            });
            
            // Event untuk reset chat
            $('#reset-chat').on('click', function() {
                if (confirm('Adakah anda pasti mahu reset percakapan?')) {
                    $.ajax({
                        url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                        type: 'POST',
                        data: {
                            action: 'reset_chat'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Reset tampilan chat
                                $('#chat-messages').html(`
                                    <div class="text-center py-6 md:py-8">
                                        <div class="inline-block bg-gray-200 rounded-full p-3 md:p-4 mb-3 md:mb-4">
                                            <i class="fas fa-robot text-2xl md:text-3xl text-primary"></i>
                                        </div>
                                        <h3 class="text-base md:text-lg font-bold text-gray-700">Hai, saya AI Penasihat pemakanan anda !</h3>
                                        <p class="text-gray-600 text-sm md:text-base mt-2 mb-4">Sila sampaikan keperluan resipi anda. Contoh:</p>
                                        <div class="mt-3 md:mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 md:gap-3">
                                            <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                "Resipi sarapan tinggi protein"
                                            </div>
                                            <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                "Makan tengah hari rendah kalori dengan ayam"
                                            </div>
                                            <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                "Resipi vegetarian untuk pemula"
                                            </div>
                                            <div class="bg-white rounded-lg p-2 md:p-3 text-xs md:text-sm shadow">
                                                "Makanan penutup sihat tanpa gula"
                                            </div>
                                        </div>
                                    </div>
                                `);
                                
                                // Reset kata kunci dan resipi
                                $('#keywords-section').html('');
                                $('#recipe-results').html(`
                                    <div class="text-center py-8 md:py-12">
                                        <div class="inline-block bg-gray-100 rounded-full p-4 md:p-5 mb-4">
                                            <i class="fas fa-utensils text-2xl md:text-3xl text-primary"></i>
                                        </div>
                                        <h3 class="text-base md:text-lg font-bold text-gray-700">Rekomendasi Resipi</h3>
                                        <p class="text-gray-600 text-sm md:text-base mt-2">Resipi yang disyorkan akan muncul di sini</p>
                                        <p class="text-gray-500 text-xs md:text-sm mt-1">Cuba tanya AI untuk cadangan resipi</p>
                                    </div>
                                `);
                            }
                        }
                    });
                }
            });
            
            // Enter to send message
            $('#user-message').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    $('#chat-form').submit();
                }
            });
            
            // Auto-scroll ke bawah saat halaman dimuat
            scrollChatToBottom();
        });
    </script>
    
    <style>
        .chat-container {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        
        .chat-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .chat-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .chat-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }
        
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        
        .keyword-badge {
            transition: all 0.2s ease;
        }
        
        .keyword-badge:hover {
            transform: translateY(-1px);
        }
        
        .loading-dots span {
            animation: blink 1.4s infinite both;
        }
        
        .loading-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .loading-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes blink {
            0%, 80%, 100% { opacity: 0; }
            40% { opacity: 1; }
        }
    </style>
</body>
</html>