<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="images/favicon.png" rel="icon" />
    <title>Invoice - {{$settings->app_name}} </title>
    <meta name="author" content="harnishdesign.net">

    <!-- Web Fonts -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900' type='text/css'>

    <!-- Stylesheet -->
    <link rel="stylesheet" type="text/css" href="{{asset('frontend/assets/css/bootstrap.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('global/css/pdf.css')}}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <!-- Container -->
  <div class="container-fluid invoice-container">
    <!-- Header -->
    <header>
        <div class="row align-items-center gy-3">
            <div class="col-sm-7 text-center text-sm-start">
                <img id="logo" src="{{asset($settings->logo)}}" width="120" alt="{{ $settings->app_name }}" />
            </div>
            <div class="col-sm-5 text-center text-sm-end">
                <h4 class="mb-0">3D Printing Invoice</h4>
                <p class="mb-0">Order # <strong>{{$order->order_number}}</strong></p>
                <p class="mb-0 text-muted" style="font-size: 0.9rem;">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>
        <hr>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Customer Info -->
        <div class="row align-items-start mb-4">
            <div class="col-sm-6 mb-3">
                <strong style="font-size: 1.1rem;">Customer Information</strong>
                <br><br>
                <strong>{{ $order->user->name }}</strong>
                <br>
                {{ $order->user->email }}
                @if($order->user->phone)
                    <br>
                    {{ $order->user->phone }}
                @endif
            </div>
            @if($order->address)
            <div class="col-sm-6 mb-3 text-sm-end">
                <strong style="font-size: 1.1rem;">Shipping Address</strong>
                <br><br>
                <span>
                    {{ $order->address->full_name }}
                    <br>
                    {{ $order->address->address }}
                    @if($order->address->address_2)
                        <br>
                        {{ $order->address->address_2 }}
                    @endif
                    <br>
                    {{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}
                    <br>
                    {{ $order->address->country }}
                    @if($order->address->phone)
                        <br>
                        Phone: {{ $order->address->phone }}
                    @endif
                </span>
            </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="row mb-3">
            <div class="col-12">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #667eea;">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-2">
                            <small class="text-muted d-block">Payment Method</small>
                            <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</strong>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <small class="text-muted d-block">Payment Status</small>
                            <strong>{{ ucfirst($order->payment_status) }}</strong>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <small class="text-muted d-block">Order Status</small>
                            <strong>{{ ucfirst($order->status) }}</strong>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <small class="text-muted d-block">Shipping Method</small>
                            <strong>{{ ucfirst(str_replace('_', ' ', $order->shipping_method ?? 'N/A')) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>

        <!-- Files List -->
        <h6 class="mb-3"><strong>Order Items ({{ $order->total_files }} Files)</strong></h6>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th>#</th>
                        <th>File Name</th>
                        <th>Technology</th>
                        <th>Material</th>
                        <th>Color</th>
                        <th class="text-end">Volume (cm³)</th>
                        <th class="text-center">Scale</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->files_data as $index => $file)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $file['name'] ?? 'File ' . ($index + 1) }}</strong>
                        </td>
                        <td>{{ $file['technology'] ?? 'N/A' }}</td>
                        <td>{{ $file['material'] ?? 'N/A' }}</td>
                        <td>
                            <span style="display: inline-block; width: 16px; height: 16px; background-color: {{ $file['color'] ?? '#ccc' }}; border: 1px solid #ddd; border-radius: 3px; vertical-align: middle;"></span>
                            <small>{{ $file['color'] ?? 'N/A' }}</small>
                        </td>
                        <td class="text-end">{{ number_format((float)($file['volume'] ?? 0), 2) }}</td>
                        <td class="text-center">{{ $file['scale'] ?? 100 }}%</td>
                        <td class="text-center">{{ $file['quantity'] ?? 1 }}</td>
                        <td class="text-end">${{ number_format((float)($file['price'] ?? 0), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="table-responsive mt-3">
            <table class="table mb-0">
                <tr>
                    <td colspan="2" class="text-end border-0 p-1">
                        <strong class="fw-medium">Total Volume:</strong>
                    </td>
                    <td class="col-sm-3 text-end border-0 p-1">
                        {{ number_format($order->total_volume, 2) }} cm³
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end border-0 p-1">
                        <strong class="fw-medium">Files Subtotal:</strong>
                    </td>
                    <td class="col-sm-3 text-end border-0 p-1">
                        ${{ number_format($order->total_price, 2) }}
                    </td>
                </tr>
                @if($order->shipping_charge > 0)
                <tr>
                    <td colspan="2" class="text-end border-0 p-1">
                        <strong class="fw-medium">Shipping Charge:</strong>
                    </td>
                    <td class="col-sm-3 text-end border-0 p-1">
                        ${{ number_format($order->shipping_charge, 2) }}
                    </td>
                </tr>
                @endif
                <tr style="background: #f8f9fa;">
                    <td colspan="2" class="text-end border-0 p-2">
                        <strong class="fw-bold" style="font-size: 1.1rem;">Grand Total:</strong>
                    </td>
                    <td class="col-sm-3 text-end border-0 p-2">
                        <strong class="text-success" style="font-size: 1.2rem;">${{ number_format($order->total_price + $order->shipping_charge, 2) }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        @if($order->customer_note)
        <div class="mt-4">
            <strong>Customer Note:</strong>
            <p class="text-muted">{{ $order->customer_note }}</p>
        </div>
        @endif

        @if($order->notes)
        <div class="mt-3">
            <strong>Admin Notes:</strong>
            <p class="text-muted">{{ $order->notes }}</p>
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="text-center mt-4">
        <hr>
        <div class="btn-group btn-group-sm d-print-none">
            <button type="button" class="btn btn-light border text-black-50 shadow-none print-btn">
                <i class="fa fa-print"></i> Print & Download
            </button>
        </div>
        <p class="text-muted mt-3" style="font-size: 0.85rem;">
            Thank you for your business! If you have any questions, please contact us.
        </p>
    </footer>
  </div>

  <!-- Back Link -->
  <p class="text-center d-print-none mt-3">
    <a href="{{ url()->previous() }}">&laquo; Go Back</a>
  </p>

  <!-- Scripts -->
   <script type="text/javascript" src="{{asset('global/js/jquery-3.7.1.js')}}"></script>
   <script type="text/javascript" src="{{asset('global/js/pdf.js')}}"></script>

</body>
</html>
