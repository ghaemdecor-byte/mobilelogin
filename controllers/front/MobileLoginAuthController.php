<?php
class MobileLoginAuthController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        if ($this->context->customer->isLogged()) {
            Tools::redirect('index.php');
        }

        $this->context->smarty->assign([
            'mobilelogin_enabled' => Configuration::get('MOBILELOGIN_ENABLED'),
            'allow_email_register' => Configuration::get('MOBILELOGIN_ALLOW_EMAIL_REGISTER'),
            'redirect_url' => Tools::getValue('redirect', ''),
        ]);

        $this->setTemplate('module:mobilelogin/views/templates/front/auth.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitMobileLogin')) {
            $phone = Tools::getValue('phone');
            $this->processMobileLogin($phone);
        } elseif (Tools::isSubmit('submitVerifyCode')) {
            $phone = Tools::getValue('phone');
            $code = Tools::getValue('code');
            $this->processVerification($phone, $code);
        }
    }

    private function processMobileLogin($phone)
    {
        if (!Validate::isPhoneNumber($phone)) {
            $this->errors[] = $this->module->l('Invalid phone number format');
            return;
        }

        if ($this->module->sendVerificationCode($phone)) {
            $this->context->smarty->assign([
                'verification_sent' => true,
                'phone' => $phone,
                'resend_timeout' => 60
            ]);
        } else {
            $this->errors[] = $this->module->l('Failed to send verification code. Please try again.');
        }
    }

    private function processVerification($phone, $code)
    {
        $verification = $this->module->verifyCode($phone, $code);
        
        if ($verification) {
            $customer = $this->findCustomerByPhone($phone);
            
            if ($customer) {
                $this->loginCustomer($customer);
            } else {
                $this->redirectToRegistration($phone);
            }
        } else {
            $this->errors[] = $this->module->l('Invalid verification code');
        }
    }

    private function findCustomerByPhone($phone)
    {
        $sql = 'SELECT c.* FROM '._DB_PREFIX_.'customer c
                INNER JOIN '._DB_PREFIX_.'customer_phone cp ON c.id_customer = cp.id_customer
                WHERE cp.phone = "'.pSQL($phone).'" AND cp.verified = 1
                AND c.active = 1 AND c.deleted = 0';
        
        return Db::getInstance()->getRow($sql);
    }

    private function loginCustomer($customer)
    {
        $this->context->updateCustomer($customer);
        
        $redirect = Tools::getValue('redirect', 'index.php');
        Tools::redirect($redirect);
    }

    private function redirectToRegistration($phone)
    {
        $registration_url = $this->context->link->getPageLink('registration', true);
        $registration_url .= '?phone=' . urlencode($phone);
        
        Tools::redirect($registration_url);
    }
}
