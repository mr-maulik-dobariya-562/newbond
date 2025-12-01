<table id="example_table" class="table card-table table-vcenter text-nowrap datatable" data-button="false">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Actions</th>
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
$qtyTotal = 0;
$parcelTotal = 0;
foreach ($data as $v):
    $qtyTotal += $v['qty'];
    $parcelTotal += $v['parcel'];
        ?>
        <tr>
            <td>
                <?= $i++ ?>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <a href="{{ route('inward.edit', ['inward' => $v['id']]) }}" data-bs-toggle='tooltip'
                        data-bs-placement='top' data-bs-original-title='Edit'
                        class="btn btn-action bg-warning text-white me-2"><i class="fas fa-edit"></i></a>
                    <a href="{{ route('inward.delete', ['inward' => $v['id']]) }}" data-bs-toggle='tooltip'
                        data-bs-placement='top' data-bs-original-title='Delete'
                        class="btn btn-action bg-danger text-white me-2 btn-delete"><i class="fas fa-trash"></i></a>
                </div>
            </td>
            <td><?= $v['date']; ?></td>
            <td><?=  round($v['qty']) ?></td>
            <td><?= $v['parcel']; ?></td>
            <td><?= $v['remark']; ?></td>
            <td><?= $v['createdBy']; ?></td>
            <td><?= date("d-m-Y H:i:s", strtotime($v['createdAt'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <thead>
            <tr>
                <th></th>
                <th>
                    <b>
                        <h3 class="totalColor">Total</h3>
                    </b>
                </th>
                <th></th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $qtyTotal ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= $parcelTotal ?></h3>
                    </b>
                </th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </tfoot>
</table>
