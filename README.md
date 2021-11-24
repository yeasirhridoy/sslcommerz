#SSL Commerz laravel integration (Popup)

Install the package
`composer require yahrdy/sslcommerz`

Define initiate and success method in a controller. Additionally, you can call checkout method to load front end.


    <?php
    
    namespace App\Http\Controllers;
    
    use Hridoy\SslCommerz\SslCommerzPay;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    
    class SslCommerzController extends Controller
    {
        public function checkout()
        {
            return view('sslcommerz::checkout');
        }
    
        public function initiate(Request $request)
        {
            return (new SslCommerzPay())->initiate($request);
        }
    
        public function success(Request $request): string
        {
            $data = $request->all();
            $trxId = $data['tran_id'];
            $amount = $data['amount'];
            $currency = $data['currency'];
            if ((new SslCommerzPay())->validate($request, $trxId, $amount, $currency)) {
                // (new SslCommerzPay())->validate($request, $trxId, $amount, $currency) will return validated respone from the SSL. Use this information to confirm the payment and update database.
                return "Payment successful and validated";
            } else {
                return "Payment is not verified";
            }
        }
    }

Call the api from front end. Example front end code-

    <!DOCTYPE html>
    <html lang="en">
    <head>
    </head>
    <body class="bg-light">
    <div class="container">
        <button class="btn btn-primary btn-lg btn-block" id="sslczPayBtn"
                postdata=""
                order="#"
                endpoint="api/initiate"> Pay Now
        </button>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/0.11.10/vue.min.js"></script>
    
    <!-- If you want to use the popup integration, -->
    <script>
        (function (window, document) {
            var loader = function () {
                var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                // script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR LIVE
                script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR SANDBOX
                tag.parentNode.insertBefore(script, tag);
            };
    
            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
        })(window, document);
    </script>
    <script>
    
        new Vue({
            el: '#container',
            data: {
                value: '',
            },
            created() {
                const obj = {};
                obj.cus_name = "Customer Name"
                obj.cus_phone = "01*********"
                obj.cus_email = 'example@email.com'
                obj.product_name = 'Product Name'
                obj.total_amount = "100"
                $('#sslczPayBtn').prop('postdata', obj);
            }
        });
    </script>
    </body>
    </html>
