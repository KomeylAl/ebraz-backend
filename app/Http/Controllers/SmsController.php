<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function singleSms(Request $request) {

        $curl = curl_init();

        $data = [
            "lineNumber" => "9982005424",
            "messageText" => $request->text,
            "mobiles" => [$request->phone]
            ];

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.sms.ir/v1/send/bulk',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
            'X-API-KEY: zkogPOvFAhdiDljYFkHMlAe4poPkaOGup1YzVK7NnHqplwCD',
            'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        return response()->json($response, 200);
    }

    public function multiSms(Request $request) {
        $data = [
            "lineNumber" => "9982005424",
            "messageText" => $request->text,
            "mobiles" => $request->phones
            ];
            
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sms.ir/v1/send/bulk',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
            'X-API-KEY: zkogPOvFAhdiDljYFkHMlAe4poPkaOGup1YzVK7NnHqplwCD',
            'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        // $error = curl_error($curl);
        curl_close($curl);
        // echo $response;
        if (curl_error($curl)) {
            return response()->json(curl_error($curl), 500);
        }

        return response($request, 200);

    }
}
