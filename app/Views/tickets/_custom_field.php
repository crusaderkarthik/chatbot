<?php
// $f = custom field row
?>
<div class="form-group">
    <label><?= e($f['label']) ?><?= $f['is_required'] ? ' *' : '' ?></label>
    <?php
    $name  = 'cf[' . $f['id'] . ']';
    $req   = $f['is_required'] ? 'required' : '';
    $opts  = array_filter(array_map('trim', explode(',', $f['options'] ?? '')));
    switch ($f['type']):
        case 'textarea': ?>
        <textarea name="<?= $name ?>" class="form-control" <?= $req ?> rows="3"></textarea>
        <?php break;
        case 'number': ?>
        <input type="number" name="<?= $name ?>" class="form-control" <?= $req ?>>
        <?php break;
        case 'date': ?>
        <input type="date" name="<?= $name ?>" class="form-control" <?= $req ?>>
        <?php break;
        case 'select': ?>
        <select name="<?= $name ?>" class="form-control" <?= $req ?>>
            <option value="">— Select —</option>
            <?php foreach ($opts as $o): ?>
            <option value="<?= e($o) ?>"><?= e($o) ?></option>
            <?php endforeach; ?>
        </select>
        <?php break;
        case 'checkbox': ?>
        <label class="checkbox-label"><input type="checkbox" name="<?= $name ?>" value="1" <?= $req ?>> Yes</label>
        <?php break;
        case 'radio':
            foreach ($opts as $o): ?>
            <label class="radio-label"><input type="radio" name="<?= $name ?>" value="<?= e($o) ?>" <?= $req ?>> <?= e($o) ?></label>
        <?php endforeach;
        break;
        default: ?>
        <input type="text" name="<?= $name ?>" class="form-control" <?= $req ?>>
        <?php break;
    endswitch; ?>
</div>
