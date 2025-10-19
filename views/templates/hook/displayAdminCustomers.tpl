<div class="panel">
    <div class="panel-heading">
        <i class="icon-phone"></i>
        {l s='Customer Mobile Numbers' mod='mobilelogin'}
    </div>
    <div class="table-responsive">
        <table class="table customer-phones-table">
            <thead>
                <tr>
                    <th>{l s='Phone Number' mod='mobilelogin'}</th>
                    <th>{l s='Status' mod='mobilelogin'}</th>
                    <th>{l s='Date Added' mod='mobilelogin'}</th>
                    <th>{l s='Actions' mod='mobilelogin'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $mobilelogin_customer_phones as $phone}
                <tr>
                    <td>{$phone.phone}</td>
                    <td>
                        {if $phone.verified}
                            <span class="label label-success">
                                <i class="icon-check"></i> {l s='Verified' mod='mobilelogin'}
                            </span>
                        {else}
                            <span class="label label-danger">
                                <i class="icon-times"></i> {l s='Not Verified' mod='mobilelogin'}
                            </span>
                        {/if}
                    </td>
                    <td>{$phone.date_add}</td>
                    <td>
                        {if !$phone.verified}
                            <button type="button" class="btn btn-default btn-xs verify-phone" 
                                    data-phone="{$phone.phone}" data-id-customer="{$phone.id_customer}">
                                {l s='Verify' mod='mobilelogin'}
                            </button>
                        {/if}
                        <button type="button" class="btn btn-danger btn-xs delete-phone" 
                                data-id="{$phone.id_customer_phone}">
                            {l s='Delete' mod='mobilelogin'}
                        </button>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="4" class="text-center">
                        {l s='No phone numbers found' mod='mobilelogin'}
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.verify-phone').on('click', function() {
        var phone = $(this).data('phone');
        var idCustomer = $(this).data('id-customer');
        
        if (confirm('{l s='Are you sure you want to verify this phone number?' mod='mobilelogin' js=1}')) {
            $.ajax({
                url: '{$mobilelogin_admin_ajax_url}',
                type: 'POST',
                data: {
                    action: 'verifyPhone',
                    id_customer: idCustomer,
                    phone: phone,
                    ajax: true
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('{l s='Error verifying phone number' mod='mobilelogin' js=1}');
                    }
                }
            });
        }
    });
    
    $('.delete-phone').on('click', function() {
        var id = $(this).data('id');
        
        if (confirm('{l s='Are you sure you want to delete this phone number?' mod='mobilelogin' js=1}')) {
            $.ajax({
                url: '{$mobilelogin_admin_ajax_url}',
                type: 'POST',
                data: {
                    action: 'deletePhone',
                    id_customer_phone: id,
                    ajax: true
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('{l s='Error deleting phone number' mod='mobilelogin' js=1}');
                    }
                }
            });
        }
    });
});
</script>
