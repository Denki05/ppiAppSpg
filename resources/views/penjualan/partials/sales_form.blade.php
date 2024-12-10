<div class="row g-3 mt-4">
    <input type="hidden" name="brand" value="{{ $brand }}">

    <!-- DOM and OUT DOM -->
    <div class="col-md-6 dom-field">
        <label for="{{ strtolower($brand) }}Dom" class="form-label">DOM <span class="text-danger">*</span></label>
        <select name="dom" id="{{ strtolower($brand) }}Dom" class="form-control select2" required style="width: 100%;">
            <option value="">Pilih Kota Domisili</option>
        </select>
    </div>

    <div class="col-md-6 out-dom-field">
        <label for="{{ strtolower($brand) }}OutDom" class="form-label">OUT DOM <span class="text-danger">*</span></label>
        <select name="out_dom" id="{{ strtolower($brand) }}OutDom" class="form-control select2" required style="width: 100%;">
            <option value="">Pilih Kota Luar Domisili</option>
        </select>
    </div>

    <!-- Payment Type -->
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_type_{{ strtolower($brand) }}" id="{{ strtolower($brand) }}Cash" value="1">
            <label class="form-check-label" for="{{ strtolower($brand) }}Cash">CASH</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_type_{{ strtolower($brand) }}" id="{{ strtolower($brand) }}Customer" value="2" checked>
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
                    <th class="text-center">Variant</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added dynamically here -->
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('.select2').select2();

    // Function to toggle DOM and OUT DOM visibility
    function toggleDomFields(brand) {
        const customerRadio = $(`#${brand}Customer`);
        const domFields = $(`#${brand}Dom`).closest('.col-md-6');
        const outDomFields = $(`#${brand}OutDom`).closest('.col-md-6');

        if (customerRadio.is(':checked')) {
            domFields.show();
            outDomFields.show();
        } else {
            domFields.hide();
            outDomFields.hide();
        }
    }

    // Add event listeners for payment type radios
    $('input[type="radio"][name^="payment_type_"]').on('change', function() {
        const brand = $(this).attr('name').replace('payment_type_', '');
        toggleDomFields(brand);
    });

    // Initial setup for DOM fields based on the selected radio button
    $('input[type="radio"][name^="payment_type_"]').each(function() {
        const brand = $(this).attr('name').replace('payment_type_', '');
        toggleDomFields(brand);
    });

    // Populate DOM dropdown
    function populateDomDropdown(brand) {
        const domSelect = $(`#${brand}Dom`);

        $.ajax({
            url: '{{ route('penjualan.checkCustomerDOM') }}',
            type: 'GET',
            success: function(customers) {
                domSelect.empty().append('<option value="">Pilih Kota Domisili</option>');
                customers.forEach(customer => {
                    domSelect.append(`<option value="${customer.customer_id}">${customer.customer_nama} - ${customer.customer_kecamatan} - ${customer.customer_kota}</option>`);
                });
            },
            error: function() {
                alert('Failed to load DOM options.');
            }
        });
    }

    // Populate OUTDOM dropdown
    function populateOutDomDropdown(brand) {
        const outDomSelect = $(`#${brand}OutDom`);

        $.ajax({
            url: '{{ route('penjualan.checkCustomerOUTDOM') }}',
            type: 'GET',
            success: function(customers) {
                outDomSelect.empty().append('<option value="">Pilih Kota Luar Domisili</option>');
                customers.forEach(customer => {
                    outDomSelect.append(`<option value="${customer.customer_id}">${customer.customer_kota} - ${customer.customer_kecamatan} - ${customer.customer_provinsi}</option>`);
                });
            },
            error: function() {
                alert('Failed to load OUTDOM options.');
            }
        });
    }

    // Trigger the function when a specific action occurs (e.g., tab switch or on load)
    const activeBrand = $('#brandTabs .nav-link.active').data('brand').toLowerCase();
    populateDomDropdown(activeBrand);
    populateOutDomDropdown(activeBrand);

    // Example: If needed, repopulate DOM dropdown on brand switch
    $('#brandTabs a').on('shown.bs.tab', function(e) {
        const brand = $(e.target).data('brand').toLowerCase();
        populateDomDropdown(brand);
        populateOutDomDropdown(brand);
    });
});
</script>
