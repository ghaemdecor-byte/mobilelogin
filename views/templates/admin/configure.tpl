<div class="panel">
    <div class="panel-heading">
        <i class="icon-phone"></i>
        {l s='Mobile Login Settings' mod='mobilelogin'}
    </div>
    
    <form action="{$module_config_url}" method="post" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='Enable Mobile Login' mod='mobilelogin'}
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="MOBILELOGIN_ENABLED" id="MOBILELOGIN_ENABLED_on" value="1" {if $MOBILELOGIN_ENABLED}checked{/if}>
                    <label for="MOBILELOGIN_ENABLED_on">{l s='Yes' mod='mobilelogin'}</label>
                    <input type="radio" name="MOBILELOGIN_ENABLED" id="MOBILELOGIN_ENABLED_off" value="0" {if !$MOBILELOGIN_ENABLED}checked{/if}>
                    <label for="MOBILELOGIN_ENABLED_off">{l s='No' mod='mobilelogin'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='SMS Provider' mod='mobilelogin'}
            </label>
            <div class="col-lg-9">
                <select name="MOBILELOGIN_SMS_PROVIDER" class="fixed-width-xl">
                    <option value="smsir" {if $MOBILELOGIN_SMS_PROVIDER == 'smsir'}selected{/if}>SMS.ir</option>
                    <option value="ippanel" {if $MOBILELOGIN_SMS_PROVIDER == 'ippanel'}selected{/if}>IPPanel</option>
                </select>
            </div>
        </div>

        <!-- SMS.ir Settings -->
        <div class="sms-provider-settings {if $MOBILELOGIN_SMS_PROVIDER != 'smsir'}hidden{/if}" id="smsir-settings">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='SMS.ir API Key' mod='mobilelogin'}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="MOBILELOGIN_SMSIR_API_KEY" value="{$MOBILELOGIN_SMSIR_API_KEY}" class="fixed-width-xxl">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='SMS.ir Secret Key' mod='mobilelogin'}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="MOBILELOGIN_SMSIR_SECRET_KEY" value="{$MOBILELOGIN_SMSIR_SECRET_KEY}" class="fixed-width-xxl">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Line Number' mod='mobilelogin'}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="MOBILELOGIN_SMSIR_LINE_NUMBER" value="{$MOBILELOGIN_SMSIR_LINE_NUMBER}" class="fixed-width-lg">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Template ID' mod='mobilelogin'}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="MOBILELOGIN_SMSIR_TEMPLATE_ID" value="{$MOBILELOGIN_SMSIR_TEMPLATE_ID}" class="fixed-width-lg">
                </div>
            </div>
        </div>

        <!-- IPPanel Settings -->
        <div class="sms-provider-settings {if $MOBILELOGIN_SMS_PROVIDER != 'ippanel'}hidden{/if}" id="ippanel-settings">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='IPPanel API Key' mod='mobilelogin'}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="MOBILELOGIN_IPPANEL_API_KEY" value="{$MOBILELOGIN_IPPANEL_API_KEY}" class="fixed-width-xxl">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Pattern Code' mod='mobilelogin'}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="MOBILELOGIN_IPPANEL_PATTERN_CODE" value="{$MOBILELOGIN_IPPANEL_PATTERN_CODE}" class="fixed-width-lg">
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" name="savemobilelogin" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='mobilelogin'}
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('select[name="MOBILELOGIN_SMS_PROVIDER"]').change(function() {
        $('.sms-provider-settings').addClass('hidden');
        $('#' + $(this).val() + '-settings').removeClass('hidden');
    });
});
</script>
