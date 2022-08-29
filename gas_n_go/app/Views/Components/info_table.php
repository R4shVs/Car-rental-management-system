<div class="w-full shadow-md sm:rounded-lg mt-5">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th colspan="2" scope="col" class="px-6 py-3">
                    <?= $title ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($row as $key => $value) {
                if (!is_null($value)) {
            ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                            <?=ucfirst(str_replace("_"," ",$key))?>
                        </th>
                        <td class="px-6 py-4">
                            <?= $value ?>
                        </td>
                    </tr>
            <?php   }
            } ?>
        </tbody>
    </table>
</div>