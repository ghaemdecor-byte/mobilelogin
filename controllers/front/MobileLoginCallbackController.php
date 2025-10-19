<?php
class MobileLoginCallbackController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        // دریافت داده‌های callback از پنل پیامکی
        $input = json_decode(Tools::file_get_contents('php://input'), true);
        
        if ($input) {
            $this->processCallback($input);
        }
        
        // بازگرداندن پاسخ موفق
        http_response_code(200);
        die('OK');
    }

    private function processCallback($data)
    {
        // لاگ کردن داده‌های دریافتی
        $this->logCallback($data);
        
        // پردازش پیامک دریافتی بر اساس پنل پیامکی
        $provider = Configuration::get('MOBILELOGIN_SMS_PROVIDER');
        
        switch ($provider) {
            case 'smsir':
                $this->processSMSIrCallback($data);
                break;
            case 'ippanel':
                $this->processIPPanelCallback($data);
                break;
        }
    }

    private function processSMSIrCallback($data)
    {
        // پردازش callback مخصوص SMS.ir
        if (isset($data['Messages'])) {
            foreach ($data['Messages'] as $message) {
                $this->handleReceivedSMS(
                    $message['Mobile'],
                    $message['MessageText'],
                    'smsir'
                );
            }
        }
    }

    private function processIPPanelCallback($data)
    {
        // پردازش callback مخصوص IPPanel
        if (isset($data['message'])) {
            $this->handleReceivedSMS(
                $data['from'],
                $data['message'],
                'ippanel'
            );
        }
    }

    private function handleReceivedSMS($phone, $message, $provider)
    {
        // پردازش پیامک دریافتی
        // این بخش می‌تواند برای پاسخ خودکار استفاده شود
        
        $log_data = [
            'phone' => $phone,
            'message' => $message,
            'provider' => $provider,
            'received_at' => date('Y-m-d H:i:s')
        ];
        
        $this->logCallback($log_data);
    }

    private function logCallback($data)
    {
        $log_file = _PS_MODULE_DIR_ . 'mobilelogin/logs/callback.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " - " . json_encode($data) . PHP_EOL;
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
}
