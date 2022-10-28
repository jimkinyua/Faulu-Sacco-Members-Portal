<?php
namespace app\library;
use yii;
use yii\base\Component;
use common\models\Hruser;

class Mfiles extends Component
{
    public function absoluteUrl(){
        return \yii\helpers\Url::home(true);
    }


    public function printrr($var){
        print '<pre>';
        print_r($var);
        print '<br>';
        exit('turus!!!');
    }

    public function getApplicantData($ApplicantNo){
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $Applicant = \Yii::$app->navhelper->findOneRecord($service,'Application_No',$ApplicantNo);
        return $Applicant;
    }

    public function getMemberData($ApplicantNo){
        $service = Yii::$app->params['ServiceName']['CustomerCard'];
        $Applicant = \Yii::$app->navhelper->findOneRecord($service,'Application_No',$ApplicantNo);
        return $Applicant;
    }

    public function CreateLoan($model){
        // echo '<pre>';
        // print_r($model);
        // exit;
        //Get Applicant Info
        // $ApplicantData = $this->getApplicantData($ApplicantNo);
        // if(is_object($ApplicantData)){
        //     //Error Manenos
        // }

        $data = array(
            'loanNumber'=> $model->Application_No,
            'memberNumber'=> $model->Member_No,
            "loanType"=>$model->Product_Description,
            "amount"=>(int)$model->Applied_Amount,
            "loanPeriod" =>$model->Repayment_Period_M,
            "loanRepaymentMode"=>$model->Recovery_Mode,
            "modeOfDisbursement"=> 'NA',
            "applicationDate" =>'2022-01-08T12:26:58.693Z',
            "phoneName"=>'NA',
            "phoneNumber"=>Yii::$app->user->identity->{'Phone No_'},
            "accountName"=>'Loan Application',
            "accountNumber"=>'NA',
            "bank"=>'NA',
            "branch"=>'NA'
        ); 

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Loan',  
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        $decodedRes = json_decode($response);
    //    echo '<pre>';
    //     print_r();
    //     exit;
        //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
        if (strpos($response, 'already exists') !== false) {
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$model->Application_No
            ];
        }
        return [
            'Exists'=> false,
            'Created'=>true,
            'ApplicantNo'=>$model->Application_No
        ];
        // Then, after your curl_exec call:
        // $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // $header = substr($response, 0, $header_size);
        // $body = substr($response, $header_size);
        // echo '<pre>';
        // print_r($body);
        // exit;
        // curl_close($curl);
        // echo $response;


  
      
        return true;
    }

    public function CreateApplicant($ApplicantModel){
        $data = array(
            'ApplicantId'=> $ApplicantModel->Application_No,
            'MembershipType'=> $ApplicantModel->Member_Category,
            'Individual_Group_Corporate_Name'=>@$ApplicantModel->First_Name . ' '. @$ApplicantModel->Last_Name,
            'Id_Passport_No'=> isset($ApplicantModel->National_ID_No)?$ApplicantModel->National_ID_No:'N\A',
            'DOB_Incorporation_Date'=>date('Y-m-d'), //
            'PhoneNumber'=> '07123456789',
            'EmailAddress'=> $ApplicantModel->E_Mail_Address,
            'PostalAddress'=>$ApplicantModel->Address,
            'Town'=> $ApplicantModel->Address,
            'KRAPin'=>isset($ApplicantModel->KRA_PIN_No)?$ApplicantModel->KRA_PIN_No:'N\A',
            'Constituency'=>isset($ApplicantModel->Constituency_Name)?$ApplicantModel->Constituency_Name:'N\A'
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Applicant/create?',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        // echo '<pre>';
        // print_r($response);
        // exit;

        $DecodedResponse = json_decode($response);
        if(is_object($DecodedResponse)){
            
            if($DecodedResponse->status == 'Success'){
                return [
                    'Exists'=> false,
                    'Created'=>true,
                    'ApplicantNo'=>$ApplicantModel->Application_No
                ];
            }
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$ApplicantModel->Application_No
            ];

        }// Unusual Areas... 
        return [
            'Exists'=> false,
            'Created'=>false,
            'ApplicantNo'=>$ApplicantModel->Application_No
        ];

    }

    public function UpdateApplicant($ApplicantModel){
        $data = array(
            'ApplicantId'=> $ApplicantModel->Application_No,
            'MembershipType'=> $ApplicantModel->Member_Category,
            'Individual_Group_Corporate_Name'=>@$ApplicantModel->First_Name . ' '. @$ApplicantModel->Last_Name,
            'Id_Passport_No'=> isset($ApplicantModel->National_ID_No)?$ApplicantModel->National_ID_No:'N\A',
            'DOB_Incorporation_Date'=>'2021-12-01', //
            'PhoneNumber'=>'0712345678',
            'EmailAddress'=> $ApplicantModel->E_Mail_Address,
            'PostalAddress'=>$ApplicantModel->Address,
            'Town'=> $ApplicantModel->Address,
            'KRAPin'=>isset($ApplicantModel->KRA_PIN_No)?$ApplicantModel->KRA_PIN_No:'N\A',
            'Constituency'=>isset($ApplicantModel->Constituency_Name)?$ApplicantModel->Constituency_Name:'N\A'
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Applicant/bio-data/update?',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        // echo '<pre>';
        // print_r($response);
        // exit;

        $DecodedResponse = json_decode($response);
        if(is_object($DecodedResponse)){
            
            if($DecodedResponse->status == 'Success'){
                return [
                    'Exists'=> false,
                    'Created'=>true,
                    'ApplicantNo'=>$ApplicantModel->Application_No
                ];
            }
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$ApplicantModel->Application_No
            ];

        }// Unusual Areas... 
        return [
            'Exists'=> false,
            'Created'=>false,
            'ApplicantNo'=>$ApplicantModel->Application_No
        ];

    }

    public function CreateMember($MemberNo, $ApplicanNo){
        //Get Applicant Info
        // $MemberData = $this->getMemberData($MemberNo);
        // if(is_object($MemberData)){
        //     //Error Manenos
        // }
       

        $data = array(
            'applicantId'=> $ApplicanNo,
            'memberId'=> $MemberNo,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Applicant/add-member-number',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        if(is_object($response)){
            
            if($response->status == 'Success'){
                return [
                    'Exists'=> false,
                    'Created'=>true,
                    'ApplicantNo'=>$ApplicanNo
                ];
            }
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$ApplicanNo
            ];

        }// Unusual Areas... 
        return [
            'Exists'=> false,
            'Created'=>false,
            'ApplicantNo'=>$ApplicanNo
        ];
      
        return true;
    }

    public function CreateChangeRequestonEDMS($Model){

        $data = array(
            'memberId'=> $Model->Member_No,
            'changeRequestID'=> $Model->Document_No,
            'changeRequestType'=> $Model->Type_of_Change,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/MemberActions/action/biodata-change-request/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        // echo '<pre>';
        // print_r($response);
        // exit;
        curl_close($curl);
         //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
         if (strpos($response, 'already exists') !== false) {
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$Model->Document_No,            ];
        }
        return [
            'Exists'=> false,
            'Created'=>true,
            'ApplicantNo'=>$Model->Document_No,
        ];
    }


    public function UploadChangeRequestDocument($NomineeAttachementModel){
        // echo '<pre>';
        // print_r($NomineeAttachementModel);
        // exit;

        $data = array(
            'ChangeRequestId ' => $NomineeAttachementModel->DocNum,
            'formFile'=> new \CURLFILE($NomineeAttachementModel->FilePath, '', $NomineeAttachementModel->FileName .'.pdf')
        );

            

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/MemberActions/action/biodata-change-request/'.$NomineeAttachementModel->DocNum.'/document/upload',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        // echo '<pre>';
        // print_r($response);
        // exit;

        $DecodedResponse = json_decode($response);
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileUrl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileUrl
            ];
        }
      

    }

    public function CreateWithDrawalRequest($KinModel){
            // echo '<pre>';
            // print_r($KinModel);
            // exit;

            $curl = curl_init();
            $data = array(
                'member_Id'=> $KinModel->Member_No,
                'exit_Request_Id'=> $KinModel->Document_No,
            );

            curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/MemberActions/action/exit-request/create?',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);            
            $DecodedResponse = json_decode($response);
            // echo '<pre>';
            // print_r($DecodedResponse);
            // exit;
            

            if(is_object($DecodedResponse)){
                if($DecodedResponse->status == 'Success'){
                    return [
                        'Exists'=> false,
                        'Created'=>true,
                        'ApplicantNo'=>$KinModel->Document_No   
                    ];
                }
                return [
                    'Exists'=> false,
                    'Created'=>false,
                    'ApplicantNo'=>$KinModel->Document_No   
                ];
            }

            return [
                'Exists'=> false,
                'Created'=>false,
                'ApplicantNo'=>$KinModel->App_No
            ];
     
    }


    public function CreateKin($KinModel){
            $curl = curl_init();
            $data = array(
                'applicant_Id'=> $KinModel->App_No,
                'nextofkin_Id'=> $KinModel->ID_No,
                'nextofkin_Fullname'=> $KinModel->First_Name. ' ' . @$KinModel->Middle_Name . ' ' . $KinModel->Last_Name ,
                'nextofkin_IdNumber_Passport'=> $KinModel->ID_No,
                'nextofkin_PhoneNumber'=> $KinModel->Phone_No,
                'nextofkin_EmailAddress'=> 'noemail@me.com'
            );

            // echo '<pre>';
            // print_r($data);
            // exit;

            curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/NextOfKin/create?',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);
       
        //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
        if (strpos($response, 'already exists') !== false) {
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$KinModel->App_No            ];
        }
        return [
            'Exists'=> false,
            'Created'=>true,
            'ApplicantNo'=>$KinModel->App_No
        ];
     
    }

    public function CreateSignatory($KinModel){
      

            $curl = curl_init();
            $data = array(
                'applicantID'=> $KinModel->Application_No,
                'signatoryID'=> $KinModel->ID_No,
                'fullName'=> $KinModel->First_Name. ' ' . @$KinModel->Middle_Name . ' ' . $KinModel->Last_Name ,
                'gender'=> $KinModel->Gender,
                'id_Passport'=> $KinModel->ID_No,
                'phoneNumber'=>$KinModel->PhoneNo,
                'emailAddress'=> $KinModel->Email,
                'krapin'=> $KinModel->KRA_Pin,
                'birthDate'=> $KinModel->Date_of_Birth 
            );

            	

          

            curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Signatory/create?',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);
            //   echo '<pre>';
            // print_r($response);
            // exit;
       
        //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
        if (strpos($response, 'already exists') !== false) {
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$KinModel->Application_No            ];
        }
        return [
            'Exists'=> false,
            'Created'=>true,
            'ApplicantNo'=>$KinModel->Application_No
        ];
     
    }

    

      public function UpdateSignatory($KinModel){
    
      
            $curl = curl_init();
            $data = array(
                'applicantID'=> $KinModel->Application_No,
                'signatoryID'=> $KinModel->ID_No,
                'fullName'=> $KinModel->First_Name. ' ' . @$KinModel->Middle_Name . ' ' . $KinModel->Last_Name ,
                'gender'=> $KinModel->Gender,
                'id_Passport'=> $KinModel->ID_No,
                'phoneNumber'=>$KinModel->PhoneNo,
                'emailAddress'=> $KinModel->Email,
                'krapin'=> $KinModel->KRA_Pin,
                'birthDate'=> $KinModel->Date_of_Birth 
            );

            curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Signatory/bio-data/update?',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

           $m = curl_exec($curl);
           $response = json_decode($m);
            //   echo '<pre>';
            // print_r($response);
            // exit;

            if(empty($response)){
                return [
                    'Exists'=> false,
                    'Created'=>false,
                    'ApplicantNo'=>$KinModel->Application_No
                ];
            }

        //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
        if (strpos($response->message, 'already exists') !== false) {
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$KinModel->Application_No            ];
        }

       

        return [
            'Exists'=> false,
            'Created'=>true,
            'ApplicantNo'=>$KinModel->Application_No
        ];
     
    }

    public function CreateNominee($NomineeModel){
   

        $data = array(
            "applicant_Id"=>$NomineeModel->Application_No,
            "nominee_ID"=>$NomineeModel->National_ID_No,
            "nominee_Fullname"=>$NomineeModel->FullName,
            "nominee_IdNumber_Passport"=>$NomineeModel->National_ID_No,
            "nominee_BirthCertificateNumber"=>$NomineeModel->National_ID_No,
            "nominee_AllocationAmount"=>$NomineeModel->Percent_Allocation,
            "nominee_EmailAddress"=>$NomineeModel->Email,
            "nominee_PhoneNumber"=>$NomineeModel->Phone_No,
            "nominee_Relationship"=>$NomineeModel->Relationship
        );

        // echo '<pre>';
        // print_r($data);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Nominee/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        if(is_object($response)){
            
            if($response->status == 'Success'){
                return [
                    'Exists'=> false,
                    'Created'=>true,
                    'ApplicantNo'=>$NomineeModel->Application_No
                ];
            }
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$NomineeModel->Application_No
            ];

        }// Unusual Areas... 
        return [
            'Exists'=> false,
            'Created'=>false,
            'ApplicantNo'=>$NomineeModel->Application_No
        ];


    }

    public function UpdateNominee($NomineeModel){

        $data = array(
            "phoneNumber"=>$NomineeModel->Phone_No,
            "emailAddress"=>$NomineeModel->Email,
            "allocationAmount"=>$NomineeModel->Percent_Allocation,
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Nominee/'.$NomineeModel->National_ID_No.'/bio-data/update',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
             //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
             if (strpos($response, 'already exists') !== false) {
                return [
                    'Exists'=> true,
                    'Created'=>false,
                    'ApplicantNo'=>$NomineeModel->Application_No            ];
            }
            return [
                'Exists'=> false,
                'Created'=>true,
                'ApplicantNo'=>$NomineeModel->Application_No
            ];

    }

    public function UpdateKin($KinModel){
            $curl = curl_init();
            $data = array(
                'phoneNumber'=> $KinModel->Phone_No,
                'emailAddress'=> 'noemail@me.com',
            );

            // echo '<pre>';
            // print_r($KinModel);
            // exit;

            curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/NextOfKin/'.$KinModel->ID_No.'/bio-data/update',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            echo '<pre>';
            print_r(json_encode($response));   
            exit;    
        //Check if thre is a String by name of Failed. If you Find it, It Did not Create the User
        if (strpos($response, 'already exists') !== false) {
            return [
                'Exists'=> true,
                'Created'=>false,
                'ApplicantNo'=>$KinModel->App_No            ];
        }
        return [
            'Exists'=> false,
            'Created'=>true,
            'ApplicantNo'=>$KinModel->App_No
        ];
     
    }

    public function UploadNomineeDocumentToEDMS($NomineeAttachementModel){
         

        $data = array(
            'NextofkinId' => $NomineeAttachementModel->IdentificationNo,
            'formFile'=> new \CURLFILE($NomineeAttachementModel->FilePath, '', $NomineeAttachementModel->FileName .'.pdf')
        );

            // echo '<pre>';
            //    print_r($data);
            //    exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Nominee/'.$NomineeAttachementModel->IdentificationNo.'/documents/upload',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileUrl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileUrl
            ];
        }
      

    }
    

    public function UploadSignatoryDocumentToEDMS($NomineeAttachementModel){
         
       

        $data = array(
            'SignatoryId' => $NomineeAttachementModel->IdentificationNo,
            'formFile'=> new \CURLFILE($NomineeAttachementModel->FilePath, '',  $NomineeAttachementModel->FileName.'.pdf')
        );

            

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Signatory/'.$NomineeAttachementModel->IdentificationNo.'/documents/upload',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);

        // echo '<pre>';
        // print_r($DecodedResponse );
        // exit;

        if (is_object($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse->message
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileurl)){//Sucesss
            // exit('ndani');
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileurl
            ];
        }
      

    }

    public function UpdateSignatoryDocumentToEDMS($NomineeAttachementModel){
         
       

        $data = array(
            'SignatoryId' => $NomineeAttachementModel->IdentificationNo,
            'formFile'=> new \CURLFILE($NomineeAttachementModel->FilePath, '', $NomineeAttachementModel->FileName.'.pdf')
        );

            

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Signatory/'.$NomineeAttachementModel->IdentificationNo.'/documents/upload',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);

        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;

        if (is_object($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse->message
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileurl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileurl
            ];
        }
      

    }
    public function UpdateNomineeDocumentOnEDMS($NomineeAttachementModel){
         

        $data = array(
            'NextofkinId' => $NomineeAttachementModel->IdentificationNo,
            'formFile'=> new \CURLFILE($NomineeAttachementModel->FilePath, '', $NomineeAttachementModel->FileName .'.pdf')
        );

            // echo '<pre>';
            //    print_r($data);
            //    exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Nominee/'.$NomineeAttachementModel->IdentificationNo.'/documents/update',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileUrl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileUrl
            ];
        }
      

    }


    public function UploadKinDocumentToEDMS($KinAttachementModel){
         

        $data = array(
            'NextofkinId' => $KinAttachementModel->IdentificationNo,
            'formFile'=> new \CURLFILE($KinAttachementModel->FilePath, '', $KinAttachementModel->FileName .'.pdf')
        );

            // echo '<pre>';
            //    print_r($data);
            //    exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/NextOfKin/'.$KinAttachementModel->IdentificationNo.'/documents/upload',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileUrl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileUrl
            ];
        }
      

    }

    public function UpdateKinInfomation($KinAttachementModel){
         
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/NextOfKin/'.$KinAttachementModel->IdentificationNo.'/bio-data/update/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('NextofkinId' => $KinAttachementModel->IdentificationNo,
        'formFile'=> new \CURLFILE($KinAttachementModel->FilePath, $KinAttachementModel->docFile->type, $KinAttachementModel->FileName.'pdf')),
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);
            
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileUrl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileUrl
            ];
        }
      

    }

    public function UploadApplicantDocument($ApplicantData){
        // echo '<pre>';
        // print_r($ApplicantData);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Applicant/'.$ApplicantData->Docnum.'/documents/upload?',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('formFile'=> new \CURLFILE($ApplicantData->File_path,'',$ApplicantData->File_Name.'.pdf')),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
             
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UploadDirectDebitDocument($DirectDebitModel){
    
        $DocumentName = str_replace(' ', '', $DirectDebitModel->FileName). 'For'.$DirectDebitModel->Account_No;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/DirectDebit/'.$DirectDebitModel->Loan_No.'/'. $DocumentName .'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($DirectDebitModel->FilePath,'',$DirectDebitModel->FileName.'.pdf'),
              'DocumentName '=>$DocumentName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UpdateDirectDebitDocument($DirectDebitModel, $ExistingModel){

        
    
        $DocumentName = str_replace(' ', '', $ExistingModel->DocumentName). 'For'.$ExistingModel->Account_No;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/DirectDebit/'.$DirectDebitModel->Loan_No.'/'. $DocumentName .'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($DirectDebitModel->FilePath,'',$DirectDebitModel->FileName.'.pdf'),
              'DocumentType'=>$DocumentName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);

        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UploadSecurityDocumentToEDMS($SecurityModel){  
        $uploadTime = time();
        // echo '<pre>';
        // print_r(Yii::$app->params['EDMS']['BaseURL'].'/LoanSecurity/'.$SecurityModel->Application_No.'/'.$SecurityModel->FileName.'/documents/upload');
        // exit;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/LoanSecurity/'.$SecurityModel->Application_No.'/'. str_replace(' ', '', $SecurityModel->FileName.'#'.$uploadTime) .'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentName'=>$SecurityModel->FileName.'#'.$uploadTime,
              'LoanNumber'=>$SecurityModel->Application_No
            ),
        ));
    
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;

        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UploadPayslipDocumentToEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Payslip/'.$SecurityModel->DocNum.'/'.str_replace(' ', '',$SecurityModel->FileName).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentName '=>$SecurityModel->FileName
          ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UpdatePayslipOnDocumentOnEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/Payslip/'.$SecurityModel->DocNum.'/'.str_replace(' ', '',$SecurityModel->FileName).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentType  '=>str_replace(' ', '',$SecurityModel->FileName)
          ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }


    

    public function UploadExternalRecoveryDocumentToEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/ExternalRecoveries/'.$SecurityModel->Application_No.'/'.str_replace(' ', '', $SecurityModel->FileName).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentName'=>$SecurityModel->FileName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }


    public function UploadMemberExitDocumentToEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/MemberActions/action/exit-request/'.urlencode($SecurityModel->DocNum).'/document/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'ExitRequestId '=>$SecurityModel->DocNum
            ),
        ));
        
        $response = curl_exec($curl);

        curl_close($curl);
        $DecodedResponse = json_decode($response);
       
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }


    
    public function UploadBusinessDocumentToEDMS($SecurityModel){
        
        $RemoveSpace = str_replace(' ', '', $SecurityModel->FileName);
        $removeBackSlash = str_replace('/', '', $RemoveSpace);
        $DocumentName = $removeBackSlash;

       

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/BusinessDocuments/'.$SecurityModel->DocNum.'/'.$DocumentName.'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentName'=>$DocumentName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }


    public function UpdateBusinessDocumentToEDMS($SecurityModel, $AlreadyExists){
        
        
        $RemoveSpace = str_replace(' ', '', $SecurityModel->FileName);
        $removeBackSlash = str_replace('/', '', $RemoveSpace);
        $DocumentName = $removeBackSlash;
        // echo '<pre>';
        // print_r($DocumentName);
        // exit;

       

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/BusinessDocuments/'.$SecurityModel->DocNum.'/'.$DocumentName.'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentType '=>$DocumentName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    
    public function UpdateExternalRecoveryDocumentToEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/ExternalRecoveries/'.$SecurityModel->Application_No.'/'.str_replace(' ', '', $SecurityModel->FileName).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentType '=>$SecurityModel->FileName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UploadCashflowDocumentToEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/CashFlows/'.$SecurityModel->DocNum.'/'.str_replace(' ', '', $SecurityModel->FileName).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf')),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UpdateCashflowDocumentToEDMS($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/CashFlows/'.$SecurityModel->DocNum.'/'.str_replace(' ', '', $SecurityModel->FileName).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->FilePath,'',$SecurityModel->FileName.'.pdf'),
              'DocumentType'=>$SecurityModel->FileName
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }
    
    public function UploadCRBDocument($SecurityModel){
        // echo '<pre>';
        // print_r($SecurityModel);
        // exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/CRB/'.$SecurityModel->LoanNo.'/'.str_replace(' ', '',$SecurityModel->File_Name).'/documents/upload',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'formFile'=> new \CURLFILE($SecurityModel->LocalPath,'',$SecurityModel->File_Name.'.pdf'),
              'DocumentName '=>$SecurityModel->File_Name,
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileurl
        ];
        
    }

    public function UpdateLoanDocumentOnEDMS($NomineeAttachementModel){
         
            //     echo '<pre>';
            //    print_r(Yii::$app->params['EDMS']['BaseURL'].'/loandocument/'.urlencode($NomineeAttachementModel->Loan_No). '/'.urlencode($NomineeAttachementModel->DocumentName).'/documents/upload');
            //    exit;
        $data = array(
            // 'LoanNumber ' => $NomineeAttachementModel->Loan_No,
            // 'DocumentType '=> $NomineeAttachementModel->FileName,
            'formFile'=> new \CURLFILE($NomineeAttachementModel->FilePath, '', $NomineeAttachementModel->DocumentName )
        );

            
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL'].'/loandocument/'.urlencode($NomineeAttachementModel->Loan_No). '/'.urlencode($NomineeAttachementModel->DocumentName).'/documents/upload',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $DecodedResponse = json_decode($response);

        // echo '<pre>';
        //        print_r($response);
        //        exit;

        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>$DecodedResponse
            ];
        }

        if(is_string($DecodedResponse)){
            if (strpos($DecodedResponse, 'does not exist') !== false) {
                return [
                    'Uploaded'=> false,
                    'Message'=>$DecodedResponse
                ];
            }
        }

        if(isset($DecodedResponse[0]->fileurl)){//Sucesss
            return [
                'Uploaded'=> true,
                'Message'=>'Saved!',
                'DocumentUrl'=>$DecodedResponse[0]->fileurl
            ];
        }
      

    }

    public function UpdateApplicantDocument($ApplicantData){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => Yii::$app->params['EDMS']['BaseURL']. '/Applicant/'.$ApplicantData->Docnum.'/documents/update?',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => array('formFile'=> new \CURLFILE($ApplicantData->File_path,'',$ApplicantData->File_Name.'pdf')),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $DecodedResponse = json_decode($response);
        // echo '<pre>';
        // print_r($DecodedResponse);
        // exit;
        if (empty($DecodedResponse)) { //Error Occured. 
            return [
                'Uploaded'=> false,
                'Message'=>'We are Unble to Save Your Document. Kindly Try Again.'
            ];
        }
        return [
            'Uploaded'=> true,
            'Message'=>'Saved!',
            'DocumentUrl'=>$DecodedResponse[0]->fileUrl
        ];
        
    }
    
   


}