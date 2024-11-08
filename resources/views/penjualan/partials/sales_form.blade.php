<div class="row g-3 mt-4">
    <input type="hidden" name="brand" value="{{ $brand }}">

    <!-- DOM and OUT DOM -->
    <div class="col-md-6">
        <label for="{{ strtolower($brand) }}Dom" class="form-label">DOM <span class="text-danger">*</span></label>
        <select name="dom" id="{{ strtolower($brand) }}Dom" class="form-control select2" required style="width: 100%;">
            <option value="">Pilih Kota Domisili</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="{{ strtolower($brand) }}OutDom" class="form-label">OUT DOM <span class="text-danger">*</span></label>
        <select name="out_dom" id="{{ strtolower($brand) }}OutDom" class="form-control select2" required style="width: 100%;">
            <option value="">Pilih Kota Luar Domisili</option>
        </select>
    </div>

    <!-- Payment Type -->
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_type_{{ strtolower($brand) }}" id="{{ strtolower($brand) }}Cash" value="cash">
            <label class="form-check-label" for="{{ strtolower($brand) }}Cash">CASH</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_type_{{ strtolower($brand) }}" id="{{ strtolower($brand) }}Customer" value="customer" checked>
            <label class="form-check-label" for="{{ strtolower($brand) }}Customer">CUSTOMER</label>
        </div>
    </div>

    <hr>
    <!-- Product Table -->
    <div class="col-12">
        <button type="button" class="btn btn-success addRow" data-brand="{{ $brand }}"><i class="fa fa-plus"></i> Add Row</button>
        <table id="{{ strtolower($brand) }}Table" class="table table-striped mt-3">
            <thead>
                <tr>
                    <th class="text-center" style="width: 20%">Variant</th>
                    <th class="text-center" style="width: 15%">Qty</th>
                    <th class="text-center" style="width: 10%">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added dynamically here -->
            </tbody>
        </table>
    </div>
</div>