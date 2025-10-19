{if $mobilelogin_has_unverified_phones}
<div class="mobilelogin-nav-alert">
    <div class="alert alert-warning">
        <i class="icon-warning"></i>
        {l s='You have unverified phone numbers' mod='mobilelogin'}
        <a href="{$mobilelogin_verification_url}" class="alert-link">
            {l s='Verify now' mod='mobilelogin'}
        </a>
    </div>
</div>
{/if}
