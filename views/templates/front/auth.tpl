<div class="mobile-login-container">
    <h2 class="page-heading">{l s='Login with Mobile' mod='mobilelogin'}</h2>
    
    {if $errors}
        <div class="alert alert-danger">
            {foreach $errors as $error}
                <p>{$error}</p>
            {/foreach}
        </div>
    {/if}

    <form method="post" class="mobile-login-form">
        <div class="phone-input-section {if $verification_sent}hidden{/if}">
            <div class="form-group">
                <label for="phone">{l s='Mobile Number' mod='mobilelogin'}</label>
                <input type="tel" name="phone" id="phone" class="form-control" 
                       placeholder="09xxxxxxxxx" required 
                       pattern="09[0-9]{9}" maxlength="11">
                <small class="form-text text-muted">
                    {l s='Enter your 11-digit mobile number' mod='mobilelogin'}
                </small>
            </div>
            
            <button type="submit" name="submitMobileLogin" class="btn btn-primary btn-block send-verification">
                {l s='Send Verification Code' mod='mobilelogin'}
            </button>
            
            {if $allow_email_register}
                <div class="text-center mt-3">
                    <a href="{$link->getPageLink('authentication')}">
                        {l s='Login with Email' mod='mobilelogin'}
                    </a>
                </div>
            {/if}
        </div>

        <div class="verification-section {if $verification_sent}active{/if}">
            <div class="alert alert-info">
                {l s='Verification code sent to' mod='mobilelogin'} <strong>{$phone}</strong>
            </div>
            
            <div class="form-group">
                <label for="code">{l s='Verification Code' mod='mobilelogin'}</label>
                <div class="verification-inputs">
                    <input type="text" name="code" id="code" class="form-control" 
                           placeholder="123456" required maxlength="6"
                           pattern="[0-9]{6}">
                </div>
            </div>
            
            <button type="submit" name="submitVerifyCode" class="btn btn-success btn-block">
                {l s='Verify and Login' mod='mobilelogin'}
            </button>
            
            <button type="button" class="btn btn-link btn-block resend-code">
                {l s='Resend Code' mod='mobilelogin'}
            </button>
            
            <button type="button" class="btn btn-secondary btn-block change-phone">
                {l s='Change Phone Number' mod='mobilelogin'}
            </button>
        </div>
        
        <input type="hidden" name="redirect" value="{$redirect_url}">
    </form>
</div>

<script>
$(document).ready(function() {
    // تغییر شماره موبایل
    $('.change-phone').on('click', function() {
        $('.verification-section').removeClass('active').hide();
        $('.phone-input-section').show();
    });
    
    // ارسال کد تأیید
    $('.send-verification').on('click', function(e) {
        var phone = $('#phone').val();
        if (!/^09[0-9]{9}$/.test(phone)) {
            e.preventDefault();
            alert('{l s='Please enter a valid mobile number' mod='mobilelogin' js=1}');
            return false;
        }
    });
});
</script>
