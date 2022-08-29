<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="img/png" href="http://localhost/gas_n_go/icon.png" />
    <title>Login - Gas'n'Go</title>
</head>

<body>
    <div class="font-sans min-h-screen antialiased bg-green-900 pt-24">
        <div class="flex flex-col justify-center sm:w-96 sm:m-auto space-y-8">
            <h1 class="font-bold text-center text-4xl text-white">
                Gas'n'Go
            </h1>
            <form action="./login.php" method="POST">
                <div class="flex flex-col bg-white p-10 rounded-lg shadow space-y-6">
                    <h2 class="font-bold text-xl text-center
                    <?= (isset($_GET['user_error'])) ?  'text-red-600' : '' ?>">
                        Accedi
                    </h2>

                    <?php
                    if (isset($_GET['user_error'])) {
                        $msg = $_GET['user_error'];
                        include(__DIR__ . '/../Components/error_msg.php');
                    }
                    ?>
                    <?php
                    include(__DIR__ . '/../Components/form.php');
                    ?>
                    <?php
                    $button =  'Login';
                    include(__DIR__ . '/../Components/submit_button.php');
                    ?>
                </div>
            </form>
        </div>
    </div>
</body>

</html>