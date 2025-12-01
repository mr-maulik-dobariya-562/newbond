<table id="example_table" class="table table-hover card-table table-vcenter text-nowrap datatable" style="width: 100%;"
    data-page_length="500" data-responsive="false">
    <thead>
        <?php
$qty_total = 0;
$amount_total = 0;
$cancelQty = 0;
foreach ($data as $v) {
    $qty_total += $v->qty;
    $amount_total += $v->amount;
    $cancelQty += $v->cancel_qty;
}
        ?>
        <tr>
            <th>Sr. No.</th>
            <th>Customer Name</th>
            <th>QTY<br><b><?= $qty_total ?></b></th>
            <th>Cancel QTY<br><b><?= $cancelQty ?></b></th>
            <th>Amount<br><b><?= $amount_total ?></b></th>
            <th>Create At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
$qty_total = 0;
$amount_total = 0;
$cancelQty = 0;
foreach ($data as $v):
    $qty_total += $v->qty;
    $amount_total += $v->amount;
    $cancelQty += $v->cancel_qty;
        ?>
        <tr>
            <td style="color: <?= $v->color ?>;"><?= $i++ ?></td>
            <td style="color: <?= $v->color ?>;"><?= $v->customer ?></td>
            <td style="color: <?= $v->color ?>;"><?= $v->qty  ?></td>
            <td style="color: <?= $v->color ?>;"><?= $v->cancel_qty ?></td>
            <td style="color: <?= $v->color ?>;"><?= sprintf('%0.2f', $v->amount) ?></td>
            <td style="color: <?= $v->color ?>;"><?= date("d-m-Y H:i:s", strtotime($v->createdAt)); ?></td>
            <td style="color: <?= $v->color ?>;"><?= date("d-m-Y H:i:s", strtotime($v->updatedAt)); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th><b>
                    <h3 class="totalColor">Total</h3>
                </b></th>
            <th></th>
            <th><b>
                    <h3 class="totalColor"><?= $qty_total ?></h3>
                </b></th>
            <th><b>
                    <h3 class="totalColor"><?= $cancelQty ?></h3>
                </b></th>
            <th><b>
                    <h3 class="totalColor"><?= $amount_total ?></h3>
                </b></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
