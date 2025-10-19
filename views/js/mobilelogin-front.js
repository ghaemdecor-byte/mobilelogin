$(document).ready(function() {
    // شمارشگر معکوس برای ارسال مجدد کد
    function startResendTimer(seconds) {
        var $resendBtn = $('.resend-code');
        var originalText = $resendBtn.text();
        var counter = seconds;
        
        $resendBtn.prop('disabled', true);
        
        var timer = setInterval(function() {
            $resendBtn.text(counter + ' ثانیه تا ارسال مجدد');
            counter--;
            
            if (counter < 0) {
                clearInterval(timer);
                $resendBtn.text(originalText).prop('disabled', false);
            }
        }, 1000);
    }
    
    // ارسال کد تأیید
    $('.send-verification').on('click', function() {
        var phone = $('input[name="phone"]').val();
        
        if (!phone) {
            alert('لطفا شماره موبایل را وارد کنید');
            return;
        }
        
        $.ajax({
            url: mobilelogin_ajax_url,
            type: 'POST',
            data: {
                action: 'sendVerification',
                phone: phone
            },
            success: function(response) {
                if (response.success) {
                    $('.verification-section').show();
                    $('.phone-input-section').hide();
                    startResendTimer(60);
                } else {
                    alert('خطا در ارسال کد تأیید');
                }
            }
        });
    });
    
    // ارسال مجدد کد
    $('.resend-code').on('click', function() {
        var phone = $('input[name="phone"]').val();
        
        $.ajax({
            url: mobilelogin_ajax_url,
            type: 'POST',
            data: {
                action: 'resendVerification',
                phone: phone
            },
            success: function(response) {
                if (response.success) {
                    startResendTimer(60);
                    alert('کد تأیید مجدداً ارسال شد');
                }
            }
        });
    });
    
    // اعتبارسنجی کد ملی
    $.validator.addMethod("nationalcode", function(value, element) {
        if (value === '') return true;
        
        // الگوریتم اعتبارسنجی کد ملی
        if (!/^\d{10}$/.test(value)) return false;
        
        var check = parseInt(value[9]);
        var sum = 0;
        
        for (var i = 0; i < 9; i++) {
            sum += parseInt(value[i]) * (10 - i);
        }
        
        sum %= 11;
        return (sum < 2 && check == sum) || (sum >= 2 && check == 11 - sum);
    }, "کد ملی معتبر نیست");
    
    // اعتبارسنجی فرم
    $('.mobile-login-form').validate({
        rules: {
            phone: {
                required: true,
                minlength: 11,
                maxlength: 11
            },
            national_code: {
                nationalcode: true
            }
        },
        messages: {
            phone: {
                required: "شماره موبایل الزامی است",
                minlength: "شماره موبایل باید 11 رقم باشد",
                maxlength: "شماره موبایل باید 11 رقم باشد"
            }
        }
    });
});
