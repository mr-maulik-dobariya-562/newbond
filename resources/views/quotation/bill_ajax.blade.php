<form action="{{ route('quotation.gatePassPdf') }}" method="post" target="_blank">
    @csrf
    <input type="hidden" name="type" id="type" value="">
    <button type="button" data-type="get-pass" class="print-button btn btn-outline-primary">Quotation</button>
    <table id="example_table" class="table card-table table-vcenter text-nowrap datatable">
        <thead>
            <tr>
                <th><input type="checkbox" class="all-check"></th>
                <th>Sr. No.</th>
                <th>Actions</th>
                <th>Quotation No.</th>
                <th>Customer</th>
                <th>City</th>
                <th>Date</th>
                <th>QTY</th>
                <th>Amount</th>
                <!-- <th>Block Find</th> -->
                <th>Remark</th>
                <th>Create At</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;  ?>
            <?php
            $qtyTotal = 0;
            $amountTotal = 0;
            foreach ($data as $v) :
                $qtyTotal += $v['qty'];
                $amountTotal += $v['amount'];
            ?>
                <tr>
                    <td>
                        <input type="checkbox" class="print-check" name="id[]" value="{{ $v['id'] }}">
                    </td>
                    <td>
                        <?= $i++ ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{route('quotation.edit', ['quotation' => $v['id']])}}"
                                data-bs-toggle='tooltip'
                                data-bs-placement='top'
                                data-bs-original-title='Edit'
                                class="btn btn-action bg-warning text-white me-2"><i class="fas fa-edit"></i></a>
                            <a href="{{route('quotation.delete', ['quotation' => $v['id']])}}" 
                                data-bs-toggle='tooltip'
                                data-bs-placement='top' 
                                data-bs-original-title='Delete' 
                                class="btn btn-action bg-danger text-white me-2 btn-delete"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                    <td><?= $v['quotation_code'] ?></td>
                    <td><?= $v['customer'] ?></td>
                    <td><?= $v['city']; ?></td>
                    <td><?= $v['date']; ?></td>
                    <td><?= sprintf('%0.2f', $v['qty']) ?></td>
                    <td><?= sprintf('%0.2f', $v['amount']) ?></td>
                    <!-- <td>
                        <label class="form-check form-switch">
                            <input class="form-check-input check-item" type="checkbox" data-id="{{ $v['id'] }}" {{ ($v['block_find'] == 'Yes') ? 'checked' : '' }}>
                        </label>
                    </td> -->
                    <td><?= $v['remark']; ?></td>
                    <td><?= date("d-m-Y H:i:s", strtotime($v['createdAt'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <thead>
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
                    <th><b>
                            <h3 class="totalColor"><?= $qtyTotal ?></h3>
                        </b></th>
                    <th><b>
                            <h3 class="totalColor"><?= $amountTotal ?></h3>
                        </b></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
        </tfoot>
    </table>
</form>