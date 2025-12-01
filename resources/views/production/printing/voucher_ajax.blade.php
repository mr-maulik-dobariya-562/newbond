<table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Action</th>
            <th>Date</th>
            <th>Operator</th>
            <th>Print Type</th>
            <th>Machine</th>
            <th>Working Hours</th>
            <th>Production Qty Pcs</th>
            <th>Rejection Qty Kg</th>
            <th>Rejection Qty Pcs</th>
            <th>Rejection Reason</th>
            <th>Remarks</th>
            <th>Created By</th>
            <th>Create At</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        $productionQty = 0;
        $rectionQty = 0;
        $rejectionQty = 0;
        foreach ($data as $v) :
            $productionQty += $v->production_qty;
            $rectionQty += $v->rection_qty;
            $rejectionQty += $v->rejection_qty;
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <?php
                    $delete = route("production.printing.delete", ['printing' => $v->id]);
                    $action = "";
                    if ($editPermission) {  ?>
                        <a class='btn edit-btn  btn-action bg-success text-white mb-2' data-id='{{{$v->id}}}' data-date='{{$v->date}}' data-print_type_id='{{$v->print_type_id}}' data-machine_id='{{$v->machine_id}}' data-operator_id='{{$v->operator_id}}' data-production_qty='{{$v->production_qty}}' data-rection_qty='{{$v->rection_qty}}' data-working_hours_id='{{$v->working_hours_id}}' data-rejection_qty='{{$v->rejection_qty}}' data-rejection_reason='{{$v->rejection_reason}}' data-remarks='{{$v->remarks}}' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                            <i class='far fa-edit' aria-hidden='true'></i>
                        </a><br>
                    <?php }
                    if ($deletePermission) { ?>
                        <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-id='{{$v->id}}' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Delete' href='{{$delete}}'>
                            <i class='fa-solid fa-trash'></i>
                        </a>
                    <?php }
                    ?>
                </td>
                <td><?= $v->date; ?></td>
                <td><?= $v->operator_name; ?></td>
                <td><?= $v->printType?->name ?></td>
                <td><?= $v->machine?->name; ?></td>
                <td><?= $v->workingHours?->name; ?></td>
                <td><?= $v->production_qty ?></td>
                <td><?= $v->rection_qty ?></td>
                <td><?= $v->rejection_qty ?></td>
                <td><?= $v->rejection_reason; ?></td>
                <td><?= $v->remarks; ?></td>
                <td><?= $v->createdBy?->name; ?></td>
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
                <th><b>
                        <h3 class="totalColor"><?= $productionQty ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $rectionQty ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $rejectionQty ?></h3>
                    </b></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>
