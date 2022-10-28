<?php
namespace app\library;
use yii;
use yii\base\Component;
use common\models\Hruser;

class MetroPolIntergration extends Component
{
    public function absoluteUrl(){
        return \yii\helpers\Url::home(true);
    }

    private function calculateHash($payload, $apiTimestamp){
        $string = Yii::$app->params['MetroPol']['PrivateKey']. trim(json_encode($payload)) . Yii::$app->params['MetroPol']['PublicKey'] . $apiTimestamp;
        return hash('sha256', $string);
    }

    public function actionMetroPolCheck($data, $ApplicantData){
        //calculate the timestamp as required e.g 2014 07 08 17 58 39 987843
        //Format: Year, Month, Day, Hour, Minute, Second, Milliseconds
        $now = new \DateTime('UTC');
        $apiTimestamp = $now->format('Y-m-d-H-i-s-u');
        $apiTimestamp = str_replace('-', '', $apiTimestamp);
        $payload = [
            "report_type"     => 2,
            "identity_number" => (string) '880000088', //$ApplicantData->National_ID_No
            "identity_type"   => '001',
            "loan_amount" => (int)$data->Applied_Amount
        ];
        $apiHash = $this->calculateHash($payload, $apiTimestamp);

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['MetroPol']['BaseURL'].'/delinquency/status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER=>false,
        CURLOPT_HTTPHEADER => array(
            "X-METROPOL-REST-API-KEY:" . Yii::$app->params['MetroPol']['PublicKey'],
            "X-METROPOL-REST-API-HASH:" . $apiHash,
            "X-METROPOL-REST-API-TIMESTAMP:" . $apiTimestamp,
            "Content-Type:application/json",
        ),
        ));
        $response = curl_exec($curl);        
        return json_decode($response);
    }

}