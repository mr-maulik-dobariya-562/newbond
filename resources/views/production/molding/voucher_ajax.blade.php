<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Action</th>
            <th>Date</th>
            <th>Operator</th>
            <th>Shift</th>
            <th>Machine</th>
            <th>Product Type</th>
            <th>Row Material</th>
            <th>Cavity</th>
            <th>Product Name</th>
            <th>Machine Counter</th>
            <th>Production Weight</th>
            <th>Production Quantity</th>
            <th>Runner Waste</th>
            <th>Component Rejection</th>
            <th>Color Type</th>
            <th>Created By</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        $counter = 0;
        $weight = 0;
        $qty = 0;
        $waste = 0;
        $rejection = 0;
        foreach ($data as $v):
            $counter += $v->machine_counter;
            $weight += sprintf('%0.2f', $v->production_weight);
            $qty += $v->production_pieces_quantity;
            $waste += $v->runner_waste;
            $rejection += sprintf('%0.2f', $v->component_rejection);
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <?php
                    $delete = route("production.molding.delete", ['molding' => $v->id]);

                    if ($editPermission) { ?>
                        <a class='btn edit-btn  btn-action bg-success text-white mb-2' data-id='{{$v->id}}'
                            data-remark='{{$v->remark}}' data-date='{{$v->date}}' data-shift_id='{{$v->shift_id}}'
                            data-machine_id='{{$v->machine_id}}' data-operator_id='{{$v->operator_id}}'
                            data-product_type_id='{{$v->product_type_id}}' data-row_material_id='{{$v->row_material_id}}'
                            data-cavity_id='{{$v->cavity_id}}' data-item_id='{{$v->item_id}}'
                            data-machine_counter='{{$v->machine_counter}}' data-production_weight='{{$v->production_weight}}'
                            data-production_pieces_quantity='{{$v->production_pieces_quantity}}'
                            data-runner_waste='{{$v->runner_waste}}' data-component_rejection='{{$v->component_rejection}}'
                            data-color_type='{{$v->color_type}}' data-bs-toggle='tooltip' data-bs-placement='top'
                            data-bs-original-title='Edit' href='javascript:void(0);'>
                            <i class='far fa-edit' aria-hidden='true'></i>
                        </a><br>
                    <?php    }
                    if ($deletePermission) { ?>
                        <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-id='{{$v->id}}'
                            data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Delete' href='{{$delete}'>
                    <i class='fa-solid fa-trash'></i>
                </a>
                <?php    } ?>
            </td>
            <td><?= $v->date; ?></td>
            <td><?= $v->operator_name; ?></td>
            <td><?= $v->shift->name ?></td>
            <td><?= $v->machine->name; ?></td>
            <td><?= $v->productType->name; ?></td>
            <td><?= $v->rowMaterial->name; ?></td>
            <td><?= $v->cavity->name; ?></td>
            <td><?= $v->item->name; ?></td>
            <td><?= $v->machine_counter ?></td>
            <td><?= $v->production_weight ?></td>
            <td><?= $v->production_pieces_quantity ?></td>
            <td><?= $v->runner_waste ?></td>
            <td><?= $v->component_rejection ?></td>
            <td><?= $v->color_type; ?></td>
            <td><?= $v->createdBy->name; ?></td>
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
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><b>
                        <h3 class="totalColor"><?= $counter ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $weight ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $qty ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $waste ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $rejection ?></h3>
                    </b></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>
