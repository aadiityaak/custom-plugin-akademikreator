jQuery(document).ready(function ($) {
  // Inisialisasi sortable pada daftar
  $(".list-order ul").sortable({
    update: function (event, ui) {
      // Memperbarui nilai 'Urutan Post' setelah drag-drop
      updateUrutanPost($(this));
    },
  });

  // Menangkap perubahan pada checkbox
  $(".list-order input").change(function () {
    // Memperbarui nilai 'Urutan Post' hanya untuk checkbox dalam grup yang sama
    updateUrutanPost($(this).closest(".list-order"));
  });

  // Fungsi untuk memperbarui nilai 'Urutan Post'
  function updateUrutanPost(group) {
    // Mendapatkan nilai yang dipilih dari checkbox dalam grup yang sama
    var selectedValues = [];
    group.find("input:checked").each(function () {
      selectedValues.push($(this).val());
    });

    // Menggabungkan nilai yang dipilih menjadi string yang dipisahkan koma
    var selectedValuesString = selectedValues.join(",");

    // Menemukan elemen 'Urutan Post' di dalam grup yang sama
    var urutanPostField = group
      .closest(".cmb-field-list")
      .find(".order-fyp input");

    // Memperbarui nilai 'Urutan Post'
    urutanPostField.val(selectedValuesString);
  }
});

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
