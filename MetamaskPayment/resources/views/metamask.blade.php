<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/nucleo/css/nucleo.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" type="text/css">
    <!-- Page plugins -->
    <!-- Argon CSS -->
    <link rel="stylesheet" href="{{ asset('css/argon.css?v=1.2.0') }}" type="text/css">
    <title>Metamask</title>
    <style>
        .breadcrumb-dark .breadcrumb-item a {
            color: #2b2c5ab0;
        }

        .breadcrumb-dark .breadcrumb-item.active {
            color: #2b2c5ab0;
        }

        .breadcrumb-dark .breadcrumb-item+.breadcrumb-item::before {
            color: #2b2c5ab0;
        }

        .footer .copyright {
            font-size: .8rem;
        }

        .main-content-height {
            min-height: 400px;
        }

    </style>
</head>

<body>


<div class="row justify-content-center">
    <div class="col-lg-4 text-center">

        <div class="form-group">

            <h3>Enter Amount Here</h3>
            <input type="text" class="form-control" name="amount" id="inp_amount" aria-describedby="helpId"
                   placeholder="Enter Amount In USD">
        </div>
        <div id="message" ></div>
        <br>
        <button type="button" onClick="startProcess()" class="btn btn-success">Pay Now</button>
    </div>
</div>
{{--<div class="row">--}}
{{--    <div class="col-lg-12" style="margin-top:100px;">--}}
{{--        <div class="card">--}}
{{--            <div class="card-body">--}}
{{--                <h5 class="card-title">Transactions</h5>--}}
{{--                <table class="table">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>#</th>--}}
{{--                        <th>TxHash</th>--}}
{{--                        <th>Amount</th>--}}
{{--                        <th>Status</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @forelse ($transactions as $key => $transaction)--}}
{{--                        <tr>--}}
{{--                            <td scope="row">{{ $key+1 }}</td>--}}
{{--                            <td>{{ $transaction->txHash }}</td>--}}
{{--                            <td>{{ $transaction->amount }} ETH</td>--}}
{{--                            <td>--}}
{{--                                @switch($transaction->status)--}}
{{--                                    @case(1)--}}
{{--                                    <span class="badge badge-warning">Pending</span>--}}
{{--                                    @break--}}
{{--                                    @case(2)--}}
{{--                                    <span class="badge badge-success">Success</span>--}}
{{--                                    @break--}}
{{--                                    @case(3)--}}
{{--                                    <span class="badge badge-danger">Declined</span>--}}
{{--                                    @break--}}
{{--                                @endswitch--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @empty--}}
{{--                    @endforelse--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/js-cookie/js.cookie.js') }}"></script>
<script src="{{ asset('vendor/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js') }}"></script>
<!-- Optional JS -->
<script src="{{ asset('vendor/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/dist/Chart.extension.js') }}"></script>
<!-- Argon JS -->
<script src="{{ asset('js/argon.js?v=1.2.0') }}"></script>
<script>
    function startProcess() {
        if ($('#inp_amount').val() > 0) {
            // run metamsk functions here
            EThAppDeploy.loadEtherium();
        } else {
            alert('Please Enter Valid Amount');
        }
    }


    EThAppDeploy = {
        loadEtherium: async () => {
            if (typeof window.ethereum !== 'undefined') {
                EThAppDeploy.web3Provider = ethereum;
                EThAppDeploy.requestAccount(ethereum);
            } else {
                alert(
                    "Not able to locate an Ethereum connection, please install a Metamask wallet"
                );
            }
        },
        /****
         * Request A Account
         * **/
        requestAccount: async (ethereum) => {
            ethereum
                .request({
                    method: 'eth_requestAccounts'
                })
                .then((resp) => {
                    //do payments with activated account
                    EThAppDeploy.payNow(ethereum, resp[0]);
                })
                .catch((err) => {
                    // Some unexpected error.
                    console.log(err);
                });
        },
        /***
         *
         * Do Payment
         * */
        payNow: async (ethereum, from) => {
            var amount = $('#inp_amount').val();
            ethereum
                .request({
                    method: 'eth_sendTransaction',
                    params: [{
                        from: from,
                        to: "0x14DE05287a3859947a41119f04CBCD132CF84680",
                        value: '0x' + ((amount * 1000000000000000000).toString(16)),
                    }, ],
                })
                .then((txHash) => {
                    if (txHash) {
                        console.log(txHash);
                        storeTransaction(txHash, amount);
                    } else {
                        console.log("Something went wrong. Please try again");
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    }
    /***
     *
     * @param Transaction id
     *
     */
    function storeTransaction(txHash, amount) {
        $.ajax({
            url: "{{ route('metamask_payment') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
                txHash: txHash,
                amount: amount,
            },
            success: function (response) {
                // console.log("Payment Done");
                window.location.reload();
                document.getElementById('message').innerHTML = 'Payment Done.';
                // reload page after success

            }
        });
    }

</script>
</body>
</html>
