<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Item Name</th>
            <th>Date</th>
            <th>QTY</th>
            <th>Parcel</th>
            <th>Remark</th>
            <th>Created By</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
        $qty = 0;
        $parcel = 0;
        foreach ($data as $v) :
            $qty += $v->qty;
            $parcel += $v->parcel;
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $v->item_name; ?></td>
                <td><?= $v->date; ?></td>
                <td><?= sprintf('%0.2f', $v->qty) ?></td>
                <td><?= $v->parcel; ?></td>
                <td><?= $v->remark; ?></td>
                <td><?= $v->createdBy; ?></td>
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
                <th><b><h3 class="totalColor"><?= $qty ?></h3></b></th>
                <th><b><h3 class="totalColor"><?= $parcel ?></h3></b></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>
