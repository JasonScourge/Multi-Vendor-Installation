{if !$runtime.company_id
    && $payout.order_id
    && ($payout.commission != 0 || $payout.commission_amount != 0)
}
    {capture name="order_amount"}
        {include file="common/price.tpl" value=$payout.order_amount}
    {/capture}
    <br>
    <small class="muted">
        {if $payout.commission_type == "A"} {* Backward compatibility *}
            {capture name="commission_amount"}
                {include file="common/price.tpl" value=$payout.commission}
            {/capture}
            {__("addons.vendor_plans.commission_description_absolute", [
                "[amount]" => $smarty.capture.commission_amount,
                "[sum]" => $smarty.capture.order_amount
            ])}
        {else}
            {__("addons.vendor_plans.commission_description_percent", [
                "[amount]" => $payout.commission,
                "[sum]" => $smarty.capture.order_amount
            ])}
        {/if}
    </small>
{/if}