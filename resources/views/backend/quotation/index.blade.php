@extends('backend.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Quotation List')}}</h3>
            </div>
            {!! Form::open(['route' => 'quotations.index', 'method' => 'get']) !!}
            <div class="row mb-3">
                <div class="col-md-5 offset-md-0 ml-5 mt-4">
                    <div class="form-group row">
                        <label class="d-tc mt-1"><strong>{{trans('file.Choose Your Date')}} : </strong>&nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$starting_date}} To {{$ending_date}}" required />
                                <input type="hidden" name="starting_date" value="{{$starting_date}}" />
                                <input type="hidden" name="ending_date" value="{{$ending_date}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="form-group row">
                        <label class="d-tc mt-1"><strong>{{trans('file.Choose Warehouse')}} : </strong> &nbsp;</label>
                        <div class="d-tc">
                            <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($lims_warehouse_list as $warehouse)
                                    @if($warehouse->id == $warehouse_id)
                                        <option selected value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @else
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-0 mt-4">
                    <div class="form-group">
                        <button class="btn btn-primary" id="filter-btn" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @if(in_array("quotes-add", $all_permission))
            <a href="{{route('quotations.create')}}" class="btn btn-info" id="btn-add-quotation"><i class="dripicons-plus"></i> {{trans('file.Add Quotation')}}</a>&nbsp;        
        @endif    

    </div>
    <div class="table-responsive">
        <table id="quotation-table" class="table quotation-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.Biller')}}</th>
                    <th>{{trans('file.customer')}}</th>
                    <th>{{trans('file.Supplier')}}</th>
                    <th>{{trans('file.Quotation Status')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            
            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="quotation-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                    <div class="col-md-6 d-print-none">
                        <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                        <input type="hidden" name="quotation_id">
                        <button id="email-btn" class="btn btn-default btn-sm d-print-none"><i class="dripicons-mail"></i> {{trans('file.Email')}}</button>
                    </div>

                    <div class="col-md-6 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="col-md-12">
                        <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                    </div>
                    <div class="col-md-12 text-center">
                        <i style="font-size: 15px;">{{trans('file.Quotation Details')}}</i>
                    </div>
                </div>
            </div>
            <div id="quotation-content" class="modal-body">
            </div>
            <br>
            <div>
                <label for="product-quotation-list" id="product-label"><strong>{{trans('file.Order Products')}}</strong></label>
                <table class="table table-bordered product-quotation-list" name="product-quotation-list">
                    <thead>
                        <th>#</th>
                        <th>{{trans('file.product')}}</th>
                        <th>{{trans('file.Batch No')}}</th>
                        <th>{{trans('file.Qty')}}</th>
                        <th>{{trans('file.Unit Price')}}</th>
                        <th>{{trans('file.Tax')}}</th>
                        <th>{{trans('file.Discount')}}</th>
                        <th>{{trans('file.Subtotal')}}</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <label for="service-quotation-list" id="service-label"><strong>{{trans('file.Order Services')}}</strong></label>
            <table class="table table-bordered service-quotation-list" name="service-quotation-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.Service')}}</th>
                    <th>{{trans('file.Price')}}</th>
                    <th>{{trans('file.Discount')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <label for="Bill"><strong>{{trans('file.Order Statistics')}}</strong></label>
            <table class="table table-bordered Bill-footer" name="Bill">
                <tbody></tbody>
            </table>
            <div id="quotation-footer" class="modal-body"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#quotation").siblings('a').attr('aria-expanded','true');
    $("ul#quotation").addClass("show");
    $("ul#quotation #quotation-list-menu").addClass("active");

    $(".daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period){
        var starting_date = startDate.format('YYYY-MM-DD');
        var ending_date = endDate.format('YYYY-MM-DD');
        var title = starting_date + ' To ' + ending_date;
        $(this).val(title);
        $('input[name="starting_date"]').val(starting_date);
        $('input[name="ending_date"]').val(ending_date);
      }
    });

    var all_permission = <?php echo json_encode($all_permission) ?>;
    var quotation_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    
    
    $("#email-btn").on("click" , function() {

        var quotationData = [];
        var quotation_id = $('input[name="quotation_id"]').val(); 
        quotationData = quotation_id;
        //alert(quotationData);
        if(confirm("Do you wand specify a new email to send it this bill?")){
            $(location).attr('href' , 'quotations/'+quotation_id+'/send_email');
        }else{
            $('#quotation-details').modal('hide');
            location.reload();
            $.ajax({
                url: 'quotations/sendmail',
                method: 'POST',
                data: {
                    quotationIdArray: quotationData,
                    
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });


    $(document).on("click", "tr.quotation-link td:not(:first-child, :last-child)", function() {
        var quotation = $(this).parent().data('quotation');
        quotationDetails(quotation);
    });

    $(document).on("click", ".view", function() {
        var quotation = $(this).parent().parent().parent().parent().parent().data('quotation');
        quotationDetails(quotation);
    });

    $("#print-btn").on("click", function(){
        var divContents = document.getElementById("quotation-details").innerHTML;
        console.log(divContents);
        var a = window.open('');
        a.document.write('<html>');
        a.document.write('<body><style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        setTimeout(function(){a.close();},10);
        a.print();
    });

    var starting_date = $("input[name=starting_date]").val();
    var ending_date = $("input[name=ending_date]").val();
    var warehouse_id = $("#warehouse_id").val();
    $('#quotation-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"quotations/quotation-data",
            data:{
                all_permission: all_permission,
                starting_date: starting_date,
                ending_date: ending_date,
                warehouse_id: warehouse_id
            },
            dataType: "json",
            type:"post",
            /*success:function(data){
                console.log(data);
            }*/
        },
        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('quotation-link');
            $(row).attr('data-quotation', data['quotation']);
        },
        "columns": [
            {"data": "key"},
            {"data": "date"},
            {"data": "reference_no"},
            {"data": "warehouse"},
            {"data": "biller"},
            {"data": "customer"},
            {"data": "supplier"},
            {"data": "status"},
            {"data": "grand_total"},
            {"data": "options"},
        ],
        'language': {
            /*'searchPlaceholder': "{{trans('file.Type date or quotation reference...')}}",*/
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['1', 'desc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 3, 4, 7, 8,9]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        quotation_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var quotation = $(this).closest('tr').data('quotation');
                                quotation_id[i-1] = quotation[13];
                            }
                        });
                        if(quotation_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'quotations/deletebyselection',
                                data:{
                                    quotationIdArray: quotation_id
                                },
                                success:function(data) {
                                    alert(data);
                                    //dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!quotation_id.length)
                            alert('Nothing is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }

    if(all_permission.indexOf("quotes-delete") == -1)
        $('.buttons-delete').addClass('d-none');


    function quotationDetails(quotation){

        var total_discount = 0;
        var amount_total = 0;
        
        //console.log(quotation);
        
        $('input[name="quotation_id"]').val(quotation[13]);
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+quotation[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+quotation[1]+'<br><strong>{{trans("file.Status")}}: </strong>'+quotation[2]+'<br>';
        if(quotation[25])
            htmltext += '<strong>{{trans("file.Attach Document")}}: </strong><a href="documents/quotation/'+quotation[25]+'">Download</a><br>';
        htmltext += '<br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>';
        htmltext += '<strong>{{trans("file.Name")}} : </strong>' + quotation[3] + '<br>';
        htmltext += '<strong>{{trans("file.Company Name")}} : </strong>' + quotation[4] + '<br>';
        htmltext += '<strong>{{trans("file.Email")}} : </strong>' + quotation[5] + '<br>';
        htmltext += '<strong>{{trans("file.Phone Number")}} : </strong>' + quotation[6] + '<br>'; 
        htmltext += '<strong>{{trans("file.Address")}} : </strong>' + quotation[7] + '<br>';
        htmltext += '<strong>{{trans("file.City")}} : </strong>' + quotation[8] + '</div>';
        htmltext += '<div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>';
        htmltext += '<strong>{{trans("file.Name")}} : </strong>' + quotation[9] + '<br>';
        htmltext += '<strong>{{trans("file.Phone Number")}} : </strong>' + quotation[10] + '<br>';
        htmltext += '<strong>{{trans("file.Address")}} : </strong>' + quotation[11] + '<br>';
        htmltext += '<strong>{{trans("file.City")}} : </strong>' + quotation[12] + '</div></div></div><br>';

        $.get('quotations/product_quotation/' + quotation[13], function(data){

            //console.log(data);
            
            if(data.length == 0){
                $(".product-quotation-list thead").hide();            
                $(".product-quotation-list tbody").remove();         
                $("#product-label").hide();         

            }else{

                $(".product-quotation-list tbody").remove();
                var name_code = data[0];
                var qty = data[1];
                var unit_code = data[2];
                var tax = data[3];
                var tax_rate = data[4];
                var discount = data[5];
                var subtotal = data[6];
                var batch_no = data[7];
                var newBody = $("<tbody>");
                $.each(name_code, function(index){
                    var newRow = $("<tr>");
                    var cols = '';
                    cols += '<td><strong>' + (index+1) + '</strong></td>';
                    cols += '<td>' + name_code[index] + '</td>';
                    cols += '<td>' + batch_no[index] + '</td>';
                    cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';
                    cols += '<td>' + parseFloat(subtotal[index] / qty[index]).toFixed({{$general_setting->decimal}}) + '</td>';
                    cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';
                    if(data[5][0] == null){
                        cols += '<td>' + {{number_format(0, $general_setting->decimal, '.', '')}} + '</td>';
                    }else{
                        cols += '<td>' + discount[index] + '</td>';
                    }
                    cols += '<td>' + subtotal[index] + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);

                    total_discount += discount[index];
                    amount_total += subtotal[index];
                });


                $("#product-label").show();
                $(".product-quotation-list thead").show();
                $("table.product-quotation-list").append(newBody);

            }
        });

        //console.log(quotation);
        $.get('quotations/service_quotation/' + quotation[13], function(data){

            if(data.length == 0){
                $(".service-quotation-list thead").hide();            
                $(".service-quotation-list tbody").remove();
                $("#service-label").hide();           

            }else{

                $(".service-quotation-list tbody").remove();
                
                var title_code = data[0];
                var price = data[1];
                var discount = data[2];
                var total = data[3];
                var newBody = $("<tbody>");

                //console.log(data[2][0] == null);

                $.each(title_code, function(index){
                    var newRow = $("<tr>");
                    var cols = '';
                    cols += '<td><strong>' + (index+1) + '</strong></td>';
                    cols += '<td>' + title_code[index] + '</td>';
                    cols += '<td>' + price[index] + '</td>';
                    if(data[2][0] == null){
                        cols += '<td>' + {{number_format(0, $general_setting->decimal, '.', '')}} + '</td>';
                    }else{
                        cols += '<td>' + discount[index] + '</td>';
                    }
                    cols += '<td>' + total[index] + '</td>';
                    total_discount += discount[index];
                    amount_total += total[index];
                    newRow.append(cols);
                    newBody.append(newRow);
                });

                $("#service-label").show();
                $(".service-quotation-list thead").show();  
                $("table.service-quotation-list").append(newBody);

            }
            
        });

        //console.log(quotation[20].length);

        $("table.Bill-footer tbody").remove();


        var newBody = $("<tbody>");

        var newRow = $("<tr>");
        cols = '';
        cols += '<td colspan=4><strong>{{trans("file.Order Tax")}}:</strong></td>';
        cols += '<td>' + quotation[17] + '(' + quotation[18] + '%)' + '</td>';
        newRow.append(cols);
        newBody.append(newRow);

        var newRow = $("<tr>");
        cols = '';
        if(quotation[19].length == 0){
            cols += '<td colspan=4><strong>{{trans("file.Order Discount")}}:</strong></td>';
            cols += '<td>' + {{number_format(0, $general_setting->decimal, '.', '')}} + '</td>';
        }else{
            cols += '<td colspan=4><strong>{{trans("file.Order Discount")}}:</strong></td>';
            cols += '<td>' + quotation[19] + '</td>';
        }
        newRow.append(cols);
        newBody.append(newRow);
        
        var newRow = $("<tr>");
        cols = '';
        if(quotation[20].length == 0){
            cols += '<td colspan=4><strong>{{trans("file.Shipping Cost")}}:</strong></td>';
            cols += '<td>' + {{number_format(0, $general_setting->decimal, '.', '')}} + '</td>';
        }else{
            cols += '<td colspan=4><strong>{{trans("file.Shipping Cost")}}:</strong></td>';
            cols += '<td>' + quotation[20] + '</td>';
        }
        newRow.append(cols);
        newBody.append(newRow);

        var newRow = $("<tr>");
        cols = '';
        cols += '<td colspan=4><strong>{{trans("file.grand total")}}:</strong></td>';
        cols += '<td>' + quotation[21] + '</td>';
        newRow.append(cols);
        newBody.append(newRow);


        $("table.Bill-footer").append(newBody);




        var htmlfooter = '<p><strong>{{trans("file.Note")}}:</strong> '+quotation[22]+'</p><strong>{{trans("file.Created By")}}:</strong><br>'+quotation[23]+'<br>'+quotation[24];
        $('#quotation-content').html(htmltext);
        $('#quotation-footer').html(htmlfooter);
        $('#quotation-details').modal('show');
    }

</script>
@endpush
