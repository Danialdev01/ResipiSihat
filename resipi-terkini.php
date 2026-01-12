<?php $location_index = "."; include('./components/head.php');?>

<body>

    <main style='min-height:90dvh;'>
        <?php $location_index = "."; require('./components/home/nav.php')?>


        <section id="resipi-terkini" class="pt-20">
            <center>
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <br><br>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                        Resipi Sihat untuk Anda
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600">
                        Temui beribu-ribu resipi sihat yang sesuai dengan citarasa dan gaya hidup anda
                    </p>
                </div>
            </center>
            <?php $location_index = "."; require('./components/home/resipi-terkini.php')?>
        </section>
    </main>

    <?php $location_index='.'; include('./components/footer.php')?>
    
</body>
</html>