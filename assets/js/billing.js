$(document).ready(function () {
  $(".viewAllBtn").click(function () {
    let userId = $(this).data("userid");
    $("#addressRow-" + userId).toggleClass("hidden");
    $.get("fetch_addresses.php", { user_id: userId }, function (data) {
      $("#addressList-" + userId).html(data);
    });
  });

  $(document).on("click", ".editAddressBtn", function () {
    $.get(
      "fetch_address.php",
      { address_id: $(this).data("id") },
      function (data) {
        let response = JSON.parse(data);
        $("#edit_address_id").val(response.address_id);
        $("#editBillingModal").modal("show");
      }
    );
  });
});
