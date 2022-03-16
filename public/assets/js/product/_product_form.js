$('#product_price').attr('type', 'number')
  
var _refIdField = $('#product_referenceId');
const _refIdBtn = $('#btn-refid-edit');
function activeInput(){
    if (_refIdField.attr('readonly') === 'readonly') {
        _refIdField.attr('readonly', false);
        _refIdBtn.html('<i class="fa-solid fa-check"></i>');
    }else{
        _refIdField.attr('readonly', true) 
        _refIdBtn.html('<i class="fa-solid fa-pen"></i>');
    }
}