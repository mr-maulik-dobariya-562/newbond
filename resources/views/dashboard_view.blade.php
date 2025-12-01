<div class="row">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Production Pieces Quantity</div>
                </div>
                <div class="h1 mb-3">{{$data['productionPiecesQuantity']}}</div>
                @foreach ($data['locationPiecesWiseData'] as $locationPieces)
                <div class="d-flex mb-2">
                    <div>{{ $locationPieces->location_name }}</div>
                    <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            {{ $locationPieces->percentage }}%
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                    </div>
                </div>
                <div class="progress progress-sm mb-2">
                    <div class="progress-bar bg-primary" style="width: {{ $locationPieces->percentage }}%;" role="progressbar" aria-valuenow="{{ $locationPieces->percentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $locationPieces->percentage }}% Complete">
                        <span class="visually-hidden">{{ $locationPieces->percentage }}% {{ $locationPieces->location_name }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Production Weight(Kgs.)</div>
                </div>
                <div class="h1 mb-3">{{$data['productionweight']}}</div>
                @foreach ($data['locationweightWiseData'] as $locationWeight)
                <div class="d-flex mb-2">
                    <div>{{ $locationWeight->location_name }}</div>
                    <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            {{ $locationWeight->percentage }}%
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                    </div>
                </div>
                <div class="progress progress-sm mb-2">
                    <div class="progress-bar bg-primary" style="width: {{ $locationWeight->percentage }}%;" role="progressbar" aria-valuenow="{{ $locationWeight->percentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $locationWeight->percentage }}% Complete">
                        <span class="visually-hidden">{{ $locationWeight->percentage }}% {{ $locationWeight->location_name }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Runner Waste (Kgs.)</div>
                </div>
                <div class="h1 mb-3">{{$data['runnerWaste']}}</div>
                <div class="d-flex align-items-center">
                    <div class="subheader">AVG Runner Waste</div>
                </div>
                <div class="h1 mb-3">{{$data['runnerWasteAvg']}}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Component Rejection(Kgs.)</div>
                </div>
                <div class="h1 mb-3">{{$data['componentRejection']}}</div>
                <div class="d-flex align-items-center">
                    <div class="subheader">AVG Component Rejection</div>
                </div>
                <div class="h1 mb-3">{{$data['componentRejectionAvg']}}</div>
            </div>
        </div>
    </div>
    <div style="width: 50%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Production Pieces Quantity by Month</h3>
        </div>
        <canvas id="productionChart"></canvas>
    </div>
    <div style="width: 50%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Production Weight by Month</h3>
        </div>
        <canvas id="productionweight"></canvas>
    </div>
    <div style="width: 70%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Component Rejection & Runner Waste</h3>
        </div>
        <canvas id="componentRunner"></canvas>
    </div>
    <div style="width: 30%; margin: 0; float: left;">
        <div class="page-header" style="text-align: center;">
            <h3>Location Wise Component Rejection (%)</h3>
        </div>
        <canvas id="locationComponent"></canvas>
    </div>
</div>
