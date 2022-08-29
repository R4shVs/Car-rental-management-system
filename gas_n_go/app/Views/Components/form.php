<?php
foreach ($fields as $field) {
    switch ($field->type) {
        case 'text':
        case 'password':
        case 'number':
        case 'email':
?>
            <input
                type="<?= $field->type ?>"
                name="<?= $field->name ?>"
                id="<?= $field->name ?>"
                <?= $field->isRequired ? 'required' : '' ?>
                <?= isset($field->value) ? 'value="' . $field->value . '"' : '' ?>
                <?= $field->type === 'number' ? 'min="5"' : '' ?>
                placeholder="<?= $field->data ?>"
                class="border-2 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-400 focus:shadow" />
        <?php
            break;
        case 'date':
        ?>
            <div class="flex gap-4 items-center">
                <p class="font-semibold"><?= $field->data ?></p>
                <input
                    type="date"
                    name="<?= $field->name ?>"
                    id="<?= $field->name ?>"
                    <?= $field->isRequired ? 'required' : '' ?>
                    <?= isset($field->value) ? 'value="' . $field->value . '"' : '' ?>
                    class="flex-grow border-2 rounded px-3 py-2 focus:outline-none focus:border-blue-400 focus:shadow" />
            </div>
<?php
            break;
    }
}
?>