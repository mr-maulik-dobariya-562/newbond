<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Machine</th>
            <th>Runner Waste</th>
            <th>Production Weight</th>
            <th>Production Pieces Quantity</th>
            <th>Component Rejection</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;  ?>
        <?php
        $waste = 0;
        $weight = 0;
        $quantity = 0;
        $rejection = 0;
        foreach ($data as $v) :
            $waste += $v->runner_waste_sum;
            $weight += $v->production_weight;
            $quantity += $v->production_pieces_quantity;
            $rejection += $v->component_rejection;
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $v->machine->name ?></td>
                <td><?= $v->runner_waste_sum ?></td>
                <td><?= $v->production_weight ?></td>
                <td><?= $v->production_pieces_quantity ?></td>
                <td><?= $v->component_rejection ?></td>
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
                        <h3 class="totalColor"><?= $waste ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $weight ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $quantity ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $rejection ?></h3>
                    </b>
                </th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>
