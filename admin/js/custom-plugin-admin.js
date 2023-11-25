(function ($) {
  $(document).ready(function () {
    // Sembunyikan elemen dengan kelas '.if-condition' saat halaman dimuat
    $(".cmb-field-list .if-condition").hide();

    // Periksa checkbox dalam kelas '.cmb-field-list .parent-condition' saat halaman dimuat
    $(".cmb-field-list .parent-condition input[type='checkbox']").each(
      function () {
        if ($(this).prop("checked")) {
          $(this).closest(".cmb-field-list").find(".if-condition").show();
        }
      }
    );

    // Tangani perubahan checkbox dalam kelas '.cmb-field-list' menggunakan event delegation
    $(".cmb-field-list").on(
      "change",
      '.parent-condition input[type="checkbox"]',
      function () {
        var $parentList = $(this).closest(".cmb-field-list");
        var $ifCondition = $parentList.find(".if-condition");

        if ($(this).prop("checked")) {
          $ifCondition.show();
        } else {
          $ifCondition.hide();
        }
      }
    );
  });
})(jQuery);
