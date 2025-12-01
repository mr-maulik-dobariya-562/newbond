<table class="table table-bordered table-striped datatable">
    <thead>
        <tr>
            <th>SR NO</th>
            <th>Item Name</th>
            <th>Opening Qty</th>
            <th>Pending Qty</th>
            <th>Estimate Qty</th>
            <th>Inward Qty</th>
            <th>Closing Qty</th>
            <th>Parcel</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalEstimateQty = 0;
        $totalInwardQty = 0;
        $totalClosingQty = 0;
        $totalPendingQty = 0;
        $totalParcel = 0;
        $loop = 1;
        foreach ($data as $index => $data) {
            $url = '';
            $totalPendingQty += $data->order_qty - $data->order_dispatch_qty;
            $totalEstimateQty += $data->estimate_qty;
            $totalInwardQty += $data->inward_qty;
            $totalClosingQty += $data->inward_qty - $data->estimate_qty;
            $totalParcel += $data->packing != 0 ? round(($data->inward_qty - $data->estimate_qty) / $data->packing) : 0;
            ?>
            <tr>
                <td><?= $loop++ ?></td>
                <td><a href="{{ route('stock.stock-details', $data->id) }}" target="_blank">
                        <?= isset($data->item_name) && !empty($data->item_name) ? $data->item_name : ""; ?>
                    </a> - <?= isset($data->packing) && !empty($data->packing) ? $data->packing : ""; ?></td>
                <td><?= ($opening_stock[$index]->inward_qty != null ? $opening_stock[$index]->inward_qty : 0) - ($opening_stock[$index]->estimate_qty != null ? $opening_stock[$index]->estimate_qty : 0)  ?></td>
                <td>
                    <?= $data->order_qty - $data->order_dispatch_qty;?>
                </td>
                <td>
                    <?= $data->estimate_qty ?? 0 ?>
                </td>
                <td>
                    <?= $data->inward_qty ?? 0 ?>
                </td>
                <td>
                    <?= $data->inward_qty - $data->estimate_qty ?>
                </td>
                <td>
                    <?= $data->packing != 0 ? round(($data->inward_qty - $data->estimate_qty) / $data->packing) : 0 ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: center;">Total</th>
            <th>
                <?= $totalPendingQty ?>
            </th>
            <th>
                <?= $totalEstimateQty ?>
            </th>
            <th>
                <?= $totalInwardQty ?>
            </th>
            <th>
                <?= $totalClosingQty ?>
            </th>
            <th>
                <?= $totalParcel ?>
            </th>
        </tr>
    </tfoot>
</table>
