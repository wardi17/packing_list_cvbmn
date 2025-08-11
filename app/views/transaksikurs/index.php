<?php

$userlog = (isset($_SESSION['login_user'])) ?  $_SESSION['login_user'] : '';
?>

<style>
  #thead {
    background-color: #E7CEA6 !important;
    /* font-size: 8px;
        font-weight: 100 !important; */
    /*color :#000000 !important;*/
  }

  .table-hover tbody tr:hover td,
  .table-hover tbody tr:hover th {
    background-color: #F3FEB8;
  }

  /* .table-striped{
      background-color:#E9F391FF !important;
    } */
  .dataTables_filter {
    padding-bottom: 20px !important;
  }

  #frompacking {
    width: 100%;
    height: 2% !important;
    margin: 0 auto;
  }
</style>
<div id="main">
  <header class="mb-3">
    <input type="hidden" id="usernama" class="form-control" value="<?= $userlog ?>">
    <a href="#" class="burger-btn d-block d-xl-none">
      <i class="bi bi-justify fs-3"></i>
    </a>
  </header>
  <!-- Content Header (Page header) -->
  <div class="col-md-12 col-12">
    <!-- Default box -->
    <div class="card">
      <div class="card-header">
        <h5 class="text-center">Biaya Import</h5>
      </div>
      <div class="card-body">
        <div class="row col-md-12 col-12">
          <!-- <h3 class="text-center">Target upload</h3> -->

          <div class="col-md-8">
            <form id="form_filter">
              <div class=" row col-md-8">
                <div style="width:25%;" class="col-md-2">
                  <select class="form-control" id="filter_tahun"></select>
                </div>
                <!-- <div class="col-md-3">
                                          <select class ="form-control" id="filter_bulan"></select>
                                        </div> -->
                <!-- <div class="col-md-3">
                                          <select class ="form-control" id="filter_status"></select>
                                        </div> -->
                <!-- <div class="col-md-2">
                                        <button  type="button" name="filterdata" id="filterdata" class="btn btn-primary">Submit</button>
                                        </div> -->
              </div>
            </form>

          </div>


          <div class="col-md-4 text-end mb-3">
            <a class="btn" href="<?= base_url; ?>/transaksikurs/tambah">
              <i class="fa-solid fa-file-circle-plus fa-lg "></i></a>
          </div>

        </div>
        <div id="tabellist" class="table-responsive"></div>
      </div>
    </div>
    <!-- /.card-body -->
  </div>

  <div id="app"></div>
</div>


<script>
  $(document).ready(function() {

    const dateya = new Date();
    let bulandefault = dateya.getMonth() + 1;
    let tahundefault = dateya.getFullYear();
    let tahun = tahundefault;
    const userid = "<?= trim($userlog) ?>";
    //if(userid !==""){
    get_tahun();
    // get_Data(userid,tahun);
    $("#filter_tahun").val(tahun);

    const datas = {
      "userid": userid,
      "tahun": tahun
    };
    get_Data(datas);


    $(document).on("change", "#filter_tahun", function() {

      const tahun = $(this).val();
      const useridx = $("#usernama").val();

      const datas = {
        "userid": useridx,
        "tahun": tahun
      };
      get_Data(datas);
    })

  });
  // and document ready
  function get_tahun() {
    let startyear = 2020;
    let date = new Date().getFullYear();
    let endyear = date + 2;
    for (let i = startyear; i <= endyear; i++) {
      var selected = (i !== date) ? 'selected' : date;

      $("#filter_tahun").append($(`<option />`).val(i).html(i).prop('selected', selected));
    }
  }

  function get_Data(datas) {

    $.ajax({
      url: "<?= base_url ?>/transaksikurs/listdata",
      data: datas,
      method: "POST",
      dataType: "json",
      success: function(result) {
        Set_Tabel(result);

      }
    });
  }


  function Set_Tabel(result) {
    let datatabel = `
          <table id="tabel1" class="table table-striped table-hover" style="width:100%">                    
            <thead id="thead" class="thead">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>NoPo</th>
                <th>Ket</th>
                <th class="text-end">Amount</th>
                <th>User</th>
                <th>Posting</th>
                <th>Edit</th>
                <th>Print</th>
              </tr>
            </thead>
            <tbody>
        `;

    let no = 1;
    $.each(result, function(a, b) {
      let status = b.status;
      let ket = rep_kutif(b.Note);
      let idpack = rep_kutif(b.No_Pli);
      let formatTanggal = moment(b.EntryDate).format("DD-MM-YYYY");

      let [tgl, bln, thn] = formatTanggal.split("-");
      let th = thn.substr(2, 2);
      let tanggal = `${tgl}-${bln}-${th}`;
      let EntryDate = moment(b.EntryDate).format("YYYY-MM-DD");

      datatabel += `
            <tr>
              <td>${no++}</td>
              <td style="width:10%">${tanggal}<sup class="text-info">${b.Totaldetail}</sup></td>
              <td>${b.supid}</td>
              <td>${b.NoPo}</td>
              <td>${ket}</td>
              <td class="text-end">${b.Forwarder}</td>
              <td>${b.userid}</td>
              <td>
                <form id="frompacking" role="form" action="<?= base_url; ?>/transaksikurs/posting" method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="NoPo" value="${b.NoPo}">
                  <input type="hidden" name="supid" value="${b.supid}">
                  <input type="hidden" name="No_Pli" value="${b.No_Pli}">
                  <input type="hidden" name="No_Pls" value="${b.No_Pls}">
                  <input type="hidden" name="EntryDate" value="${EntryDate}">
                   <input type="hidden" name="id_bl_awb" value="${b.id_bl_awb}">
                  <input type="hidden" name="Note" value="${b.Note}">
                  <input type="hidden" name="POTransacid" value="${b.POTransacid}">
                  <input type="hidden" name="Pib" value="${b.Pib}">
                  <input type="hidden" name="Forwarder" value="${b.Forwarder}">
                  <input type="hidden" name="Total" value="${b.Total}">
                   <input type="hidden" name="Note2" value="${b.Note2}">
                   <input type="hidden" name="NamaProduk" value="${b.NamaProduk}">
                  <button type="submit" class="btn btn-warning" title="Posting">
                    <i class="fas fa-fan"></i>
                  </button>
                </form>
              </td>
              <td>
                <form id="frompacking" role="form" action="<?= base_url; ?>/transaksikurs/edit" method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="NoPo" value="${b.NoPo}">
                  <input type="hidden" name="supid" value="${b.supid}">
                  <input type="hidden" name="No_Pli" value="${b.No_Pli}">
                  <input type="hidden" name="No_Pls" value="${b.No_Pls}">
                  <input type="hidden" name="EntryDate" value="${EntryDate}">
                  <input type="hidden" name="id_bl_awb" value="${b.id_bl_awb}">
                  <input type="hidden" name="Note" value="${b.Note}">
                  <input type="hidden" name="POTransacid" value="${b.POTransacid}">
                  <input type="hidden" name="Pib" value="${b.Pib}">
                  <input type="hidden" name="Forwarder" value="${b.Forwarder}">
                  <input type="hidden" name="Total" value="${b.Total}">
                  <input type="hidden" name="Note2" value="${b.Note2}">
                   <input type="hidden" name="NamaProduk" value="${b.NamaProduk}">
                  <button type="submit" class="btn btn-info" title="Edit">
                    <i class="fa-solid fa-file-pen"></i>
                  </button>
                </form>
              </td>
              <td>
                <form id="frompacking" role="form" action="<?= base_url; ?>/laporankurs/cetakprint" method="POST" target="_blank" enctype="multipart/form-data">
                  <input type="hidden" name="No_Pls" value="${b.No_Pls}">
                  <input type="hidden" name="POTransacid" value="${b.POTransacid}">
                  <button type="submit" class="btn btn-info">
                    <i class="fa-solid fa-print"></i>
                  </button>
                </form>
              </td>
            </tr>
          `;
    });

    datatabel += `
            </tbody>
          </table>
        `;

    $("#tabellist").empty().html(datatabel);
    Tampildatatabel();
  }






  function rep_kutif(data) {

    return data.replace(/&amp;apos;/g, "'").replace(/&amp;quot;/g, '"')
  }

  function Tampildatatabel() {

    const id = "#tabel1";
    $(id).DataTable({
      order: [
        [0, 'asc']
      ],
      responsive: true,
      "ordering": true,
      "destroy": true,
      pageLength: 5,
      lengthMenu: [
        [5, 10, 20, -1],
        [5, 10, 20, 'All']
      ],
      fixedColumns: {
        // left: 1,
        right: 1
      },

    })
  }
</script>