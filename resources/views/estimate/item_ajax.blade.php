<table id="example_table" class="table card-table table-vcenter text-nowrap datatable" style="width: 100%"
    data-responsive="false" data-page_length="500">
    <thead>
        <?php
$qty_total = 0;
$amount_total = 0;
foreach ($data as $v) {
    $qty_total += $v->qty;
    $amount_total += $v->amount;
}
        ?>
        <tr>
            <th>Sr. No.</th>
            <th>Item Name</th>
            <th>QTY</br><b><?= $qty_total ?></b></th>
            <th>Amount</br><b><?= $amount_total ?></b></th>
            <th>Create At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
$qty_total = 0;
$amount_total = 0;
foreach ($data as $v):
    $qty_total += $v->qty;
    $amount_total += $v->amount;
        ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= $v->item ?></td>
            <td><?= $v->qty ?></td>
            <td><?= sprintf('%0.2f', $v->amount) ?></td>
            <td><?= date("d-m-Y H:i:s", strtotime($v->createdAt)); ?></td>
            <td><?= date("d-m-Y H:i:s", strtotime($v->updatedAt)); ?></td>
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
                    <h3 class="totalColor"><?= $amount_total ?></h3>
                </b></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
