@extends('layouts.app')

@section('title', 'Production | New Order')

@section('content')
  <div class="row">
    <form id="productionForm" action="{{ route('production.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
     
      <div class="row">
        <div class="col-12 col-md-12 mb-3">
          <section class="card">
            <header class="card-header">
              <div style="display: flex;justify-content: space-between;">
                <h2 class="card-title">New Production</h2>
              </div>
            </header>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-md-2 mb-3">
                  <label>Production #</label>
                  <input type="text" class="form-control" value="{{ $nextProductionCode ?? '' }}" disabled/>
                </div>

                <div class="col-12 col-md-2">
                  <label>Category<span style="color: red;"><strong>*</strong></span></label>
                  <select class="form-control" name="category_id" required>
                    <option value="" selected disabled>Select Category</option>
                      @foreach($categories as $item)  
                        <option value="{{$item->id}}">{{$item->name}}</option>
                      @endforeach
                  </select>
                </div>

                <div class="col-12 col-md-2 mb-3">
                  <label>Vendor Name</label>
                  <select data-plugin-selecttwo class="form-control select2-js" name="vendor_id" id="vendor_name" required>
                    <option value="" selected disabled>Select Vendor</option>
                      @foreach($vendors as $item)  
                        <option value="{{$item->id}}">{{$item->name}}</option>
                      @endforeach
                  </select>
                </div>

                <div class="col-12 col-md-2 mb-3">
                  <label>Production Type</label>
                  <select class="form-control" name="production_type" id="production_type" required>
                    <option value="" selected disabled>Select Type</option>
                    <option value="cmt">CMT</option>
                    <option value="sale_leather">Sale Leather</option>
                  </select>
                  <input type="hidden" name="challan_generated" value="0">
                </div>

                <div class="col-12 col-md-2 mb-3">
                  <label>Order Date</label>
                  <input type="date" name="order_date" class="form-control" id="order_date" value="{{ date('Y-m-d') }}" required/>
                </div>

                <div class="col-12 col-md-2 mb-3">
                  <label>Challan #</label>
                  <input type="text" name="challan_no" class="form-control" value="{{ $nextChallanNo ?? '' }}" required/>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div class="col-12 col-md-12 mb-3">
          <section class="card">
            <header class="card-header" style="display: flex;justify-content: space-between;">
              <h2 class="card-title">Raw Material Details</h2>
            </header>
            <div class="card-body">
              <table class="table table-bordered" id="myTable">
                <thead>
                  <tr>
                    <th>Raw</th>
                    <th>Purchase #</th>
                    <th>Rate</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Total</th>
                    <th width="10%"></th>
                  </tr>
                </thead>
                <tbody id="PurPOTbleBody">
                  <tr class="item-row">
                    <td>
                      <select name="item_details[0][product_id]" id="productSelect0" class="form-control select2-js" onchange="onItemChange(this)" required>
                        <option value="" selected disabled>Select Leather</option>
                        @foreach($allProducts as $product)
                          <option value="{{ $product->id }}" data-unit="{{ $product->unit }}">{{ $product->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <select name="item_details[0][invoice_id]" id="invoiceSelect0" class="form-control" required onchange="onInvoiceChange(this)">
                        <option value="" disabled selected>Select Invoice</option>
                      </select>
                    </td>
                    <td><input type="number" name="item_details[0][item_rate]" id="item_rate_0" onchange="rowTotal(0)" step="any" value="0" class="form-control" placeholder="Rate" required/></td>
                    <td><input type="number" name="item_details[0][qty]" id="item_qty_0" onchange="rowTotal(0)" step="any" value="0" class="form-control" placeholder="Quantity" required/></td>
                    <td>
                      <select id="item_unit_0" class="form-control" name="item_details[0][item_unit]" required>
                        <option value="" disabled selected>Select Unit</option>
                         @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                         @endforeach          
                      </select>
                    </td>
                    <td><input type="number" id="item_total_0" class="form-control" placeholder="Total" disabled/></td>
                    <td width="5%">
                      <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-xs"><i class="fas fa-times"></i></button>
                      <button type="button" class="btn btn-primary btn-xs" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>

        <div class="col-12 col-md-5 mb-3">
          <section class="card">
            <header class="card-header d-flex justify-content-between">
              <h2 class="card-title">Voucher (Challan #)</h2>
              <div>
                <a class="btn btn-danger text-end" onclick="generateVoucher()">Generate Challan</a>
              </div>
            </header>
            <div class="card-body">
              <div class="row pb-4">
                <div class="col-12 mt-3" id="voucher-container"></div>
              </div>
            </div>
          </section>
        </div>

        <div class="col-12 col-md-7">
          <section class="card">
            <header class="card-header d-flex justify-content-between">
              <h2 class="card-title">Summary</h2>
            </header>
            <div class="card-body">
              <div class="row pb-4">
                <div class="col-12 col-md-3">
                  <label>Total Raw Quantity</label>
                  <input type="number" class="form-control" id="total_fab" placeholder="Total Qty" disabled/>
                </div>

                <div class="col-12 col-md-3">
                  <label>Total Raw Amount</label>
                  <input type="number" class="form-control" id="total_fab_amt" placeholder="Total Amount" disabled />
                </div>
                
                <div class="col-12 col-md-5">
                  <label>Attachment</label>
                  <input type="file" class="form-control" name="attachments[]" multiple accept="image/png, image/jpeg, image/jpg, image/webp">
                </div>

                <div class="col-12 text-end">
                  <h3 class="font-weight-bold mb-0 text-5 text-primary">Net Amount</h3>
                  <span><strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">0.00</span></strong></span>
                  <input type="hidden" name="total_amount" id="net_amount">
                </div>
              </div>
            </div>

            <footer class="card-footer text-end">
              <a class="btn btn-danger" href="{{ route('production.index') }}">Discard</a>
              <button type="submit" class="btn btn-primary">Create</button>
            </footer>
          </section>
        </div>
      </div>
    </form>
  </div>
  <script>
    var index = 1;
    const allProducts = @json($allProducts);

    document.addEventListener("DOMContentLoaded", function () {
      const form = document.getElementById('productionForm');

      form.addEventListener('submit', function (e) {
        const productionType = document.getElementById('production_type')?.value;
        const challanGenerated = document.querySelector('input[name="challan_generated"]')?.value;

        if (productionType === 'sale_leather' && challanGenerated !== '1') {
          e.preventDefault();
          alert("Please generate the challan before submitting the form.");
          return false;
        }
      });
    });

    function removeRow(button) {
      const tableRows = $("#PurPOTbleBody tr").length;
      if (tableRows > 1) {
        const row = button.closest('tr');
        row.remove();
        index--;
        tableTotal();
      }
    }

    function addNewRow() {
      const lastRow = $('#PurPOTbleBody tr:last');
      const latestValue = lastRow.find('select').val();

      if (latestValue !== "") {
        const table = document.getElementById('myTable').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.classList.add('item-row');

        const options = allProducts.map(p =>
          `<option value="${p.id}" data-unit="${p.unit ?? ''}">${p.name}</option>`
        ).join('');


        newRow.innerHTML = `
          <td>
            <select data-plugin-selecttwo name="item_details[${index}][product_id]" required id="productSelect${index}" class="form-control select2-js" onchange="onItemChange(this)">
              <option value="" disabled selected>Select Leather</option>
              ${options}
            </select>
          </td>
          <td>
            <select name="item_details[${index}][invoice_id]" id="invoiceSelect${index}" class="form-control" onchange="onInvoiceChange(this)" required>
              <option value="" disabled selected>Select Invoice</option>
            </select>
          </td>
          <td><input type="number" name="item_details[${index}][item_rate]" id="item_rate_${index}" step="any" value="0" onchange="rowTotal(${index})" class="form-control" required/></td>
          <td><input type="number" name="item_details[${index}][qty]" id="item_qty_${index}" step="any" value="0" onchange="rowTotal(${index})" class="form-control" required/></td>
          <td>
            <select id="item_unit_${index}" class="form-control" name="item_details[${index}][item_unit]" required>
              <option value="" disabled selected>Select Unit</option>
              @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
              @endforeach              
            </select>
          </td>
          <td><input type="number" id="item_total_${index}" class="form-control" placeholder="Total" disabled/></td>
          <td>
            <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-xs"><i class="fas fa-times"></i></button>
            <button type="button" onclick="addNewRow()" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i></button>
          </td>
        `;

        index++;
        $('#myTable select[data-plugin-selecttwo]').select2();
      }
    }

    function rowTotal(i) {
      const rate = parseFloat($(`#item_rate_${i}`).val()) || 0;
      const qty = parseFloat($(`#item_qty_${i}`).val()) || 0;
      const total = rate * qty;

      $(`#item_total_${i}`).val(total.toFixed(2));
      tableTotal();
    }

    function tableTotal() {
      let totalQty = 0;
      let totalAmt = 0;

      $('#PurPOTbleBody tr').each(function () {
        const rate = parseFloat($(this).find('input[id^="item_rate_"]').val()) || 0;
        const qty = parseFloat($(this).find('input[id^="item_qty_"]').val()) || 0;
        totalQty += qty;
        totalAmt += rate * qty;
      });

      $('#total_fab').val(totalQty);
      $('#total_fab_amt').val(totalAmt.toFixed(2));
      updateNetTotal(totalAmt);
    }

    function updateNetTotal(total) {
      const net = parseFloat(total) || 0;
      $('#netTotal').text(formatNumberWithCommas(net.toFixed(0)));
    }

    function formatNumberWithCommas(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    function onItemChange(select) {
      const row = select.closest('tr');
      const itemId = select.value;
      const option = select.selectedOptions?.[0];

      if (!row || !itemId || !option) return;

      const unit = option.getAttribute('data-unit');

      // Set the unit field
      const unitSelect = row.querySelector('select[name^="item_details"][name$="[item_unit]"]');
      if (unitSelect && unit) unitSelect.value = unit;

      const invoiceSelect = row.querySelector('select[name^="item_details"][name$="[invoice_id]"]');
      if (!invoiceSelect) return;

      invoiceSelect.innerHTML = '<option value="">Loading...</option>';

      // Clear other fields
      row.querySelector(`input[name^="item_details"][name$="[qty]"]`).value = '';
      row.querySelector(`input[name^="item_details"][name$="[item_rate]"]`).value = '';
      row.querySelector(`input[id^="item_total_"]`).value = '';

      // Load invoice data
      fetch(`/api/item/${itemId}/invoices`)
        .then(res => res.json())
        .then(data => {
          invoiceSelect.innerHTML = '<option value="">Select Invoice</option>';
          data.forEach(inv => {
            invoiceSelect.innerHTML += `<option value="${inv.id}">#${inv.id} - ${inv.vendor}</option>`;
          });
        })
        .catch(() => {
          invoiceSelect.innerHTML = '<option value="">Error loading invoices</option>';
        });
    }

    function onInvoiceChange(select) {
      const row = select.closest('tr');
      const invoiceId = select.value;
      const itemSelect = row.querySelector('select[name^="item_details"][name$="[product_id]"]');
      const itemId = itemSelect?.value;

      if (!invoiceId || !itemId) return;

      fetch(`/invoice-item/${invoiceId}/item/${itemId}`)
        .then(res => res.json())
          .then(data => {
            if (!data.error) {
              row.querySelector(`input[name^="item_details"][name$="[qty]"]`).value = data.quantity || 0;
              row.querySelector(`input[name^="item_details"][name$="[item_rate]"]`).value = data.price || 0;

              // Trigger row total
              const rowIndex = row.rowIndex - 1; // Adjust index if header row exists
              rowTotal(rowIndex);
            }
          })
          .catch(() => {
            console.warn("Failed to fetch invoice-item data.");
          });
    }

    function generateVoucher() {
      const voucherContainer = document.getElementById("voucher-container");
      voucherContainer.innerHTML = "";

      const vendorName = document.querySelector("#vendor_name option:checked")?.textContent ?? "-";
      const orderDate = $('#order_date').val();
      const challanNo = "PROD-" + Math.floor(100000 + Math.random() * 900000);

      let itemsHTML = "";
      let grandTotal = 0;

      document.querySelectorAll(".item-row").forEach((row) => {
        const productName = row.querySelector('select[name*="[product_id]"] option:checked')?.textContent ?? "-";
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
        const unit = row.querySelector('select[name*="[item_unit]"] option:checked')?.textContent ?? "-";
        const rate = parseFloat(row.querySelector('input[name*="[item_rate]"]')?.value || 0);
        const total = qty * rate;
        grandTotal += total;

        itemsHTML += `
          <tr>
            <td>${productName}</td>
            <td>${qty} ${unit}</td>
            <td>${rate.toFixed(2)}</td>
            <td>${total.toFixed(2)}</td>
          </tr>
        `;
      });

      const html = `
        <div class="border p-3 mt-3">
          <h3 class="text-center text-dark">Production Challan</h3>
          <hr>

          <div class="d-flex justify-content-between text-dark">
            <p><strong>Vendor:</strong> ${vendorName}</p>
            <p><strong>Challan No:</strong> ${challanNo}</p>
            <p><strong>Date:</strong> ${orderDate}</p>
          </div>

          <table class="table table-bordered mt-3">
            <thead class="bg-light">
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              ${itemsHTML}
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="text-end">Grand Total</th>
                <th>${grandTotal.toFixed(2)}</th>
              </tr>
            </tfoot>
          </table>

          <input type="hidden" name="voucher_amount" value="${grandTotal}">
          <input type="hidden" name="challan_no" value="${challanNo}">
          <input type="hidden" name="challan_generated" value="1">

          <div class="d-flex justify-content-between mt-4">
            <div>
              <p class="text-dark"><strong>Authorized By:</strong></p>
              <p>________________________</p>
            </div>
          </div>
        </div>
      `;

      voucherContainer.innerHTML = html;

      // Also ensure challan_no and challan_generated are attached to form (in case form is submitted separately)
      const form = document.querySelector('form');

      const ensureHiddenInput = (name, value) => {
        let input = form.querySelector(`input[name="${name}"]`);
        if (!input) {
          input = document.createElement('input');
          input.type = 'hidden';
          input.name = name;
          form.appendChild(input);
        }
        input.value = value;
      };

      ensureHiddenInput("challan_no", challanNo);
      ensureHiddenInput("challan_generated", "1");
      ensureHiddenInput("voucher_amount", grandTotal.toFixed(2));
    }

    $(document).on("click", ".delete-row", function () {
      $(this).closest("tr").remove();
    });

    $(document).ready(function () {
      $('.select2-js').select2();
    });
  </script>

@endsection
