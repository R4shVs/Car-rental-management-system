<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="img/png" href="http://localhost/gas_n_go/icon.png" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
    <title><?= $current_page ?></title>
</head>

<body>
    <div class="flex flex-col h-screen font-sans">
        <div class="flex flex-grow">
            <?php include(__DIR__ . '/Components/aside.php'); ?>
            <div class="flex-grow px-14">
                <div class="flex justify-between items-center mt-5">
                    <h1 class="text-4xl font-bold"><?= $current_page ?></h1>
                </div>
                <div class="w-3/5 mx-auto mt-6">
                    <?php include(__DIR__ . '/Components/cards.php'); ?>
                </div>
                <div class="w-3/5 h-1/2 mx-auto mt-12">
                    <canvas id="myChart" ></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="js/<?= $chartType ?>.js"></script>

    <script>
        let data = <?= $chart ?>;
        let title = "<?= $chartTitle ?>";
        plot(data, title);
    </script>
</body>

</html>