
$( document ).ready(function() {
    // Customisation select2 
    $("select#category").select2({
        placeholder: "Catégory",
        allowClear: true
    });
    $("select#classement").select2({
        placeholder: "Classement",
        allowClear: true
    });
    $("select#gamme").select2({
        placeholder: "Gamme",
        allowClear: true,
    })

    $("select#reccurent_paypal").select2({
        placeholder: "Selectionner l'engagement",
		width: '30%'
    })
    
});