/*!
  *  v1.0.1 ({REPLACE_ME_URL})
  * Copyright 2013-2023 {REPLACE_ME_AUTHOR}
  * Licensed under GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
  */
(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  /*!
   *  v1.0.1 (https://websweetstudio.com)
   * Copyright 2013-2023 Aditya K
   * Licensed under GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
   */
  (function (factory) {
    typeof define === "function" && define.amd ? define(factory) : factory();
  })(function () {

    jQuery(document).ready(function ($) {
      $(".change-parent").on("change", function () {
        var selectedQuestion = $(this).data("question");
        var selectedValue = $(this).val();
        if (selectedQuestion && selectedValue) {
          // Find the element with the matching data-condition-key and data-condition-val

          // Hide elements that do not match the selected key
          $(".wss-card").filter(function () {
            return parseInt($(this).data("condition-key")) >= parseInt(selectedQuestion);
          }).hide();

          // Uncheck all radio buttons inside hidden elements
          $(".wss-card").filter(function () {
            return parseInt($(this).data("condition-key")) >= parseInt(selectedQuestion);
          }).find('input[type="radio"]').prop("checked", false);
          $('.wss-card[data-condition-val="' + selectedValue + '"]').show();
        }
      });
      $(".questionnaire-frame").on("submit", function (event) {
        // Mencegah pengiriman formulir yang normal
        event.preventDefault();
        var $this = $(this);
        $this.find(".wss-btn-submit span").html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise wss-loading wss-d-iline-block" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/></svg>');

        // Mengumpulkan data dari seluruh formulir
        var formData = {};
        $(".questionnaire-frame").each(function () {
          var formId = $(this).data("id");

          // Menggunakan serializeArray untuk mengambil semua elemen formulir
          var formElements = $(this).serializeArray();

          // Menyusun data ke dalam objek
          formData[formId] = {};
          formData[formId]["soal"] = {};
          formData[formId]["jawaban"] = {};
          $.each(formElements, function (index, element) {
            formData[formId]["jawaban"][element.name] = element.value;
            formData[formId]["soal"][element.name] = $('[name="' + element.name + '"]:first').data("question-text");
          });
          // console.log(formData);
        });

        // Menggunakan fungsi jQuery.post untuk melakukan AJAX request
        $.post(custom_plugin.ajaxurl, {
          action: "update_hasil_questionnaire",
          formData: formData
        }, function (response) {
          // console.log(response);
          $this.find(".wss-btn-submit span").html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-upload wss-d-iline-block" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/><path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/></svg>');
          window.location.href = "?page=for-you";
          // Lakukan tindakan atau manipulasi lainnya setelah respons dari server
        });
      });
    });
  });

}));
//# sourceMappingURL=script.js.map
