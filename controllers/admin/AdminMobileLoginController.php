<?php
class AdminMobileLoginController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'mobilelogin_settings';
        $this->identifier = 'id';
        $this->className = 'Configuration';
        
        parent::__construct();
        
        $this->fields_options = [
            'mobilelogin' => [
                'title' => $this->l('Mobile Login Settings'),
                'fields' => [
                    'MOBILELOGIN_ENABLED' => [
                        'title' => $this->l('Enable Mobile Login'),
                        'type' => 'bool',
                        'validation' => 'isBool'
                    ],
                    'MOBILELOGIN_SMS_PROVIDER' => [
                        'title' => $this->l('SMS Provider'),
                        'type' => 'select',
                        'list' => [
                            ['id' => 'smsir', 'name' => 'SMS.ir'],
                            ['id' => 'ippanel', 'name' => 'IPPanel']
                        ],
                        'identifier' => 'id'
                    ],
                    'MOBILELOGIN_OTP_LENGTH' => [
                        'title' => $this->l('OTP Length'),
                        'type' => 'text',
                        'validation' => 'isUnsignedInt',
                        'class' => 'fixed-width-sm'
                    ],
                    'MOBILELOGIN_OTP_EXPIRY' => [
                        'title' => $this->l('OTP Expiry (seconds)'),
                        'type' => 'text',
                        'validation' => 'isUnsignedInt',
                        'class' => 'fixed-width-sm'
                    ],
                    'MOBILELOGIN_REQUIRE_VERIFICATION' => [
                        'title' => $this->l('Require Phone Verification'),
                        'type' => 'bool',
                        'validation' => 'isBool'
                    ],
                    'MOBILELOGIN_ALLOW_EMAIL_REGISTER' => [
                        'title' => $this->l('Allow Email Registration'),
                        'type' => 'bool',
                        'validation' => 'isBool'
                    ],
                    'MOBILELOGIN_REDIRECT_CART' => [
                        'title' => $this->l('Redirect Cart to Login'),
                        'type' => 'bool',
                        'validation' => 'isBool'
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save')
                ]
            ],
            'smsir_settings' => [
                'title' => $this->l('SMS.ir Settings'),
                'fields' => [
                    'MOBILELOGIN_SMSIR_API_KEY' => [
                        'title' => $this->l('API Key'),
                        'type' => 'text',
                        'size' => 64
                    ],
                    'MOBILELOGIN_SMSIR_SECRET_KEY' => [
                        'title' => $this->l('Secret Key'),
                        'type' => 'text',
                        'size' => 64
                    ],
                    'MOBILELOGIN_SMSIR_LINE_NUMBER' => [
                        'title' => $this->l('Line Number'),
                        'type' => 'text',
                        'class' => 'fixed-width-lg'
                    ],
                    'MOBILELOGIN_SMSIR_TEMPLATE_ID' => [
                        'title' => $this->l('Template ID'),
                        'type' => 'text',
                        'class' => 'fixed-width-lg'
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save')
                ]
            ],
            'ippanel_settings' => [
                'title' => $this->l('IPPanel Settings'),
                'fields' => [
                    'MOBILELOGIN_IPPANEL_API_KEY' => [
                        'title' => $this->l('API Key'),
                        'type' => 'text',
                        'size' => 64
                    ],
                    'MOBILELOGIN_IPPANEL_PATTERN_CODE' => [
                        'title' => $this->l('Pattern Code'),
                        'type' => 'text',
                        'class' => 'fixed-width-lg'
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save')
                ]
            ]
        ];
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['test_sms'] = [
            'href' => self::$currentIndex . '&test_sms&token=' . $this->token,
            'desc' => $this->l('Test SMS'),
            'icon' => 'process-icon-phone'
        ];
    }

    public function postProcess()
    {
        if (Tools::isSubmit('test_sms')) {
            $this->testSMS();
        }
        
        parent::postProcess();
    }

    private function testSMS()
    {
        $test_phone = Tools::getValue('test_phone');
        if ($test_phone && Validate::isPhoneNumber($test_phone)) {
            if ($this->module->sendVerificationCode($test_phone)) {
                $this->confirmations[] = $this->l('Test SMS sent successfully');
            } else {
                $this->errors[] = $this->l('Failed to send test SMS');
            }
        } else {
            $this->errors[] = $this->l('Invalid phone number for test');
        }
    }

    public function renderOptions()
    {
        $this->tpl_option_vars = [
            'module_config_url' => $this->context->link->getAdminLink('AdminMobileLogin'),
            'MOBILELOGIN_ENABLED' => Configuration::get('MOBILELOGIN_ENABLED'),
            'MOBILELOGIN_SMS_PROVIDER' => Configuration::get('MOBILELOGIN_SMS_PROVIDER'),
            'MOBILELOGIN_SMSIR_API_KEY' => Configuration::get('MOBILELOGIN_SMSIR_API_KEY'),
            'MOBILELOGIN_SMSIR_SECRET_KEY' => Configuration::get('MOBILELOGIN_SMSIR_SECRET_KEY'),
            'MOBILELOGIN_SMSIR_LINE_NUMBER' => Configuration::get('MOBILELOGIN_SMSIR_LINE_NUMBER'),
            'MOBILELOGIN_SMSIR_TEMPLATE_ID' => Configuration::get('MOBILELOGIN_SMSIR_TEMPLATE_ID'),
            'MOBILELOGIN_IPPANEL_API_KEY' => Configuration::get('MOBILELOGIN_IPPANEL_API_KEY'),
            'MOBILELOGIN_IPPANEL_PATTERN_CODE' => Configuration::get('MOBILELOGIN_IPPANEL_PATTERN_CODE')
        ];

        return parent::renderOptions();
    }
}
