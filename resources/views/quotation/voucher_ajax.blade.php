<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Item Name</th>
            <th>Customer</th>
            <th>City</th>
            <th>Date</th>
            <th>Transports</th>
            <th>Block</th>
            <th>Printing Detail</th>
            <th>Remark</th>
            <th>QTY</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Discount</th>
            <th>Remark</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
        $qty = 0;
        $rate = 0;
        $amount = 0;
        $discount = 0;
        foreach ($data as $v) :
            $qty += $v->qty;
            $rate += $v->rate;
            $amount += $v->amount;
            $discount += $v->discount;
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $v->item_name . ' - ' . $v->print_type; ?></td>
                <td><?= $v->customer ?></td>
                <td><?= $v->city; ?></td>
                <td><?= $v->date; ?></td>
                <td><?= $v->transports; ?></td>
                <td><?= $v->block; ?></td>
                <td><?= $v->narration; ?></td>
                <td><?= $v->remark; ?></td>
                <td><?= sprintf('%0.2f', $v->qty) ?></td>
                <td><?= sprintf('%0.2f', $v->rate) ?></td>
                <td><?= sprintf('%0.2f', $v->amount) ?></td>
                <td><?= sprintf('%0.2f', $v->discount) ?></td>
                <td><?= $v->remark; ?></td>
                <td><?= date("d-m-Y H:i:s", strtotime($v->createdAt)); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <thead>
            <tr>
                <th><b><h3 class="totalColor">Total</h3></b></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><b><h3 class="totalColor"><?= $qty ?></h3></b></th>
                <th><b><h3 class="totalColor"><?= $rate ?></h3></b></th>
                <th><b><h3 class="totalColor"><?= $amount ?></h3></b></th>
                <th><b><h3 class="totalColor"><?= $discount ?></h3></b></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>