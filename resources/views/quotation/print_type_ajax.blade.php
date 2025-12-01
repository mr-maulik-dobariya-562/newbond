<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th> 
            <th>Print Type</th>
            <th>QTY</th>
            <th>Amount</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
        $qty_total = 0;
        $amount_total = 0;
        foreach ($data as $v) {
            $qty_total += $v->qty;
            $amount_total += $v->amount;
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $v->print_type ?></td>
                <td><?= sprintf('%0.2f', $v->qty) ?></td>
                <td><?= sprintf('%0.2f', $v->amount) ?></td>
                <td><?= date("d-m-Y H:i:s", strtotime($v->createdAt)); ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <thead>
            <tr>
                <th><b><h3 class="totalColor">Total</h3></b></th>
                <th></th>
                <th><b><h3 class="totalColor"><?= $qty_total ?></h3></b></th>
                <th><b><h3 class="totalColor"><?= $amount_total ?></h3></b></th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>