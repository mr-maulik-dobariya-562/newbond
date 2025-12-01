<table id="example_table" class="table card-table table-vcenter text-nowrap datatable" data-page_length="500"
    data-responsive="false">
    <thead>
        <?php
$qty = 0;
$rate = 0;
$amount = 0;
$discount = 0;
foreach ($data as $v) {
    $qty += $v->qty;
    $rate += $v->rate;
    $amount += $v->amount;
    $discount += $v->discount;
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
            <th>QTY</br><b><?= $qty ?></b></th>
            <th>Rate</br><b><?= $rate ?></b></th>
            <th>Amount</br><b><?= $amount ?></b></th>
            <th>Discount</br><b><?= $discount ?></b></th>
            <th>LR Photo</th>
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
foreach ($data as $v):
    $qty += $v->qty;
    $rate += $v->rate;
    $amount += $v->amount;
    $discount += $v->discount;
        ?>
        <tr>
            <td style="color: <?=$v->color?>;"><?= $i++ ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->item_name . ' - ' . $v->print_type; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->customer ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->city; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->date; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->transports; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->block; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->narration; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->remark; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->qty ?></td>
            <td style="color: <?=$v->color?>;"><?= sprintf('%0.2f', $v->rate) ?></td>
            <td style="color: <?=$v->color?>;"><?= sprintf('%0.2f', $v->amount) ?></td>
            <td style="color: <?=$v->color?>;"><?= sprintf('%0.2f', $v->discount) ?></td>
            <td>
                <?php    if (isset($v->lr_photo) && !empty($v->lr_photo)) {?>
                <img src="{{ asset('storage/lrPhoto/' . $v->lr_photo) }}" width="50px" height="50px">
                <?php    } ?>
            </td>
            <td style="color: <?=$v->color?>;"><?= $v->remark; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->created_by_name; ?></td>
            <td style="color: <?=$v->color?>;"><?= $v->updated_by_name; ?></td>
            <td style="color: <?=$v->color?>;"><?= date("d-m-Y H:i:s", strtotime($v->createdAt)); ?></td>
            <td style="color: <?=$v->color?>;"><?= date("d-m-Y H:i:s", strtotime($v->updatedAt)); ?></td>
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
                    <h3 class="totalColor"><?= $qty ?></h3>
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
