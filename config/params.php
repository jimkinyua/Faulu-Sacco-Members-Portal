<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bsVersion' => '4.x', // this will set globally `bsVersion` to Bootstrap 4.x for all Krajee Extensions
    'NavisionUsername' => env('NavisionUsername'),
    'NavisionPassword' => env('NavisionPassword'),
    'supportEmail' => env('supportEmail'),

    'powered' => 'Iansoft Technologies Ltd.',
    'generalTitle' => '',

    'server' => env('server'),
    'WebServicePort' => env('WebServicePort'),
    'ServerInstance' => env('ServerInstance'),
    'ServiceCompanyName' => env('CompanyName'),
    'DbCompanyName' => env('DbCompanyName'),
    'ldPrefix' => env('ldPrefix'),
    'adServer' => env('adServer'),
    'GroupCharge' => env('GroupCharge'),
    'IndividualCharge' => env('IndividualCharge'),

    'codeUnits' => [
        'PortalFactory' => 'PortalFactory',
        'PortalIntegrations' => 'PortalIntegrations',
    ],


    'Mpesa' => [
        'ConsumerKey' => env('ConsumerKey'),
        'ConsumerSecret' => env('ConsumerSecret'),
        //SDK Config
        'PhoneNumber' => env('PhoneNumber'),
        'PartyB' => env('PartyB'),
        'PassKey' => env('PassKey'),
        'Company' => env('Company'),
        'BusinessShortCode' => env('BusinessShortCode'),
        'RegisterURL' => env('RegisterURL'),
        'SimulateC2BURL' => env('SimulateC2BURL'),
        'CreateSTKURL' => env('CreateSTKURL'),
        'AuthURL' => env('AuthURL'),
        #B2c Intergrations
        'MpesaApiUserPassword' => env('MpesaApiUserPassword'),
        'MpesaAPIUser' => env('MpesaAPIUser'),
        'PaymentRequestURL' => env('PaymentRequestURL'),
        'PartyA' => env('PartyA'),
    ],

    'SMS' => [
        'AcessToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMjBiNTIxNGRkY2VmYTIzN2EzNjQ2ODBmM2M0NzdhODE2MTM1OTE2NzJiM2NkZDU2ZDNhNDRjYWRlNjg2MzBiMWU0YjMwOWJlNTRlNGM4ZTciLCJpYXQiOjE2Mzc5MTUwMjgsIm5iZiI6MTYzNzkxNTAyOCwiZXhwIjo0NzkzNTg4NjI4LCJzdWIiOiIxMjIiLCJzY29wZXMiOltdfQ.Vdggp54bPTlcX196UBztJgxbzgThfLxvybm9U1Sw4KKru93yLPchp4MAr53CV54tciPDr78XrmWEdhnhlMfEwaH-dsE_hGpvTPYKOuw9qzpc48hmYjkNiUmcJbnMZpD_aFDrOJFYzMOuHoZfQ41R-ljmZ41r9H_hIFQZjifg32QcGqlXcwx8n5UYWcJfYkftm_c0Wu3R_sXbzovYPw61joNrld-m8EPgoq3iiRu186i1FcCrmeMrgnQMXWzJT5spD9v-ySvN_yYmEqhPR_dRxlf1__FVnwlInJnN2b-BPW52AFWpypdX2XgXHs05VYiuOy_UanSJkZVcsv3bfhRq0VdBf0v63aVHHGMHi8dYbCj3qfX1qPO4ejMIhUX0V0III-S2-9LkrBFubPtiYce-LZ6Ji4P2cL1Wbisft_FxxDqpkRax3xxrx_2qL9ZM3p693LNi-iiEDw5sLuA6fvldspnHR4ptCSpo7L01MfZmohOgern2jFveAC7AemCEE0KGNo5Eg2GcFgbOD2k5Kicx9390yTIA0Z3COIVyHyLiWFL7Sqc0KuuZt_IaQ4YFDXGURraX8eK4zKKlwXvv_ubK4aS93EG04Nu5e_WbBbHof0jG0aWLVT3pm4rcdPIfPTI8dPzRwepkRcEkGdgi9_ILwAhkwLlrfhjNfHCWlG-FyHQ',
        'BaseURL' => env('BaseURL'),
    ],



    'MetroPol' => [
        'BaseURL' => 'https://api.metropol.co.ke:5555/v2_1',
        'PrivateKey' => 'owXwkIguSOINADMnKHYVveBGaMTJZIpnGmlQwUMpIWEKAqXMXykUeLceZWqY',
        'PublicKey' => 'YcisxGDqLnTbIbYIthBztkMaLyXqmr',

        'IdentityTypes' => [
            'NationalID' => 001,
            'PassPort' => 002,
            'BusinessRegistrationNo' => 005,
        ]
    ],

    'ServiceName' => [
        'MemberSingle'=>'MemberSingle',//50202
        'NextoKIN'=>'NextoKIN',//50205
        'LoanApplicationList'=>'LoanApplicationList',//50232
        'LoanApplicationCard'=>'LoanApplicationCard',//50229
        'ProductSetupList'=>'ProductSetupList',//50187
        'SasraSectors'=>'SasraSectors',//50494
        'SasraSubSector'=>'SasraSubSector',//50495
        'LoanPurpose'=>'LoanPurpose',//50380
        'AppraisalSalaryDetails'=>'AppraisalSalaryDetails',//50246
        'LoanTopUp'=>'LoanTopUp',//50240
        'LoansList'=>'LoansList',//50228
        'LoanGuarantors'=>'LoanGuarantors',//50237
        'PortalReports'=>'PortalReports',//50053
        'MemberStatistics'=>'MemberStatistics',//50391
        'SavingsStatistics'=>'SavingsStatistics',//50392
        'MemberApplicationSingle'=>'MemberApplicationSingle',//50190
        'MemberApplication_KINs'=>'MemberApplication_KINs',//50193
        'PostedLoans'=>'PostedLoans',//50244
        'MemberApplicationCard'=>'MemberApplicationCard',//50190
        'ApplicationSubscriptions'=>'ApplicationSubscriptions',//
        'SalesPeople'=>'SalesPeople',//14
        'RelationshipTypes'=>'RelationshipTypes',//50194
    ],

    'codeUnits'=>[
        'PortalReports'=>'PortalReports',//50053
    ],
    
    'SystemConfigs' => [
        'UsingNTLM' => env('UsingNTLM'),
        'ChildAccount' => env('ChildAccount'),
        'GroupAccount' => env('GroupAccount'),
        'IndividualAccount' => env('IndividualAccount'),
        'DownloadsFolder' => env('DownloadsFolder')
    ],

    'MimeTypes' => [
        'application/pdf',
    ],

    'Microsoft' => [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'application/vnd.ms-word.document.macroEnabled.12',
        'application/vnd.ms-word.template.macroEnabled.12',
        'application/vnd.ms-excel',
        'application/vnd.ms-excel',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'application/vnd.ms-excel.sheet.macroEnabled.12',
        'application/vnd.ms-excel.template.macroEnabled.12',
        'application/vnd.ms-excel.addin.macroEnabled.12',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'application/vnd.ms-powerpoint',
        'application/vnd.ms-powerpoint',
        'application/vnd.ms-powerpoint',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.template',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'application/vnd.ms-access',
        'application/rtf',
        'application/octet-stream'
    ],

];
