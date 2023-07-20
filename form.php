<form method="post" name="customerData" action="">
  <table width="40%" height="100" border='1' align="center">
    <tr>
      <td>TID :</td>
      <td>
        <input type="text" name="tid" id="tid" readonly />
        <input type="text" name="merchant_id" class="hidden" value="<?= $ccca_merchant_id ?>" />
      </td>
    </tr>
    <tr>
      <td>Order Id :</td>
      <td><input type="text" name="order_id" value="<?= $order_id ?>" readonly /></td>
    </tr>
    <tr>
      <td>Amount :</td>
      <td><input type="text" name="amount" value="<?= $price ?>" readonly /></td>
    </tr>
    <tr>
      <td>Currency :</td>
      <td><input type="text" name="currency" value="INR" readonly />
        <input type="text" name="redirect_url" class="hidden" value="<?= $ccca_redirect_url ?>" />
        <input type="text" name="cancel_url" class="hidden" value="<?= $ccca_cancel_url ?>" />
      </td>
    </tr>
    <tr>
      <td>Language :</td>
      <td><input type="text" name="language" value="EN" readonly /></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" value="CheckOut"></td>
    </tr>
  </table>
</form>
<script>
  window.onload = function() {
    var d = new Date().getTime();
    document.getElementById("tid").value = d;
  };
</script>