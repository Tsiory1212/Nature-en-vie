$('#product_price').attr('type', 'number')
$('#product_price_acn_allier').attr('type', 'number')
  
var _refIdField = $('#product_referenceId');
var _product_refCode = $('#product_refCode');
const _refIdBtn = $('#btn-refid-edit');
const _refrefCodeBtn = $('#btn-refCode-edit');

function activeInput(){
    if (_refIdField.attr('readonly') === 'readonly') {
        _refIdField.attr('readonly', false);
        _refIdBtn.html('<i class="fa-solid fa-check"></i>');
    }else{
        _refIdField.attr('readonly', true) 
        _refIdBtn.html('<i class="fa-solid fa-pen"></i>');
    }
}

function writeRefCode(){
    if (_product_refCode.attr('readonly') === 'readonly') {
        _product_refCode.attr('readonly', false);
        _refrefCodeBtn.html('<i class="fa-solid fa-check"></i>');
    }else{
        _product_refCode.attr('readonly', true) 
        _refrefCodeBtn.html('<i class="fa-solid fa-pen"></i>');
    }
}


