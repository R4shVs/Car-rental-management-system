<input
    type="text"
    name="<?= $campo['name'] ?>"
    id="<?= $campo['name'] ?>"
    <?= ($campo['required'] === 'true' ? true : false) ? "required\n" : "" ?>
    value="<?= $campo['data'] ?>"
    placeholder="<?= $campo['data'] ?>"
    class="border-2 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-400 focus:shadow"
/>