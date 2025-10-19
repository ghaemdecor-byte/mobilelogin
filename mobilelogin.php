<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'mobilelogin/vendors/SMSIr/SMSIrClient.php';
require_once _PS_MODULE_DIR_ . 'mobilelogin/vendors/IPPanel/IPPanelClient.php';

class MobileLogin extends Module
{
    public function __construct()
    {
        $this->name = 'mobilelogin';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Mobile Login Pro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Mobile Login Pro');
        $this->description = $this->l('Professional mobile phone login and verification system');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->installSQL() ||
            !$this->installTab() ||
            !$this->registerHooks()
        ) {
            return false;
        }

        $this->setDefaultSettings();
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->uninstallSQL() ||
            !$this->uninstallTab()
        ) {
            return false;
        }

        return true;
    }

    private function installSQL()
    {
        $sql = [];
        include dirname(__FILE__) . '/sql/install.php';
        
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    private function uninstallSQL()
    {
        $sql = [];
        include dirname(__FILE__) . '/sql/uninstall.php';
        
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    private function registerHooks()
    {
        return $this->registerHook([
            'displayCustomerAccount',
            'displayHeader',
            'displayNav2',
            'actionCustomerAccountAdd',
            'actionCustomerAccountUpdate',
            'actionObjectCustomerAddAfter',
            'actionObjectCustomerUpdateAfter',
            'actionAuthentication',
            'displayAdminCustomers',
            'actionAdminCustomersControllerSaveAfter',
            'actionBeforeSubmitAccount',
            'actionValidateCustomerAddressForm'
        ]);
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminMobileLogin';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('SELL');
        $tab->icon = 'phone_iphone';
        
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Mobile Login');
        }
        
        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminMobileLogin');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    private function setDefaultSettings()
    {
        Configuration::updateValue('MOBILELOGIN_ENABLED', 1);
        Configuration::updateValue('MOBILELOGIN_SMS_PROVIDER', 'smsir');
        Configuration::updateValue('MOBILELOGIN_OTP_LENGTH', 6);
        Configuration::updateValue('MOBILELOGIN_OTP_EXPIRY', 300);
        Configuration::updateValue('MOBILELOGIN_REQUIRE_VERIFICATION', 1);
        Configuration::updateValue('MOBILELOGIN_ALLOW_EMAIL_REGISTER', 0);
        Configuration::updateValue('MOBILELOGIN_REDIRECT_CART', 1);
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminMobileLogin'));
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'mobilelogin-css',
            'modules/'.$this->name.'/views/css/mobilelogin.css',
            ['media' => 'all', 'priority' => 150]
        );

        $this->context->controller->registerJavascript(
            'mobilelogin-js',
            'modules/'.$this->name.'/views/js/mobilelogin-front.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function hookDisplayCustomerAccount($params)
    {
        return $this->display(__FILE__, 'views/templates/hooks/customer-account.tpl');
    }

    public function hookActionCustomerAccountAdd($params)
    {
        return $this->processCustomerPhone($params);
    }

    public function hookActionCustomerAccountUpdate($params)
    {
        return $this->processCustomerPhone($params);
    }

    private function processCustomerPhone($params)
    {
        if (!Tools::isSubmit('submitAccount')) {
            return;
        }

        $phone = Tools::getValue('phone');
        if ($phone && Validate::isPhoneNumber($phone)) {
            $customerId = isset($params['newCustomer']->id) ? $params['newCustomer']->id : $this->context->customer->id;
            
            Db::getInstance()->insert('customer_phone', [
                'id_customer' => $customerId,
                'phone' => pSQL($phone),
                'verified' => 0,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s')
            ], true, true, Db::REPLACE);
        }
    }

    public function hookDisplayAdminCustomers($params)
    {
        $id_customer = (int)$params['id_customer'];
        $phones = $this->getCustomerPhones($id_customer);

        $this->context->smarty->assign([
            'customer_phones' => $phones
        ]);

        return $this->display(__FILE__, 'views/templates/admin/customer_phones.tpl');
    }

    private function getCustomerPhones($id_customer)
    {
        return Db::getInstance()->executeS('
            SELECT * FROM '._DB_PREFIX_.'customer_phone 
            WHERE id_customer = '.(int)$id_customer.'
        ');
    }

    public function sendVerificationCode($phone, $customerId = null)
    {
        $code = $this->generateOTP();
        $provider = Configuration::get('MOBILELOGIN_SMS_PROVIDER');
        
        try {
            $result = $this->sendSMS($provider, $phone, $code);
            
            if ($result) {
                $this->saveVerificationCode($phone, $code, $customerId);
                return true;
            }
        } catch (Exception $e) {
            PrestaShopLogger::addLog('MobileLogin SMS Error: ' . $e->getMessage(), 3);
        }
        
        return false;
    }

    private function generateOTP()
    {
        $length = (int)Configuration::get('MOBILELOGIN_OTP_LENGTH');
        $characters = '0123456789';
        $otp = '';
        
        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $otp;
    }

    private function sendSMS($provider, $phone, $code)
    {
        switch ($provider) {
            case 'smsir':
                return $this->sendViaSMSIr($phone, $code);
            case 'ippanel':
                return $this->sendViaIPPanel($phone, $code);
            default:
                return false;
        }
    }

    private function sendViaSMSIr($phone, $code)
    {
        $apiKey = Configuration::get('MOBILELOGIN_SMSIR_API_KEY');
        $secretKey = Configuration::get('MOBILELOGIN_SMSIR_SECRET_KEY');
        $lineNumber = Configuration::get('MOBILELOGIN_SMSIR_LINE_NUMBER');
        $templateId = Configuration::get('MOBILELOGIN_SMSIR_TEMPLATE_ID');

        $client = new SMSIrClient($apiKey, $secretKey, $lineNumber);
        
        $parameters = [
            ['name' => 'CODE', 'value' => $code]
        ];

        return $client->sendVerify($phone, $templateId, $parameters);
    }

    private function sendViaIPPanel($phone, $code)
    {
        $apiKey = Configuration::get('MOBILELOGIN_IPPANEL_API_KEY');
        $patternCode = Configuration::get('MOBILELOGIN_IPPANEL_PATTERN_CODE');

        $client = new IPPanelClient($apiKey);
        
        $parameters = [
            'code' => $code
        ];

        return $client->sendPattern($patternCode, $phone, $parameters);
    }

    private function saveVerificationCode($phone, $code, $customerId = null)
    {
        $expiry = (int)Configuration::get('MOBILELOGIN_OTP_EXPIRY');
        
        return Db::getInstance()->insert('mobilelogin_verification', [
            'phone' => pSQL($phone),
            'code' => pSQL($code),
            'id_customer' => $customerId ? (int)$customerId : null,
            'expires_at' => date('Y-m-d H:i:s', time() + $expiry),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function verifyCode($phone, $code)
    {
        $result = Db::getInstance()->getRow('
            SELECT * FROM '._DB_PREFIX_.'mobilelogin_verification 
            WHERE phone = "'.pSQL($phone).'" 
            AND code = "'.pSQL($code).'"
            AND expires_at > NOW()
            AND verified = 0
        ');

        if ($result) {
            Db::getInstance()->update('mobilelogin_verification', [
                'verified' => 1,
                'verified_at' => date('Y-m-d H:i:s')
            ], 'id = '.(int)$result['id']);

            return $result;
        }

        return false;
    }
}
