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
                <?php $location_index = "../.."; require('../../components/home/resipi-terkini.php')?>
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
        
        // Simulate progress bar animations
        document.querySelectorAll('.progress-fill').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    </script>

    </main>

    <?php $location_index='../..'; include('../../components/footer.php')?>
    
</body>
</html>