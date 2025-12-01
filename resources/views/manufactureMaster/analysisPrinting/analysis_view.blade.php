<div class="row">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Production Qty.</div>
                </div>
                <div class="h1 mb-3">{{$data['productionPiecesQuantity']}}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Rejection Qty.</div>
                </div>
                <div class="h1 mb-3">{{$data['rectionPiecesQuantity']}}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">

    </div>
    <div style="width: 100%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Production Quantity & Rejection Quantity by Month</h3>
        </div>
        <canvas id="productionChart"></canvas>
    </div>
    <div style="width: 100%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Production Quantity by Day of Month</h3>
        </div>
        <canvas id="weightChart"></canvas>
    </div>
    <div style="width: 100%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Rejection Quantity by Day of Month</h3>
        </div>
        <canvas id="rejectionChart"></canvas>
    </div>
    <div style="width: 100%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Production Quantity VS Rejection Quantity by Machine Number</h3>
        </div>
        <canvas id="machineChart"></canvas>
    </div>
    <div style="width: 40%; margin: 0; float: left;">
        <div style="text-align: center;">
            <h3>Production Quantity VS Rejection Quantity by Machine Type</h3>
        </div>
        <canvas id="machineTypeChart"></canvas>
    </div>
    <div style="width: 40%; margin: 0; float: left;">
        <div style="text-align: center;">
            <h3>Production Quantity VS Rejection Quantity by Operator Name</h3>
        </div>
        <canvas id="OperatorChart"></canvas>
    </div>
</div>
