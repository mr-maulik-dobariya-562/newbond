<style>
    .dataTables_filter {
        /* margin-top: -26px !important; */
    }

    .card-body {
        margin-top: -15px;
    }
</style>
<form action="{{ route('print.getEstimatePdf') }}" method="post" target="_blank">
    @csrf
    <input type="hidden" name="type" id="type" value="">
    <input type="hidden" name="status" id="status" value="">
    <button type="button" data-type="estimate" class="print-button btn btn-outline-primary">Estimate</button>
    <button type="button" data-type="estimate-register" class="print-button btn btn-outline-primary ml-2">Estimate
        Register</button>
    <button type="button" data-type="register-excel" class="print-button btn btn-outline-primary ml-2">Estimate Register
        Excel</button>
    <button type="button" data-type="estimate-excel" class="print-button btn btn-outline-primary ml-2">Bill
        Excel</button>
    <button type="button" data-type="summary-pdf" class="summary btn btn-outline-primary ml-2">Summary PDF</button>
    <button type="button" data-type="summary-excel" class="summary btn btn-outline-primary ml-2">Summary Excel</button>
    <button type="button" data-type="cover-print" class="print-button btn btn-outline-primary">Cover Print</button>
    <table class="table card-table table-hover table-vcenter text-nowrap datatable" data-responsive="false"
        data-page_length="500">
        <thead>
            <?php
$qtyTotal = 0;
$amountTotal = 0;
$disTotal = 0;
$netTotal = 0;
foreach ($data as $v) {
    $qtyTotal += $v['qty'];
    $amountTotal += $v['total_amount'];
    $disTotal += $v['discount_amount'];
    $netTotal += $v['net_amount'];
}
			?>
            <tr>
                <th><input type="checkbox" class="all-check" style="width: 17px;height: 17px;"></th>
                <th>Sr.<br>No.</th>
                <th>Actions</th>
                <th>E Code</th>
                <th>Date</th>
                <th>PO<br>NO</th>
                <th>Bill<br> Gene</th>
                <th>Customer</th>
                <th>City</th>
                <th>QTY</br><b><?= $qtyTotal ?></b></th>
                <th>Parcel</th>
                <th>Total Amt</br><b><?= $amountTotal ?></b></th>
                <th>Dis</br><b><?= $disTotal ?></b></th>
                <th>Net Amt</br><b><?= $netTotal ?></b></th>
                <th>Transports</th>
                <th>LR No</th>
                <th>LR Date</th>
                <th>Docket No</th>
                <th>Courier</th>
                <th>LR Photo</th>
                <th>Remark</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Create At</th>
                <th>Update At</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php
$qtyTotal = 0;
$amountTotal = 0;
$disTotal = 0;
$netTotal = 0;
foreach ($data as $v):
    $qtyTotal += $v['qty'];
    $amountTotal += $v['total_amount'];
    $disTotal += $v['discount_amount'];
    $netTotal += $v['net_amount'];
			?>
            <tr>
                <td>
                    <input type="checkbox" class="print-check" name="id[]" value="{{ $v['id'] }}"
                        style="width: 17px;height: 17px;">
                </td>
                <td style="color: <?=$v['color']?>;">
                    <?= $i++ ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <?php    $editPermission = auth()->user()->hasPermissionTo("estimate-edit");
    if ($editPermission) {?>
                        <a href="{{route('estimate.edit', ['estimate' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Edit'
                            class="btn btn-action bg-warning text-white"><i class="fas fa-edit"></i></a>
                        <?php    }
    $deletePermission = auth()->user()->hasPermissionTo("estimate-delete");
    if ($editPermission) {?>
                        <a href="{{route('estimate.delete', ['estimate' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Delete'
                            class="btn btn-action bg-danger text-white btn-delete"><i class="fas fa-trash"></i></a>
                        <?php    } ?>
                        <a href="{{route('estimate.view', ['estimate' => $v['id']])}}" data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='View'
                            class="btn btn-action bg-primary text-white"><i class="fas fa-eye"></i></a>
                    </div>
                </td>
                <td style="color: <?=$v['color']?>;"><?= $v['estimate_code'] ?></td>
                <td style="color: <?=$v['color']?>;"><?= $v['date']; ?></td>
                <td style="color: <?=$v['color']?>;"><?= $v['po_no'] ?></td>
                <td style="color: <?=$v['color']?>;">
                    <?php    $editPermission = auth()->user()->hasPermissionTo("estimate-other");?>
                    <label class="form-check form-switch">
                        <input class="form-check-input check-item checkItem" type="checkbox" data-id="{{ $v['id'] }}"
                            <?= ($v['bill_generated'] == 'Yes') ? 'checked' : '' ?> <?= $editPermission ? '' : 'disabled' ?>>
                    </label>
                </td>
                <td style="color: <?=$v['color']?>;"><?= $v['customer'] ?> ( <?=$v['party_type']?> )</td>
                <td style="color: <?=$v['color']?>;"><?= $v['city']; ?></td>
                <td style="color: <?=$v['color']?>;"><?= $v['qty'] ?></td>
                <td style="color: <?=$v['color']?>;"><?= $v['parcel'] ?></td>
                <td style="color: <?=$v['color']?>;"><?= round($v['total_amount']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= round($v['discount_amount']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= round($v['net_amount']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= ($v['transport']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= ($v['lr_no']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= ($v['lr_date']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= ($v['docket']) ?></td>
                <td style="color: <?=$v['color']?>;"><?= ($v['courier_name']) ?></td>
                <td>
                    <?php    if (isset($v['lr_photo']) && !empty($v['lr_photo'])) {?>
                    <img src="{{ asset('storage/lrPhoto/' . $v['lr_photo']) }}" width="50px" height="50px">
                    <?php    } ?>
                </td>
                <td style="color: <?=$v['color']?>;"><?= $v['discription']; ?></td>
                <td style="color: <?=$v['color']?>;"><?= $v['created_by_name']; ?></td>
                <td style="color: <?=$v['color']?>;"><?= $v['updated_by_name']; ?></td>
                <td style="color: <?=$v['color']?>;"><?= date("d-m-Y H:i:s", strtotime($v['createdAt'])); ?></td>
                <td style="color: <?=$v['color']?>;"><?= date("d-m-Y H:i:s", strtotime($v['updatedAt'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th>
                    <b>
                        <h3 class="totalColor">Total</h3>
                    </b>
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= round($qtyTotal) ?></h3>
                    </b>
                </th>
                <th></th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= round($amountTotal) ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= round($disTotal) ?></h3>
                    </b>
                </th>
                <th>
                    <b>
                        <h3 class="totalColor"><?= round($netTotal) ?></h3>
                    </b>
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</form>
