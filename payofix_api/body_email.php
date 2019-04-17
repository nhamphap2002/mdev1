<?php ?>
<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="order-details">
            <h3>Invoice <span class="no-link">#<?php echo $data['invoice_number'] ?></span></h3>
            <p>Order <span class="no-link">#<?php echo $data['order_number'] ?></span></p>
        </td>
    </tr>
    <tr>
        <td class="order-details">
            <h3>Customer email: <span class="no-link"><?php echo $data['customer_email'] ?></span></h3>
        </td>
    </tr>
</table>