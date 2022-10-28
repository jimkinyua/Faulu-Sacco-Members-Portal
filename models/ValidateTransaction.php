<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


class ValidateTransaction extends Model
{
    public $Code;

    public function rules(){
        return [
            [['Code',], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'Code'=>'Amount'
        ];
    }



}

