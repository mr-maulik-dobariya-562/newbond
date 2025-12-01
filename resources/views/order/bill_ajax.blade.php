<style>
    .dataTables_filter {
        margin-top: -26px !important;
    }

    .card-body {
        margin-top: -15px;
    }
</style>
<form action="{{ route('print.gatePassPdf') }}" method="post" target="_blank">
    @csrf
    <input type="hidden" name="type" id="type" value="">
    <button type="button" data-type="get-pass" class="print-button btn btn-outline-primary">Gate Pass</button>
    <button type="button" data-type="order" class="print-button btn btn-outline-primary ml-2">Gate Pass W/P</button>
    <button type="button" data-type="quotation" class="print-button btn btn-outline-primary ml-2">Quotation</button>
    <table class="table card-table table-hover table-vcenter text-nowrap datatable" data-responsive="false"
        data-page_length="500">
        <thead>
            <?php
                $qtyTotal = 0;
                $amountTotal = 0;
                $dispatchQty = 0;
                $pendingQty = 0;
                $cancelQty = 0;
                foreach ($data as $v) {
                    $qtyTotal += $v['qty'];
                    $amountTotal += $v['amount'];
                    $dispatchQty += $v['dispatch_qty'];
                    $pendingQty += $v['pending_qty'];
                    $cancelQty += $v['cancel_qty'];
                }
            ?>
            <tr>
                <th><input type="checkbox" class="all-check" style="width: 17px;height: 17px;"></th>
                <th>Sr. No.</th>
                <th>Actions</th>
                <th>Po No.</th>
                <th>Block Find</th>
                <th>Order No.</th>
                <th>Customer</th>
                <th>City</th>
                <th>Date</th>
                <th>QTY<br><b><?= $qtyTotal ?></b></th>
                <th>Dispatch QTY<br><b><?= $dispatchQty ?></b></th>
                <th>Pending QTY<br><b><?= $pendingQty ?></b></th>
                <th>Cancel QTY<br><b><?= $cancelQty ?></b></th>
                <th>Amount<br><b><?= round($amountTotal) ?></b></th>
                <th>Remark</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Create At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;  ?>
            <?php
                $qtyTotal = 0;
                $amountTotal = 0;
                $dispatchQty = 0;
                $pendingQty = 0;
                $cancelQty = 0;
                foreach ($data as $v):
                    $qtyTotal += $v['qty'];
                    $amountTotal += $v['amount'];
                    $dispatchQty += $v['dispatch_qty'];
                    $pendingQty += $v['pending_qty'];
                    $cancelQty += $v['cancel_qty'];
            ?>
            <tr>
                <td>
                    <input type="checkbox" class="print-check" name="id[]" value="{{ $v['id'] }}"
                        style="width: 17px;height: 17px;">
                </td>
                <td style="color: <?= $v['color'] ?>;">
                    <?= $i++ ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <?php    $editPermission = auth()->user()->hasPermissionTo("order-edit");
    if ($editPermission && $v['cancel_qty'] == 0) { ?>

                        <a href="{{route('order.edit', ['order' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Edit'
                            class="btn btn-action bg-warning text-white me-2"><i class="fas fa-edit"></i></a>
                        <?php    }
    $deletePermission = auth()->user()->hasPermissionTo("order-delete");
    if ($editPermission) { ?>
                        <a href="{{route('order.delete', ['order' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Delete'
                            class="btn btn-action bg-danger text-white me-2 btn-delete"><i class="fas fa-trash"></i></a>
                        <?php    } ?>
                        <a href="{{route('order.view', ['order' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='View'
                            class="btn btn-action bg-primary text-white me-2"><i class="fas fa-eye"></i></a>
                        <?php    $otherPermission = auth()->user()->hasPermissionTo("order-other");
    if ($otherPermission && $v['cancel_qty'] == 0) { ?>
                        <a href="{{route('order.cancel', ['order' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Cancel'
                            class="btn btn-action bg-danger confirmClick text-white me-2"><i
                                class="fas fa-cancel"></i></a>
                        <?php    } ?>
                        <?php    $otherPermission = auth()->user()->hasPermissionTo("order-other");
    if ($otherPermission && $v['cancel_qty'] > 0) { ?>
                        <a href="{{route('order.restoreQty', ['order' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Re Store'
                            class="btn btn-action bg-success confirmClick text-white me-2"><i
                                class="fas fa-reply"></i></a>
                        <?php    } ?>
                    </div>
                </td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['po_no'] ?></td>
                <?php    $editPermission = auth()->user()->hasPermissionTo("order-other"); ?>
                <td style="color: <?= $v['color'] ?>;">
                    <label class="form-check form-switch">
                        <input class="form-check-input check-item" type="checkbox" data-id="{{ $v['id'] }}" {{ ($v['block_find'] == 'Yes') ? 'checked' : '' }} <?= $editPermission ? '' : 'disabled' ?>>
                    </label>
                </td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['order_code'] ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['customer'] ?> ( <?= $v['party_type'] ?> )</td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['city']; ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['date']; ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['qty'] ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['dispatch_qty'] ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['pending_qty'] ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['cancel_qty'] ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= round($v['amount']) ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['discription']; ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['created_by_name'] ?? '' ?>
                </td>
                <td style="color: <?= $v['color'] ?>;"><?= $v['order_type'] == 'offline' ? $v['updated_by_name'] : '' ?>
                </td>
                <td style="color: <?= $v['color'] ?>;"><?= date("d-m-Y H:i:s", strtotime($v['createdAt'])); ?></td>
                <td style="color: <?= $v['color'] ?>;"><?= date("d-m-Y H:i:s", strtotime($v['updatedAt'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
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
                <th><b>
                        <h3 class="totalColor"><?= $qtyTotal ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $dispatchQty ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $pendingQty ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $cancelQty ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= round($amountTotal) ?></h3>
                    </b></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</form>
