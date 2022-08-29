<div class="w-full shadow-md sm:rounded-lg mt-5">
    <?php
    if (is_null($table->rowData)) {
    ?>
        <p class="text-center p-4"> Non sono stati trovati valori </p>
    <?php
    } else { ?>
        <table class="table-auto w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <?php
                    foreach ($table->tableHeaders as $header) {
                    ?>
                        <th cope="col" class="px-6 py-3 w-max">
                            <?= $header ?>
                        </th>
                    <?php } ?>

                    <?php
                    if (!is_null($table->actions))
                        foreach ($table->actions as $action) {
                    ?>
                        <th scope="col" class="px-6 py-3 w-0">

                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($table->rowData as $row) {
                    $queryString = '';
                ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <?php
                        foreach ($row as $key => $value) {
                            $value = str_replace('_', ' ', $value);
                        ?>
                            <?php
                            if ($key === $table->rowHeader) {
                            ?>
                                <th scope="row" class="px-6 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    <?=  $value ?>
                                </th>
                            <?php } else { ?>
                                <td class="px-6 py-4">
                                    <?= $value ?>
                                </td>
                            <?php } ?>
                        <?php } ?>

                        <?php
                        if (!is_null($table->actions))
                            foreach ($table->actions as $action) {
                                $queryString = '';
                                foreach ($action->queryParam as $key => $param) {
                                    $queryString .= $param . '=' . $row[$param];
                                    if ($key != array_key_last($action->queryParam)) {
                                        $queryString .= '&';
                                    }
                                }
                        ?>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= $home . '/' . $action->link ?><?= $queryString ?>" class="font-medium <?= $action->color ?>">
                                    <?= $action->title ?>
                                </a>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>