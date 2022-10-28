<?php

namespace app\library;

use app\models\B2CPayments;
use yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class MpesaIntergration extends Component
{

    public $MpesaBaseURL = '';


    public function createPushSTKNotification($MemberphoneNo, $model)
    {
        // echo '<pre>';
        // print_r($model);
        // exit;
        $CompliantPhoneNo = '254' . $MemberphoneNo;

        $timeStamp = date("Ymdhms");
        $data = json_encode(array(
            "BusinessShortCode" => Yii::$app->params['Mpesa']['PartyB'],
            "Password" => base64_encode(Yii::$app->params['Mpesa']['PartyB'] . Yii::$app->params['Mpesa']['PassKey'] . $timeStamp),
            "Timestamp" =>  $timeStamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => $model->Amount,
            "PartyA" => $CompliantPhoneNo,
            "PartyB" => Yii::$app->params['Mpesa']['PartyB'],
            "PhoneNumber" =>  $CompliantPhoneNo,
            "CallBackURL" => Url::home(true) . 'mobilemoney/processcallback?LoanNo=' . $model->RefrenceNo . '&MemberPhoneNo=' . Yii::$app->user->identity->{'Mobile Phone No_'},
            "AccountReference" => $model->RefrenceNo,
            "TransactionDesc" => "Payment of Loan Payment"
        ));

        $ch = curl_init(Yii::$app->params['Mpesa']['CreateSTKURL']);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->Authenicate(),
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $decodedResponse = json_decode($response);
    }


    public function createPushSTKNotificationForAccountDeposit($MemberphoneNo, $model)
    {
        // echo '<pre>';
        // print_r($model);
        // exit;
        $CompliantPhoneNo = '254' . $MemberphoneNo;
        // exit($CompliantPhoneNo);
        $timeStamp = date("Ymdhms");
        $data = json_encode(array(
            "BusinessShortCode" => Yii::$app->params['Mpesa']['PartyB'],
            "Password" => base64_encode(Yii::$app->params['Mpesa']['PartyB'] . Yii::$app->params['Mpesa']['PassKey'] . $timeStamp),
            "Timestamp" =>  $timeStamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => $model->Amount,
            "PartyA" => $CompliantPhoneNo,
            "PartyB" => Yii::$app->params['Mpesa']['BusinessShortCode'],
            "PhoneNumber" =>  $CompliantPhoneNo,
            "CallBackURL" => 'https://ushurusacco.com/portal',
            "AccountReference" => $model->AccountNo,
            "TransactionDesc" => "Top up Account"
        ));


        $ch = curl_init(Yii::$app->params['Mpesa']['CreateSTKURL']);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->Authenicate(),
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response  = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    public function createPushSTKNotificationForMemberActivation($MemberphoneNo, $model)
    {
        // echo '<pre>';
        // print_r($model);
        // exit;
        $AmountToPay = (int)Yii::$app->params['IndividualCharge']; //Default
        if ($model->Member_Category == 'GRP') {
            $AmountToPay = (int)Yii::$app->params['GroupCharge'];
        }

        $CompliantPhoneNo = '254' . $MemberphoneNo;
        // exit($CompliantPhoneNo);
        $timeStamp = date("Ymdhms");
        $data = json_encode(array(
            "BusinessShortCode" => Yii::$app->params['Mpesa']['PartyB'],
            "Password" => base64_encode(Yii::$app->params['Mpesa']['PartyB'] . Yii::$app->params['Mpesa']['PassKey'] . $timeStamp),
            "Timestamp" =>  $timeStamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => $AmountToPay,
            "PartyA" => $CompliantPhoneNo,
            "PartyB" => Yii::$app->params['Mpesa']['BusinessShortCode'],
            "PhoneNumber" =>  $CompliantPhoneNo,
            "CallBackURL" => Url::home(true) . 'mobilemoney/processmemberapplicationcallback?MemberApplicationNo=' . $model->Application_No . '&MemberPhoneNo=' . Yii::$app->user->identity->{'Mobile Phone No_'},
            "AccountReference" => Yii::$app->params['Mpesa']['Company'],
            "TransactionDesc" => "Payment of Membership activation"
        ));

        $ch = curl_init(Yii::$app->params['Mpesa']['CreateSTKURL']);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->Authenicate(),
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $decodedResponse = json_decode($response);
    }

    public function Authenicate()
    {
        $url = Yii::$app->params['Mpesa']['AuthURL'];
        $ch = curl_init($url);

        $credentials = base64_encode(Yii::$app->params['Mpesa']['ConsumerKey'] . ":" . Yii::$app->params['Mpesa']['ConsumerSecret']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PASSWORD, $credentials);
        $response = json_decode(curl_exec($ch));
        if (isset($response->access_token)) { //Success
            return $response->access_token;
        }
        return false;
    }

    function EncryptApiPassword($plaintextPassword)
    {
        $fp = fopen("C:\inetpub\wwwroot\Mpesa Certificates\Certificates\SandBox\SandboxCertificate.cer", "r");
        $publicKey = fread($fp, filesize("C:\inetpub\wwwroot\Mpesa Certificates\Certificates\SandBox\SandboxCertificate.cer"));
        fclose($fp);
        openssl_get_publickey($publicKey);
        openssl_public_encrypt($plaintextPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
        return  base64_encode($encrypted);
    }


    public function SendMoneyToClient($Phone, $amount, $remarks, $ocassion, $RequestID, $QualifiedAmount)
    {
        $CompliantPhoneNo = '254' . $Phone;


        $curl = curl_init();

        $data = array(
            'InitiatorName'         =>     Yii::$app->params['Mpesa']['MpesaAPIUser'],
            'SecurityCredential'     =>     'UJBJ9xp7WNe3ByrstNgAkhibWQu3NtNS/ftQG5by8la/1f77YxKZn6z7FkgWTwXNZQ+D1h/q3iTC5Sw7qGEdzMwg1Ec1JhzIb+i7xkrLGiy/8sTT3eCDGfnwQs0NGtxctbWLjTF48Cbqd+Z26b4cCtblk6xl9PjE73YDSXLxgxannqThmPm3ORp2oBgbx2ydQLgqg744Q/gW1j98LFAFkEAb6bMcxn05iSDoxW7NBkVp+HVwUNFp1yn8o5guEKoDSb3mbLaqA+epvrmWPwZBpy9lRb6N/6yfET0Gmuc2GfEHexSWTLId0tOXHHCAZEAW2S+ij37e8tS6G5VgMnRyKg==',
            'CommandID'             =>     'BusinessPayment',
            'Amount'                 =>     str_replace(',', '', $amount),
            'PartyA'                 =>     Yii::$app->params['Mpesa']['BusinessShortCode'],
            'PartyB'                 =>     $CompliantPhoneNo, //Yii::$app->params['Mpesa']['PhoneNumber'],
            'Remarks'                 =>     $remarks,
            'QueueTimeOutURL'         =>  Url::home(true) . 'mobilemoney/timeouturl?PhoneNo=' . $CompliantPhoneNo . '&RequestID=' . $RequestID . '&QualifiedAmount=' . $QualifiedAmount,
            'ResultURL'             =>     Url::home(true) . 'mobilemoney/processb2c-request-callback?PhoneNo=' . $CompliantPhoneNo . '&RequestID=' . $RequestID . '&QualifiedAmount=' . $QualifiedAmount,
            'Occasion'                 =>     $ocassion
        );

        // exit;
        // exit;
        // echo '<pre>';
        // print_r($data);
        // exit;

        curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['Mpesa']['PaymentRequestURL'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Authenicate(),
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
        // echo $response;

    }

    public function SendSMS($Message, $PhoneNo)
    {
        $data = [
            'phoneNumber' => $PhoneNo,
            'sms' => $Message,
            'channel' => 'NAV_ADMIN',
            'deviceId' => '2345412341561',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://mobileapigateway.ekenya.co.ke:8095/Ushuru_APP_API/sendSMS',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $result = json_decode($response);
        curl_close($curl); // Close the connection

        if (empty($result->status)) { //Error
            return false;
        }

        if ($result->status == 0) { //Error
            return true;
        }
    }

    function generateOTPKey($keyLength)
    {
        // Set a blank variable to store the key in
        $key = "";
        for ($x = 1; $x <= $keyLength; $x++) {
            // Set each digit
            $key .= random_int(0, 9);
        }
        return $key;
    }

    public function GenerateOTP($Key)
    {
        $key = str_pad($Key, 6, 0, STR_PAD_LEFT);
        return $key;
    }


    public function SimulateB2C()
    {
        $json = file_get_contents('FailedMpesa.json');
        $callbackData = json_decode($json);
        $service = Yii::$app->params['ServiceName']['B2CTransactions'];


        if ($callbackData->Result->ResultCode != 0) { //Error manenos
            $filter = [
                'ConversationID' => $callbackData->Result->ConversationID,
            ];
            $PaymentRecord = Yii::$app->navhelper->getData($service, $filter);
            if (is_object($PaymentRecord)) { // Trans not Found Tell Saf
            }

            $data = [
                'Failure_Message' => $callbackData->Result->ResultDesc,
                'Error_Code' => $callbackData->Result->ResultCode,
                'Key' => $PaymentRecord[0]->Key,
                'Mpesa_Code' => $callbackData->Result->TransactionID,
                'Completed_At' => time(),
                'Status' => 'Failed',
                'Navision_Error' => '',
            ];

            $result = Yii::$app->navhelper->updateData($service, $data);
        }

        $filter = [
            'ConversationID' => $callbackData->Result->ConversationID,
        ];
        $PaymentRecord = Yii::$app->navhelper->getData($service, $filter);

        if (is_object($PaymentRecord)) { //Nothing was Found

            //Notify  Safaricom

        }

        $data = [
            'Failure_Message' => '',
            'Error_Code' => '',
            'Key' => $PaymentRecord[0]->Key,
            'Mpesa_Code' => $callbackData->Result->ResultParameters->ResultParameter[1]->Value,
            'Completed_At' => $callbackData->Result->ResultParameters->ResultParameter[3]->Value,
            'Requested_At' => time(),
            'Mpesa_Charges' => $callbackData->Result->ResultParameters->ResultParameter[7]->Value,
            'Phone_Registered' =>  $callbackData->Result->ResultParameters->ResultParameter[6]->Value,
            'Payed_To' => $callbackData->Result->ResultParameters->ResultParameter[2]->Value,
            'Working_Balance' => $callbackData->Result->ResultParameters->ResultParameter[5]->Value,
            'Utility_Balance' => $callbackData->Result->ResultParameters->ResultParameter[4]->Value,
            'Status' => '',
            'Navision_Error' => '',
            'Disbursed_Amount' => $callbackData->Result->ResultParameters->ResultParameter[0]->Value,
        ];

        $result = Yii::$app->navhelper->updateData($service, $data);

        if (is_object($result)) { //All was well
            //Tell Saf all is ok
        }
        //Tell them We are unable to save it



    }

    public function LogB2CTransactionOnNav($data)
    {
        $service = Yii::$app->params['ServiceName']['MBranch'];
        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'ApplyLoan');
        return $PostResult;
    }


    public function Sumulatec2b()
    {

        $curl = curl_init();

        $data = json_encode(array(
            "ShortCode" => 600992, //Yii::$app->params['Mpesa']['BusinessShortCode'],
            "CommandID" =>  'CustomerPayBillOnline',
            "Amount" => 10,
            "Msisdn" => 254710467646,
            "BillRefNumber" => 'MSH7684973',
        ));

        curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['Mpesa']['SimulateC2BURL'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Authenicate(),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }


    function simulateC2B()
    {

        $url = Yii::$app->params['Mpesa']['SimulateC2BURL'];;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->Authenicate())); //setting custom header

        $ShortCode = Yii::$app->params['Mpesa']['BusinessShortCode'];
        $CommandID = 'CustomerPayBillOnline';
        $Amount = '570';
        $Msisdn = '254708374149'; // customer number in the format +2547000000 but remove '+'' sign.
        $BillRefNumber = 'invoice001'; //referesnce code e.g customer idno

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => $ShortCode,
            'CommandID' => $CommandID,
            'Amount' => $Amount,
            'Msisdn' => $Msisdn,
            'BillRefNumber' => $BillRefNumber
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        // echo '<pre>';
        // print_r($curl_response);
        // exit;

        return $curl_response;
    }

    // Function to register url

    public function Registerurl()
    {

        $ShortCode = Yii::$app->params['Mpesa']['BusinessShortCode']; //'600457';
        $AccessToken = $this->Authenicate();
        $url = Yii::$app->params['Mpesa']['RegisterURL'];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $AccessToken)); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => $ShortCode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => Url::home(true) . 'mobilemoney/confirmationurl',
            'ValidationURL' => Url::home(true) . 'mobilemoney/validationurl'
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        echo '<pre>';
        print_r($curl_response);
        exit;
    }
}
