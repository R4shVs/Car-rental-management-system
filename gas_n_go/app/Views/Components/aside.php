<?php
$home = 'http://localhost/ProgettoDB/gas_n_go/resources';
$profile = $home . '/profile.php';
?>

<aside class="text-gray-400 w-60 h-full flex flex-col flex-none bg-green-900 border-r border-light-gray-100">
    <div class="p-10 text-2xl font-bold text-white border-b border-gray-500">
        <a href="<?= $home ?>/">Gas'n'Go</a>
    </div>
    <a href="<?= $profile ?>" class="p-4 border-b border-gray-500 hover:bg-green-600 hover:text-white
        <?php
        echo ($current_page === 'Profilo')
            ?
            "text-white bg-green-600 hover:bg-green-700 font-bold"
            :
            "hover:text-white hover:bg-green-600";
        ?>
    ">Profilo</a>
    <a href="<?= $home ?>/" class="p-4 border-b border-gray-500 hover:bg-green-600 hover:text-white
        <?php
        echo ($current_page === 'Dashboard')
            ?
            "text-white bg-green-600 hover:bg-green-700 font-bold"
            :
            "hover:text-white hover:bg-green-600";
        ?>
    ">Dashboard</a>
    <?php
    foreach ($operations as $operation) {
    ?>

        <a href="<?= $home . '/' . $operation->path ?>" class="p-4
        <?php
        echo ($operation->name === $current_page)
            ?
            "text-white bg-green-600 hover:bg-green-700 font-bold"
            :
            "hover:text-white hover:bg-green-600 ";
        ?>
    "><?= $operation->name ?></a>
    <?php } ?>
</aside>