<?php $location_index = "."; include('./components/head.php');?>

<body>

    <main>
        <?php $location_index = "."; require('./components/home/nav.php')?>


        <section id="tentang-kami" class="pt-20">
            
            <div class="hero-pattern pt-32 pb-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-4xl mx-auto">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">
                            Mengenai <span class="text-primary-600">ResepiSihat</span>
                        </h1>
                        <p class="text-xl text-gray-600">
                            Platform pemakanan sihat yang membantu anda merancang hidangan harian dengan mudah, pantas, dan berkhasiat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Our Story -->
            <div class="py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:flex lg:items-center lg:gap-16">
                        <div class="lg:w-1/2 mb-12 lg:mb-0">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">Kisah Kami</h2>
                            <p class="text-gray-600 mb-4">
                                ResepiSihat bermula pada tahun 2020 dengan misi untuk menjadikan pemakanan sihat lebih mudah diakses oleh semua orang. Kami percaya bahawa makanan yang baik adalah asas kepada kehidupan yang sihat dan bahagia.
                            </p>
                            <p class="text-gray-600 mb-4">
                                Bermula dari sekumpulan kecil penggemar makanan sihat, kami berkembang menjadi komuniti yang besar dengan ribuan resipi yang disemak oleh pakar pemakanan.
                            </p>
                            <p class="text-gray-600">
                                Hari ini, ResepiSihat digunakan oleh beribu-ribu orang setiap hari untuk merancang makanan mereka, mengurangkan pembaziran, dan menikmati hidangan yang lebih sihat.
                            </p>
                        </div>
                        <div class="lg:w-1/2">
                            <img src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
                                alt="Team Cooking" 
                                class="w-full rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Our Mission -->
            <div class="py-16 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Misi & Visi Kami</h2>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Kami berkomitmen untuk menjadikan pemakanan sihat mudah, menyeronokkan, dan berpatutan untuk semua.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="mission-card bg-white p-8 rounded-lg shadow-sm">
                            <div class="bg-primary-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                                <i class="fas fa-heart text-primary-700 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Kesihatan Utama</h3>
                            <p class="text-gray-600">
                                Membantu pengguna membuat pilihan makanan yang lebih baik untuk kesihatan jangka panjang.
                            </p>
                        </div>
                        
                        <div class="mission-card bg-white p-8 rounded-lg shadow-sm">
                            <div class="bg-primary-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                                <i class="fas fa-lightbulb text-primary-700 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Kemudahan Penggunaan</h3>
                            <p class="text-gray-600">
                                Menyediakan alat yang intuitif untuk merancang makanan harian dengan pantas.
                            </p>
                        </div>
                        
                        <div class="mission-card bg-white p-8 rounded-lg shadow-sm">
                            <div class="bg-primary-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                                <i class="fas fa-users text-primary-700 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Komuniti Penyokong</h3>
                            <p class="text-gray-600">
                                Membina komuniti yang saling berkongsi pengetahuan dan pengalaman pemakanan sihat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php $location_index='.'; include('./components/footer.php')?>
    
</body>
</html>