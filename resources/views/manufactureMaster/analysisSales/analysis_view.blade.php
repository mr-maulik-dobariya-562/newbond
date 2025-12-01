<div class="row">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Order.</div>
                </div>
                <div class="h1 mb-3">{{ $data['totalOrders'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Order Qty.</div>
                </div>
                <div class="h1 mb-3">{{ $data['ordersTotalQty'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Order Pending Qty.</div>
                </div>
                <div class="h1 mb-3">{{ $data['ordersPendingQty'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 pt-3">
        <div class="card">
            <div class="card-body">
                <div style="width: 100%; margin: 0; float: left;">
                    <div style="text-align: center;">
                        <h3>Top Customer</h3>
                    </div>
                    <canvas id="topCustomer"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 pt-3">
        <div class="card">
            <div class="card-body">
                <div style="width: 100%; margin: 0; float: left;">
                    <div style="text-align: center;">
                        <h3>Top Item</h3>
                    </div>
                    <canvas id="topItem"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 pt-3">
        <div class="card">
            <div class="card-body">
                <div style="width: 100%; margin: 0; float: left;">
                    <div style="text-align: center;">
                        <h3>Sales Graph</h3>
                    </div>
                    <canvas id="salesGraph"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 pt-3">
        <div class="card">
            <div class="card-body">
                <div style="width: 100%; margin: 0; float: left;">
                    <div style="text-align: center;">
                        <h3>Order Graph</h3>
                    </div>
                    <canvas id="orderGraph"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 pt-3">
        <div class="card">
            <div class="card-body">
                <div style="width: 100%; margin: 0; float: left;">
                    <div style="text-align: center;">
                        <h3>Sale Party Group Graph</h3>
                    </div>
                    <canvas id="salePartyGroup"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
