{if $mobilelogin_customer_phones}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-phone"></i>
        {l s='Mobile Numbers' mod='mobilelogin'}
    </div>
    <div class="panel-body">
        {foreach $mobilelogin_customer_phones as $phone}
            <div class="phone-item">
                <span class="phone-number">{$phone.phone}</span>
                <span class="phone-status {if $phone.verified}verified{else}not-verified{/if}">
                    {if $phone.verified}
                        <i class="icon-check"></i> {l s='Verified' mod='mobilelogin'}
                    {else}
                        <i class="icon-times"></i> {l s='Not Verified' mod='mobilelogin'}
                        <a href="{$mobilelogin_verification_url}?phone={$phone.phone|urlencode}" class="btn btn-primary btn-xs">
                            {l s='Verify' mod='mobilelogin'}
                        </a>
                    {/if}
                </span>
            </div>
        {/foreach}
    </div>
</div>
{/if}
