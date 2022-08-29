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
            <div class="flex-grow px-14 mb-12">
                <h1 class="text-4xl font-bold mt-5"><?= $current_page ?></h1>
                <div class="w-3/5 mx-auto mt-6">
                    <form action="<?= $home ?>/cars/edit_state.php" method="POST">
                        <div class="flex flex-col bg-white p-10 rounded-lg shadow space-y-8">
                            <div class="flex flex-col gap-6">
                                <h2 class="font-bold text-xl text-center
                                <?= (isset($_GET['car_error'])) ?  'text-red-600' : '' ?>">
                                    Stato veiclo
                                </h2>

                                <?php
                                if (isset($_GET['car_error'])) {
                                    $msg = $_GET['car_error'];
                                    include(__DIR__ . '/../Components/error_msg.php');
                                }
                                ?>

                                <input type="hidden" id="targa" name="targa" value="<?= $targa ?>" />

                                <p class="text-center">
                                <?=ucfirst(str_replace('_', ' ', $state))?>
                                </p>


                            </div>
                            <?php
                            if ($state != 'in_noleggio') {
                                $button = ($state === 'disponibile') ?
                                    'Rimuovi dalla lista dei veicoli noleggiabili'
                                    :
                                    'Inserisci nalla lista dei veicoli noleggiabili';
                                include(__DIR__ . '/../Components/submit_button.php');
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>