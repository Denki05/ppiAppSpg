@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-eye"></i> Report Jurnal Daily</li>
        </ol>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Card Filter -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-filter"></i> Filter Tanggal :</h5>
                        <input type="text" id="dateRange" class="form-control" placeholder="Pilih rentang tanggal">
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-filter"></i> Filter SPG :</h5>
                        <select class="form-control select2" name="spg" id="spg">
                            <option value="">Pilih SPG</option>
                            @foreach($sales as $row)
                                <option value="{{ $row->name }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card Tabel -->
            <div class="col-md-12 mt-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-table"></i> Data Jurnal Daily</h5>
                        <div class="table-responsive">
                            <table id="reportJurnal" class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">SPG</th>
                                        <th class="text-center">Customer</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-center">Brand</th>
                                        <th class="text-center">Qty Transaksi (ML)</th>
                                        <th class="text-center">Qty GA (BTL)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($get_data as $row)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_jurnal)->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $row->spg }}</td>
                                            <td class="text-center">{{ $row->customer }}</td>
                                            <td class="text-center">{{ $row->kode_jurnal }}</td>
                                            <td class="text-center">{{ $row->brand_jurnal }}</td>
                                            <td class="text-center">{{ $row->total_qty }}</td>
                                            <td class="text-center">{{ $row->total_qty_ga }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
        });

        var yesterday = moment().subtract(1, 'days'); 
        var formattedYesterday = yesterday.format('DD-MM-YYYY');

        $('#dateRange').daterangepicker({
            autoUpdateInput: true,
            startDate: yesterday,
            locale: {
                format: 'DD-MM-YYYY',
                cancelLabel: 'Clear'
            }
        });

        $('#dateRange').val(formattedYesterday + ' - ' + formattedYesterday);

        var table = $('#reportJurnal').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            order: [[1, 'asc']],
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-left'B><'col-sm-3'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { 
                    extend: 'excelHtml5', 
                    text: '<i class="fa-solid fa-file-excel"></i> Excel', 
                    className: 'btn btn-success btn-sm',
                    title: 'Laporan Jurnal Daily',
                    exportOptions: {
                        columns: ':visible',
                        modifier: { page: 'all' }
                    }
                },
                { 
                    extend: 'pdfHtml5', 
                    text: '<i class="fa-solid fa-file-pdf"></i> PDF', 
                    className: 'btn btn-danger btn-sm',
                    title: 'Laporan Jurnal Daily',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                        doc.styles.title = {
                            fontSize: 14,
                            bold: true,
                            alignment: 'center'
                        };
                        doc.styles.tableHeader = {
                            fillColor: '#343a40',
                            color: 'white',
                            bold: true,
                            alignment: 'center'
                        };
                        doc.defaultStyle.fontSize = 10;
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        // Custom filter DataTables berdasarkan tanggal dan SPG
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var min = $('#dateRange').data('daterangepicker').startDate;
            var max = $('#dateRange').data('daterangepicker').endDate;
            var selectedSPG = $('#spg').val();
            var date = moment(data[1], "DD-MM-YYYY");
            var spgColumn = data[2];

            if (date.isBetween(min, max, null, '[]')) {
                if (selectedSPG === "" || spgColumn == selectedSPG) {
                    return true;
                }
            }
            return false;
        });

        table.draw();

        $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            table.draw();
        });

        $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            table.draw();
        });

        $('#spg').on('change', function() {
            table.draw();
        });

        $('#exportExcel').on('click', function() {
            table.button('.buttons-excel').trigger();
        });

        $('#exportPDF').on('click', function() {
            table.button('.buttons-pdf').trigger();
        });
    });
</script>
@endsection