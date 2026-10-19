(function ($) {
  "use strict";

  $(document).ready(function () {

    var overdue_loans_table = $("#overdue_loans_table").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: _url + "/admin/loans/overdue/get_table_data",
        method: "POST",
        data: function (d) {
          d._token = $('meta[name="csrf-token"]').attr("content");
        },
        error: function (xhr, status, error) {
          console.error("Overdue Loans AJAX Error:", status, error);
          console.error("Response:", xhr.responseText);
        },
      },
      columns: [
        { data: "loan_id",             name: "loan_id" },
        { data: "loan_product.name",   name: "loan_product.name",   defaultContent: "" },
        { data: "borrower.first_name", name: "borrower.first_name", defaultContent: "" },
        { data: "borrower.member_no",  name: "borrower.member_no",  defaultContent: "" },
        { data: "release_date",        name: "release_date" },
        { data: "applied_amount",      name: "applied_amount" },
        { data: "overdue_amount",      name: "overdue_amount",      orderable: false, searchable: false },
        { data: "overdue_days",        name: "overdue_days",        orderable: false, searchable: false },
        { data: "status",              name: "status" },
        { data: "action",              name: "action",              orderable: false, searchable: false },
      ],
      responsive: true,
      bStateSave: true,
      bAutoWidth: false,
      ordering: false,
      language: {
        decimal: "",
        emptyTable: typeof $lang_no_data_found !== "undefined" ? $lang_no_data_found : "No data available",
        info:
          (typeof $lang_showing !== "undefined" ? $lang_showing : "Showing") +
          " _START_ " +
          (typeof $lang_to !== "undefined" ? $lang_to : "to") +
          " _END_ " +
          (typeof $lang_of !== "undefined" ? $lang_of : "of") +
          " _TOTAL_ " +
          (typeof $lang_entries !== "undefined" ? $lang_entries : "entries"),
        infoEmpty: typeof $lang_showing_0_to_0_of_0_entries !== "undefined" ? $lang_showing_0_to_0_of_0_entries : "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)",
        infoPostFix: "",
        thousands: ",",
        lengthMenu: (typeof $lang_show !== "undefined" ? $lang_show : "Show") + " _MENU_ " + (typeof $lang_entries !== "undefined" ? $lang_entries : "entries"),
        loadingRecords: typeof $lang_loading !== "undefined" ? $lang_loading : "Loading...",
        processing: typeof $lang_processing !== "undefined" ? $lang_processing : "Processing...",
        search: typeof $lang_search !== "undefined" ? $lang_search : "Search:",
        zeroRecords: typeof $lang_no_matching_records_found !== "undefined" ? $lang_no_matching_records_found : "No matching records found",
        paginate: {
          first: typeof $lang_first !== "undefined" ? $lang_first : "First",
          last: typeof $lang_last !== "undefined" ? $lang_last : "Last",
          previous: "<i class='ti-angle-left'></i>",
          next: "<i class='ti-angle-right'></i>",
        },
      },
      drawCallback: function () {
        $(".dataTables_paginate > .pagination").addClass("pagination-bordered");
      },
    });

    $(document).on("ajax-screen-submit", function () {
      overdue_loans_table.draw();
    });

  }); // end document.ready

})(jQuery);
