<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


class MobiLoanForm extends Model
{
    public $AppliedAmount;
    public $QualifiedAmount;

    public function rules(){
        return [
            [['AppliedAmount',], 'required'],
            ['AppliedAmount', 'number', 'max' => $this->QualifiedAmount],
            ['AppliedAmount', 'number', 'min' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'AppliedAmount'=>'Amount'
        ];
    }



}

