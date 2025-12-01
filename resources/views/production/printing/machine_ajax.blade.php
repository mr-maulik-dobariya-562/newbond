<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Machine</th>
            <th>Production Qty Pcs</th>
            <th>Rejection Qty Kg</th>
            <th>Rejection Qty Pcs</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
        $production_qty = 0;
        $rection_qty = 0;
        $rejection_qty = 0;
        foreach ($data as $v) :
            $production_qty += $v->production_qty;
            $rection_qty += $v->rection_qty;
            $rejection_qty += $v->rejection_qty;
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $v->machine->name ?></td>
                <td><?= $v->production_qty ?></td>
                <td><?= $v->rection_qty ?></td>
                <td><?= $v->rejection_qty ?></td>
                <td><?= date("d-m-Y H:i:s", strtotime($v->created_at)); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <thead>
            <tr>
                <th><b>
                        <h3 class="totalColor">Total</h3>
                    </b></th>
                <th></th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $production_qty ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $rection_qty ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $rejection_qty ?></h3>
                    </b>
                </th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>
