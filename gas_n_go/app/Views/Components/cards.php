<div class="grid grid-cols-2  gap-8 text-center">
    <?php
    foreach ($stats as $stat) {
    ?>
        <div class="bg-gray-50 flex-grow shadow-md rounded py-4 space-y-3">
            <h1 class=" text-gray-500"><?= $stat->name ?></h1>
            <p class="font-bold text-xl"><?= $stat->value ?></p>
        </div>

    <?php } ?>
</div>