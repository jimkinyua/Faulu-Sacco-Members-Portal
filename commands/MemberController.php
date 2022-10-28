<?php

namespace app\commands;
use yii\console\Controller;
use app\models\ApplicantUser;
Use Yii;
use yii\console\ExitCode;
use app\models\user;

class MemberController extends Controller{

    public function actionGetMembersWithNoHash(){
        $service = Yii::$app->params['ServiceName']['Members'];
        $filter = [
            'No'=>'GHS00003'
        ];
        $AllMembers = Yii::$app->navhelper->getData($service);

        if(!is_object($AllMembers)){
            foreach($AllMembers as $AllMember){

                if(isset($AllMember->No)){
                    $user = user::findOne(['No_' => $AllMember->No]);
                    if($user){
                        if($user->password_hash){
                            continue;
                        }
                        $user->setPassword($AllMember->No);
                        $user->SetUpPassword = 1;
                        if($user->update(false)){
                            echo 'Updated No '.  $AllMember->No . "\n";
                        }
                    }
                }
            
            }
        }      

    }

    public function actionGetMembers(){ 
        $service = Yii::$app->params['ServiceName']['MemberApplication'];
        $AllMembers = Yii::$app->navhelper->getData($service);
    
      
        if(!is_object($AllMembers)){
            foreach($AllMembers as $AllMember){
               $this->CreateUserInPortalTable($AllMember);
            }
        }
    }

    public function CreateUserInPortalTable($Member){
        $user = new ApplicantUser();
        $user->generateAuthKey();
        $user->setPassword('#%%$YRFDGGFY$%%&^%$#');
        $user->email = isset($Member->E_Mail_Address)?$Member->E_Mail_Address:'';
        $user->status = 10; //Approved
        $user->generateEmailVerificationToken();
        $user->ApplicationId = $Member->Application_No;
        $user->memebershipType = $Member->Member_Category;
        $user->phoneNo = @$Member->Mobile_Phone_No;
        $user->memberNo = $Member->Member_No;
        $user->hasMemberNo = 1;
        if($user->save()){ 
            echo $Member->Member_No. "\n";
        }
          echo 'Failed To Insert';
    }

    public function AddUserToPortalDb($UnregisteredUser){
   
        $internal_user = new ApplicantUser();
        // $internal_user->username = $this->username;
        // print_r('Hapa');
        //     exit;
        $internal_user->Email = $UnregisteredUser->Email;
        $internal_user->setPassword(Yii::$app->security->generateRandomString(12));
        $internal_user->generateAuthKey();
        $internal_user->generateEmailVerificationToken();
        $internal_user->FirstName = $UnregisteredUser->First_Name;
        $internal_user->LastName = $UnregisteredUser->Last_Name;
        if($UnregisteredUser->Role == 'HR_Authorizer'){
            $internal_user->IsApprover = 1;
        }else{
            $internal_user->IsApprover = 0;
        }
        $internal_user->MidleName = @$UnregisteredUser->Middle_Name;
        $internal_user->OrganizationId = 12;
        if($internal_user->save()){   
            return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $internal_user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($internal_user->Email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();      
             
        }
        return false;
        // return $internal_user->save(); //&& $this->sendEmail($internal_user);

    }

    function IsUserInPortalDb($Email){
        $User = ApplicantUser::find()
        ->where(['Email'=>$Email])
        ->asArray()->one();
        
        if($User){
            return true;
        }

        return false;
    }
    function UsersinPortalDbWithNoMemberNo(){
      return   (new \yii\db\Query())
            ->select(['ApplicationId'])
            ->from('PortalMembers')
            ->where(['memberNo' => '1'])
            ->all();
    }
    public function DoesMemberExist($Member_No){ 
        $service = Yii::$app->params['ServiceName']['Members'];
        $filter = [
            'No' => $Member_No,
        ];

        $ApprovedApplicants = Yii::$app->navhelper->getData($service,$filter);
        // echo'<pre>';
        // print_r($ApprovedApplicants);
        // exit;
        if(!is_object($ApprovedApplicants)){
            return true;
        }
        return false;
    }

    public function actionNotifyMembers(){
        $service = Yii::$app->params['ServiceName']['SMSMessages'];
        $filter = [
            'Sent_To_Server'=>'No'
        ];

        $SmsMessages = Yii::$app->navhelper->getData($service, $filter);
        if(is_array($SmsMessages)){
            foreach($SmsMessages as $Message){
                if(empty($Message->Telephone_No)){ //No Phone No
                    continue;
                }
                $sendResult = $this->SendSMSFromNav($Message->SMS_Message, $Message->Telephone_No);
                if($sendResult === false){
    
                    $navData = [
                        'Sent_To_Server'=>'Failed',
                        'Key'=> $Message->Key,
                    ];
                    Yii::$app->navhelper->updateData($service,$navData);
                    continue; //We'll Retry Again
                }
                $navData = [
                    'Sent_To_Server'=>'Yes',
                    'Key'=> $Message->Key,
                ];
    
                Yii::$app->navhelper->updateData($service,$navData);
                continue; 
            }
        }
       

    }

    public function actionGetApprovedApplicants(){ 
        $service = Yii::$app->params['ServiceName']['AllMembers'];
        $filter = [
            'CreatedonEDMS' => 0,
        ];
        $ApprovedApplicants = Yii::$app->navhelper->getData($service, $filter);
     

        if(!is_object($ApprovedApplicants)){
            foreach($ApprovedApplicants as $ApprovedApplicant){
                //Check if Applicant has a MemberNo
                if(empty($ApprovedApplicant->No) || empty($ApprovedApplicant->Appliccation_No)){
                    continue; //Fake ones these ones
                }
                //Update the Member table with the Member No
                $user = ApplicantUser::findByApplicantWithNoMemberNo(@$ApprovedApplicant->Appliccation_No);                
                
                if($user){
                    //Send All documents To EDMS
                    $CreateApplicantResult = Yii::$app->Mfiles->CreateMember(@$ApprovedApplicant->No, @$ApprovedApplicant->Appliccation_No);
                    if($CreateApplicantResult['Exists']== true || $CreateApplicantResult['Created']== true){
                        continue;
                    }

                    // Begin Transaction
                    $transaction = Yii::$app->db->beginTransaction();

                    try {

                        $user->memberNo = $ApprovedApplicant->No;
                        $user->hasMemberNo = 1;
                        if($user->save(false)){
                            $data = [
                                'CreatedonEDMS'=>1,
                                'Key'=>$ApprovedApplicant->Key,
                            ];
             
                            $Updateresult = Yii::$app->navhelper->updateData($service,$data);
                            if(is_string($Updateresult)){
                                //Log Error and Notify Developer via mail
                                
                                //Try rolling back the Transaction
                                $transaction->rollBack();
                                continue;
                            }else{
                                //Commit Transaction
                                $transaction->commit();
                                $Message = 'Hello '. $ApprovedApplicant->First_Name. ', Your MemberShip Aplication has been Approved. Your Member No is '. $ApprovedApplicant->No;
                                $this->SendSMS($Message ,$user->phoneNo);
                                echo $ApprovedApplicant->No. "\n";
                            }                  
                        }else{
                            //Log Error and Notify Developer via mail
                            echo 'Unable To Save To Database ....'."\n";

                             //Try rolling back the Transaction
                             $transaction->rollBack();
                        }

                    } catch (\Throwable $th) {
                        $transaction->rollBack();
                        continue;
                        //Log Error and Notify Developer via mail
                        //throw $th;
                    }

                    
                   

                }
            }
        }
    }

    public function actionGetApprovedApplications(){ 
        $service = Yii::$app->params['ServiceName']['MemberApplication'];
        $filter = [
            'Approval_Status' => 'Approved',
        ];
        $ApprovedApplicants = Yii::$app->navhelper->getData($service, $filter);
        if(is_array($ApprovedApplicants)){
            // exit('ur');
            foreach($ApprovedApplicants as $ApprovedApplicant){
                if(empty($ApprovedApplicant->Application_No) || empty($ApprovedApplicant->Application_No)){
                    continue; //Fake ones these ones
                }                    
                $user = @ApplicantUser::ApplicantWhoseApplicationHasJustBeenApproved(@$ApprovedApplicant->Application_No);               
                if($user){
                    $user->ApplicationApproved = 2;
                    if($user->save(false)){
                        $Message = 'Hello '. $ApprovedApplicant->First_Name. ', Your MemberShip Aplication has been Approved. Make a payment of KES 1200 to Pay Bill 12345. Put '. $ApprovedApplicant->Application_No . 'as the account No';
                        echo 'Notified  '. $ApprovedApplicant->Application_No. "\n"; 
                    }
                                           
                }
            }
        }
    }

    
    public function actionGetApprovedEmployeeApplicants(){ 
        $service = Yii::$app->params['ServiceName']['MemberApplication'];
        $filter = [
            'Employee' => true,
        ];
        // $this->UsersinPortalDbWithNoMemberNo();
        $ApprovedApplicants = Yii::$app->navhelper->getData($service,$filter);
        //  echo '<pre>';
        //         print_r($ApprovedApplicants);
        //         exit;

        if(!is_object($ApprovedApplicants)){

            foreach($ApprovedApplicants as $ApprovedApplicant){
                //Check if Applicant has a MemberNo

                //Update the Member table with the Member No
                $user = ApplicantUser::findByApplicantWithNoMemberNo($ApprovedApplicant->Application_No);
                // echo '<pre>';
                // print_r($ApprovedApplicant);
                // exit;

                if(!$user){
                    $user = new ApplicantUser();
                    $user->ApplicationId = $ApprovedApplicant->Application_No;
                    $user->hasMemberNo = 0;
                    $user->email = $ApprovedApplicant->E_Mail_Address;
                    $user->email = $ApprovedApplicant->E_Mail_Address;
                    $user->phoneNo = $ApprovedApplicant->Mobile_Phone_No;
                    $user->memebershipType = $ApprovedApplicant->Member_Category;
                    $user->setPassword($ApprovedApplicant->E_Mail_Address);
                    $user->generateAuthKey();
                    $user->generateEmailVerificationToken();
                    if($user->save()){
                        // $Message = 'Hello '. $ApprovedApplicant->First_Name. ', Your MemberShip Apllication has been Approved. Your Member No is '. $ApprovedApplicant->Member_No;
                        // $this->SendSMS($Message ,$user->phoneNo);
                        echo $ApprovedApplicant->Application_No. "\n";
                    }else{
                        echo $ApprovedApplicant->Application_No. "\n";
                        // return ExitCode::UNSPECIFIED_ERROR;
                    }

                }
            }
        }
    }

    public function SendSMS($Message, $PhoneNo){
        //Todo: Clean The Phone Number to Form 07... 0r 2547....
        // exit($PhoneNo);
        $url =Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken =Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender'=> 'MHASIBU',
            'message'=> $Message,
            'phone'=> $PhoneNo,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer  '. $BearerToken));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $result = json_decode($response);
        curl_close($ch); // Close the connection
        if(empty($result->status)){ //Error
            Yii::$app->session->setFlash('error', 'Unable to Send a Message to the ');
            return $this->redirect(Yii::$app->request->referrer);
        }
        return true;

    }

    public function SendSMSFromNav($Message, $PhoneNo){
        //Todo: Clean The Phone Number to Form 07... 0r 2547....
        // exit($PhoneNo);
        $url =Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken =Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender'=> 'MHASIBU',
            'message'=> $Message,
            'phone'=> $PhoneNo,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer  '. $BearerToken));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $result = json_decode($response);
        curl_close($ch); // Close the connection
        if(empty($result->status)){ //Error
            return false;
        }
        return $result;

    }

  

    

}

?>