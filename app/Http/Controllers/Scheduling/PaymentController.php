<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\PaymentRequest;
use App\Models\Scheduling\Payment;
use App\Models\Scheduling\Reserve;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            $response = '';

            if($request->get('type') == 1) {
                $response = $this->creditCardPayment($request->all());
            }

            if($request->get('type') == 2) {
                $response = $this->psePayment($request->all());
            }

            return response()->json($response, 200);

            if(is_null($response->error)) {
                $payment = new Payment();
                $payment->reserve = $request->get('reserve');
                $payment->type = $request->get('type');
                $payment->name = $request->get('name');
                $payment->type_document = $request->get('type_document');
                $payment->document = $request->get('identification');
                $payment->phone = $request->get('phone');
                $payment->total = $request->get('total');
                $payment->authorizationCode = $response->transactionResponse->authorizationCode;
                $payment->orderId = $response->transactionResponse->orderId;
                $payment->state = $response->transactionResponse->state;
                $payment->trazabilityCode = $response->transactionResponse->trazabilityCode;
                $payment->save();
            }

            $reserve = Reserve::find($request->get('reserve'));

            if($response->transactionResponse->state == 'APPROVED') {
                $reserve->status = 3;
                $reserve->update();
            }

            if($response->transactionResponse->state == 'DECLINED') {
                $reserve->status = 4;
                $reserve->update();
            }

            if($response->transactionResponse->state == 'EXPIRED') {
                $reserve->status = 5;
                $reserve->update();
            }

            if($response->transactionResponse->state == 'PENDING') {
                $reserve->status = 6;
                $reserve->update();
            }

            $response = [
                "status" => $response->transactionResponse->state
            ];

            DB::commit();
            return response()->json($response, 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PaymentController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function banksList()
    {
        try {

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $apiLogin = config('payu.apiLogin');
            $apiKey = config('payu.apiKey');
            $apiURL = config('payu.apiURL');

            $body = [
                'language' => 'es',
                'command' => 'GET_BANKS_LIST',
                "merchant" => [
                    "apiLogin" => $apiLogin,
                    "apiKey" => $apiKey
                ],
                "test" => false,
                "bankListInformation" => [
                    "paymentMethod" => "PSE",
                    "paymentCountry" => "CO"
                ]
            ];

            $services = $client->post($apiURL, ['json' => $body, 'verify' => false] );

            $bankList = $services->getBody()->getContents();
            $bankList = json_decode($bankList);

            return response()->json($bankList, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PaymentController:banksList', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function creditCardPayment($params)
    {
        try {

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $apiLogin = config('payu.apiLogin');
            $apiKey = config('payu.apiKey');
            $apiURL = config('payu.apiURL');
            $accountId = config('payu.accountId');

            $body = [
                "test"                          => true,
                "language"                      => "es",
                "command"                       => "SUBMIT_TRANSACTION",
                "merchant" => [
                    "apiLogin"                  => $apiLogin,
                    "apiKey"                    => $apiKey
                ],
                "transaction" => [
                    "order" => [
                        "accountId"             => $accountId,
                        "referenceCode"         => $params['reference'],
                        "description"           => "Compra de servicios 4Home",
                        "language"              => "es",
                        "additionalValues"      => [
                            "TX_VALUE" => [
                                "value"         => "100000",
                                "currency"      => "COP"
                            ],
                            "TX_TAX" => [
                                "value"         => 0,
                                "currency"      => "COP"
                            ],
                            "TX_TAX_RETURN_BASE" => [
                                "value"         => 0,
                                "currency"      => "COP"
                            ]
                        ],
                        "buyer" => [
                            "fullName"          => $params['name'],
                            "emailAddress"      => $params['email'],
                            "contactPhone"      => $params['phone'],
                            "dniNumber"         => $params['identification'],
                        ],
                    ],
                    "payer" => [
                        "fullName"              => $params['name'],
                        "emailAddress"          => $params['email'],
                        "contactPhone"          => $params['phone'],
                        "dniNumber"             => $params['identification'],
                        "dniType"               => $params['type_document'],
                    ],
                    "creditCard" => [
                        "number"                => str_replace(' ', '', $params['cardNumber']),
                        "securityCode"          => $params['securityCode'],
                        "expirationDate"        => $params['expirationDate'],
                        "name"                  => $params['name']
                    ],
                    "extraParameters" => [
                        "INSTALLMENTS_NUMBER"   => $params['dues']
                    ],
                    "type"                      => "AUTHORIZATION_AND_CAPTURE",
                    "paymentMethod"             => $params['card'],
                    "paymentCountry"            => "CO",
                ]
            ];

            $services = $client->post($apiURL, ['json' => $body, 'verify' => false] );

            $response = $services->getBody()->getContents();
            return json_decode($response);

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PaymentController:creditCardPayment', $e->getMessage()));
            return $e->getMessage();
        }
    }

    public function psePayment($params)
    {
        try {

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $apiLogin = config('payu.apiLogin');
            $apiKey = config('payu.apiKey');
            $apiURL = config('payu.apiURL');
            $accountId = config('payu.accountId');

            $body = [
                "test"                          => true,
                "language"                      => "es",
                "command"                       => "SUBMIT_TRANSACTION",
                "merchant" => [
                    "apiLogin"                  => $apiLogin,
                    "apiKey"                    => $apiKey
                ],
                "transaction" => [
                    "order" => [
                        "accountId"             => $accountId,
                        "referenceCode"         => $params['reference'],
                        "description"           => "Compra de servicios 4Home",
                        "language"              => "es",
                        "additionalValues"      => [
                            "TX_VALUE" => [
                                "value"         => "100000",
                                "currency"      => "COP"
                            ],
                            "TX_TAX" => [
                                "value"         => 0,
                                "currency"      => "COP"
                            ],
                            "TX_TAX_RETURN_BASE" => [
                                "value"         => 0,
                                "currency"      => "COP"
                            ]
                        ],
                        "buyer" => [
                            "fullName"          => $params['name'],
                            "emailAddress"      => $params['email'],
                            "contactPhone"      => $params['phone'],
                            "dniNumber"         => $params['identification'],
                        ],
                    ],
                    "payer" => [
                        "fullName"              => $params['name'],
                        "emailAddress"          => $params['email'],
                        "contactPhone"          => $params['phone'],
                        "dniNumber"             => $params['identification'],
                        "dniType"               => $params['type_document'],
                    ],
                    "extraParameters" => [
                        "FINANCIAL_INSTITUTION_CODE"    => $params['bank'],
                        "USER_TYPE"             => 'N',
                        "PSE_REFERENCE2"        => $params['type_document'],
                        "PSE_REFERENCE3"        => $params['identification']
                    ],
                    "type"                      => "AUTHORIZATION_AND_CAPTURE",
                    "paymentMethod"             => "PSE",
                    "paymentCountry"            => "CO",
                ]
            ];

            $services = $client->post($apiURL, ['json' => $body, 'verify' => false] );

            $response = $services->getBody()->getContents();
            return json_decode($response);

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PaymentController:creditCardPayment', $e->getMessage()));
            return $e->getMessage();
        }
    }
}
