<?php
use yii\helpers\Html;
?>

<div>
    <p>Здравствуйте, <?=$user->first_name?> <?=$user->last_name?>!</p>
    <p>Просим Вас погасить имеющиеся долговые обязательства, которые образовались</p>
    <?php if(count($lostMembershipDates) > 0): ?>
        <p> по членским взносам за числа:
            <?php foreach ($lostMembershipDates as $item): ?>
                <?=str_replace(' 00:00:00', '', $item)?>,
            <?php endforeach ?>
        </p>
    <?php endif ?>
    <?php if(count($lostMortgageDates) > 0): ?>
        <p> по ипотечным платежам за числа:
            <?php foreach ($lostMortgageDates as $item): ?>
                <?=str_replace(' 00:00:00', '', $item)?>,
            <?php endforeach ?>
        </p>
    <?php endif ?>
    <p>с уважением,<br>
        команда МПО Цай Шень.</p>
</div>
