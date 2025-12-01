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
    <table id="example_table" class="table table-hover card-table table-vcenter text-nowrap datatable"
        data-page_length="500" data-responsive="false">
        <thead>
            <?php
$qty = 0;
$rate = 0;
$amount = 0;
$discount = 0;
$dispatchQty = 0;
$pendingQty = 0;
$cancelQty = 0;
foreach ($data as $v) {
    $qty += $v->qty;
    $rate += $v->rate;
    $amount += $v->amount;
    $discount += $v->discount;
    $dispatchQty += $v->dispatch_qty;
    $pendingQty += $v->pending_qty;
    $cancelQty += $v->cancel_qty;
}
            ?>
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
                <th>QTY<br><b><?= $qty ?></b></th>
                <th>Dispatch QTY<br><b><?= $dispatchQty ?></b></th>
                <th>Pending QTY<br><b><?= $pendingQty ?></b></th>
                <th>Cancel Qty<br><b><?= $cancelQty ?></b></th>
                <th>Rate<br><b><?= $rate ?></b></th>
                <th>Amount<br><b><?= $amount ?></b></th>
                <th>Discount<br><b><?= $discount ?></b></th>
                <th>Remark</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Create At</th>
                <th>Update At</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;  ?>
            <?php
$qty = 0;
$rate = 0;
$amount = 0;
$discount = 0;
$dispatchQty = 0;
$pendingQty = 0;
$cancelQty = 0;
foreach ($data as $v):
    $qty += $v->qty;
    $rate += $v->rate;
    $amount += $v->amount;
    $discount += $v->discount;
    $dispatchQty += $v->dispatch_qty;
    $pendingQty += $v->pending_qty;
    $cancelQty += $v->cancel_qty;
            ?>
            <tr>
                <td style="color: <?= $v->color ?>;"><?= $i++ ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->item_name . ' - ' . $v->print_type; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->customer ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->city; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->date; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->transports; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->block; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->narration; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->remark; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->qty ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->dispatch_qty ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->pending_qty ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->cancel_qty ?></td>
                <td style="color: <?= $v->color ?>;"><?= sprintf('%0.2f', $v->rate) ?></td>
                <td style="color: <?= $v->color ?>;"><?= sprintf('%0.2f', $v->amount) ?></td>
                <td style="color: <?= $v->color ?>;"><?= sprintf('%0.2f', $v->discount) ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->remark; ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->created_by_name ?? '' ?></td>
                <td style="color: <?= $v->color ?>;"><?= $v->order_type == 'offline' ? $v->updated_by_name : '' ?></td>
                <td style="color: <?= $v->color ?>;"><?= date("d-m-Y H:i:s", strtotime($v->createdAt)); ?></td>
                <td style="color: <?= $v->color ?>;"><?= date("d-m-Y H:i:s", strtotime($v->updatedAt)); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
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
                <th><b>
                        <h3 class="totalColor"><?= $qty ?></h3>
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
                        <h3 class="totalColor"><?= $rate ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $amount ?></h3>
                    </b></th>
                <th><b>
                        <h3 class="totalColor"><?= $discount ?></h3>
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
