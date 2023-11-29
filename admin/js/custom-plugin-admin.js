jQuery(document).ready(function ($) {
  // Inisialisasi sortable pada daftar
  $(".list-order ul").sortable({
    update: function (event, ui) {
      // Memperbarui nilai 'Urutan Post' setelah drag-drop
      updateUrutanPost($(this));
    },
  });

  $(document).ready(function () {
    $(".export-btn").on("click", function () {
      exportTableToCSV($(".wp-list-table"));
    });

    function exportTableToCSV($table) {
      var csv = [];

      // Add table headers
      var headers = [];
      $table.find("thead th").each(function () {
        headers.push($(this).text());
      });
      csv.push(headers.join(","));

      // Add table rows
      $table.find("tbody tr").each(function () {
        var row = [];
        $(this)
          .find("td")
          .each(function () {
            row.push($(this).text());
          });
        csv.push(row.join(","));
      });

      // Create a Blob object and create a download link
      var blob = new Blob([csv.join("\n")], { type: "text/csv" });
      var url = window.URL.createObjectURL(blob);
      var a = document.createElement("a");
      a.href = url;
      a.download = "custom_fyp.csv";
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    }
  });

  $(document).ready(function () {
    // Fungsi untuk menyembunyikan opsi berdasarkan teks di dalam HTML opsi
    function hideOptionsByValue(select, searchText) {
      // Loop melalui setiap opsi dalam elemen select
      select.find("option").each(function () {
        // Periksa apakah teks di dalam HTML opsi mengandung searchText
        if (
          $(this)
            .html()
            .indexOf("(" + searchText + ")") === -1
        ) {
          // Sembunyikan opsi yang tidak mengandung searchText
          $(this).hide();
        } else {
          // Tampilkan opsi yang mengandung searchText (jika sebelumnya disembunyikan)
          $(this).show();
        }
      });
    }

    // Tangani perubahan pada setiap select di dalam class list-question
    $(".cmb-field-list .list-question select").on("change", function () {
      // Dapatkan nilai yang dipilih
      var selectedValue = $(this).val();

      // Temukan elemen grup yang sesuai
      var group = $(this).closest(".cmb-field-list");

      // Temukan select yang sesuai di dalam class list-answer
      var correspondingSelect = group.find(".list-answer select");

      // Menyembunyikan opsi berdasarkan teks di dalam HTML opsi
      hideOptionsByValue(correspondingSelect, selectedValue);
    });

    // Panggil fungsi untuk menginisialisasi elemen select di dalam class list-answer saat halaman dimuat
    $(".cmb-field-list .list-question select").each(function () {
      // Dapatkan nilai yang dipilih
      var selectedValue = $(this).val();

      // Temukan elemen grup yang sesuai
      var group = $(this).closest(".cmb-field-list");

      // Temukan select yang sesuai di dalam class list-answer
      var correspondingSelect = group.find(".list-answer select");

      // Menyembunyikan opsi berdasarkan teks di dalam HTML opsi
      hideOptionsByValue(correspondingSelect, selectedValue);
    });
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
