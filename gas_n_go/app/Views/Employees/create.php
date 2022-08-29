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
                    <form action="<?= $home ?>/employees/create.php" method="POST">
                        <div class="flex flex-col bg-white p-10 rounded-lg shadow space-y-16">
                            <?php
                            if (isset($_GET['error'])) {
                                $msg = $_GET['error'];
                                include(__DIR__ . '/../Components/error_msg.php');
                            }
                            ?>
                            <div class="flex flex-col gap-6">
                                <h2 class="font-bold text-xl text-center
                                <?= (isset($_GET['user_error'])) ?  'text-red-600' : '' ?>">
                                    Account socio
                                </h2>

                                <?php
                                if (isset($_GET['user_error'])) {
                                    $msg = $_GET['user_error'];
                                    include(__DIR__ . '/../Components/error_msg.php');
                                }
                                ?>
                                <?php
                                $fields = $uFields;
                                include(__DIR__ . '/../Components/form.php');
                                ?>
                            </div>

                            <div class="flex flex-col gap-6">
                                <h2 class="font-bold text-xl text-center
                                <?= (isset($_GET['employee_error'])) ?  'text-red-600' : '' ?>">
                                    Informazioni addetto
                                </h2>

                                <?php
                                if (isset($_GET['employee_error'])) {
                                    $msg = $_GET['employee_error'];
                                    include(__DIR__ . '/../Components/error_msg.php');
                                }
                                ?>
                                <?php
                                $fields = $eFields;
                                include(__DIR__ . '/../Components/form.php');
                                ?>
                            </div>
                            <?php
                            $button =  'Assumi';
                            include(__DIR__ . '/../Components/submit_button.php');
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>