<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="img/png" href="http://localhost/gas_n_go/icon.png" />
    <title><?= $current_page ?> - Gas'n'Go</title>
</head>

<body>
    <div class="flex flex-col h-screen font-sans ">
        <div class="flex flex-grow">
            <?php include(__DIR__ . '/../Components/aside.php'); ?>
            <div class="flex-grow px-14">
                <div class="flex justify-between items-center mt-5">
                    <h1 class="text-4xl font-bold"><?= $current_page ?></h1>
                    <div class="flex flex-row gap-6">
                        <a href="<?= $home ?>/profile/edit.php" class="text-blue-500 font-bold px-5 py-2 hover:text-blue-700 transition-colors">
                            Modifica credenziali
                        </a>
                        <a href="<?= $home ?>/auth/logout.php" class="bg-blue-500 text-white font-bold px-5 py-2 rounded focus:outline-none shadow hover:bg-blue-700 transition-colors">
                            Log out
                        </a>
                    </div>
                </div>
                <div class="w-3/5 mx-auto mt-6">
                    <?php
                    $title = 'Informazioni';
                    $row = $info;
                    include(__DIR__ . '/../Components/info_table.php');
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>