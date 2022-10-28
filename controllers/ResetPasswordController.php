<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\ResetPasswordForm;

class ResetPasswordController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout',  'register'],
                'rules' => [

                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                     
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(){
        $this->layout = 'register';
        $model = new ResetPasswordForm();
        $model->applicantId = Yii::$app->session->get('MemberData')->id;

        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {
            
            $resetPasswordRsult = $model->resetPassword();
            if (Yii::$app->user->login($resetPasswordRsult)) {
                Yii::$app->session->setFlash('success', 'Welcome!');
                return $this->goHome();
            }

            return $this->goHome();
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }
}