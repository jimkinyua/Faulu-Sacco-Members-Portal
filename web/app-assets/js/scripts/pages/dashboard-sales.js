/*=========================================================================================
    File Name: dashboard-analytics.js
    Description: intialize advance cards
    ----------------------------------------------------------------------------------------
    Item Name: Modern Admin - Clean Bootstrap 4 Dashboard HTML Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
    ==========================================================================================*/
$(window).on("load", function () {
  let data = [];
  // console.table($('#AccountsTable').find('tbody'))
  $('#AccountsTable > tbody > tr').each(function (index, tr) {
    // let sasaAccount = tr.find('.SchemeBalance');
    let SchemeBalance = parseFloat($(this).find('.SchemeBalance').val());
    let AccountName = $(this).find('.SchemeName').val();
    let dara = {
      label: AccountName,
      value: Math.abs(SchemeBalance)
    };
    data.push(dara)
  });
  // console.log(data)
  let sasaAccount = $('#SasaAccount').val();
  let shareCapitalAccount = $('#ShareCapitalAccount').val();
  let depsitsAccount = $('#DepositsAccount').val();

  let actualSASA = 0;
  let actualShareCapital = 0;
  let actualDeposits = 0;

  if (sasaAccount) {
    actualSASA = parseFloat(sasaAccount)
  }
  if (shareCapitalAccount) {
    actualShareCapital = parseFloat(shareCapitalAccount)
  }
  if (depsitsAccount) {
    actualDeposits = parseFloat(depsitsAccount)
  }

  // -----------------------------------
  Morris.Donut({
    element: "sessions-browser-donut-chart",
    data,
    resize: true,
    colors: ["#e5ef0c", "#8b2323", "#FF7588", "#007bb6", "#FFA87D", "#16D39A", "#00FFFF", "#581845", "#900C3F", "#C70039"]
  });
});
