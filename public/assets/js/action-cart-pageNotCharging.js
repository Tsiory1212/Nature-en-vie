$(document).ready(function(){
    /** PANIER avant checkout (stepper) */
    // Add one item
    $('a.js-cart-add-item').on('click', function(e) {
        e.preventDefault();

        // $(this)[0] repésente l'élément => a 
        const url = $(this)[0].href;
        
        const quantity_item =  $(this).parent().prev().prev().children();
        const total_price_item =  $(this).parent().prev();
        const total_price =  $('td.total-general');

        axios.get(url).then(function(response){
            quantity_item.text( ' '+response.data.quantity);
            total_price_item.text(response.data.total_price_item.toFixed(2));
            total_price.text(response.data.total_price.toFixed(2));
        })
    })

    // Remove one item
    $('a.js-cart-remove-item').on('click', function(e) {
        e.preventDefault();

        // $(this)[0] repésente l'élément => a 
        const url = $(this)[0].href;
        
        const quantity_item =  $(this).parent().prev().prev().children();
        const total_price_item =  $(this).parent().prev();
        const total_price =  $('td.total-general');

        // On vérifie, si la quantity de l'article ne reste que 1, alors on supprime la ligne où il se positionne
        if (quantity_item.text() == 1) {
            const item = $(this).parent().parent().remove();
            const url = $(this).next()[0].href;
            axios.get(url).then(function(response){
                total_price.text(response.data.total_price.toFixed(2));
            })

        }else{
            axios.get(url).then(function(response){
                quantity_item.text( " "+response.data.quantity);
                total_price_item.text(response.data.total_price_item.toFixed(2));
                total_price.text(response.data.total_price.toFixed(2));
            })
        }

    })

    // Delete item

    $('a.js-cart-item_delete').on('click',function(e) {
        e.preventDefault();
        const total_price =  $('td.total-general');

        $(this).parent().parent().remove();
        const url = $(this)[0].href;
        axios.get(url).then(function(response){
            total_price.text(response.data.total_price.toFixed(2));
        })
    })


   

    /** Mini cart */
    // Add one OR remove one item
    $('.js-add-cart-and-toast').on('click', function(e) {
        e.preventDefault();

        // this repésente l'élément => a 
        const url = $(this)[0].href;
        
        var allQuantityItem =  $('.minicart-block .sub-total .quantity-item');
        var total_price =  $('.minicart-block .sub-total .total-price');
        var product_element_dom_mini_cart = $('.minicart-block .cart-content .products')[0];
        
        var hidden_li_item_mini_cart =  $('#hidden-li-item-mini-cart')[0];
        const a = $(this)

        a.text("...") ;
        a.addClass("disabled");
        
        axios.get(url)
            .then(function(response){

                // allQuantityItem.textContent = response.data.allQuantityItem;
                // total_price.textContent = response.data.total_price.toFixed(2);

                
                allQuantityItem.text(response.data.allQuantityItem);
                total_price.text(response.data.total_price.toFixed(2));
                
                // On anime le bouton 
                a.text("Ajouté") ;
                setTimeout(function(){
                    a.removeClass("disabled");
                    a.text("Ajouter au panier");
                }, 500)

                $("#Toast-add-product").toast("show").removeClass("fade");
            })
    })
   
    // Delete item
    $('a.js-mini_cart-item_delete').on('click',function(e) {
        e.preventDefault();
        var allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
        var total_price =  $('.minicart-block .sub-total .total-price')[0]

        $(this).parent().parent().parent().remove()
        const url = $(this)[0].href;
        axios.get(url).then(function(response){
            allQuantityItem.textContent = response.data.allQuantityItem;
            total_price.textContent = response.data.total_price.toFixed(2);        
        })
    })


    //After click (Loupe modal) quick-view-block
    $('a.js-quick-view-block').each(function(){
        $(this).on("click", function(){
            // in quick_view_block (pop-up)
            const product_name_quick_view =  $('#biolife-quickview-block .product-attribute .title');
            const product_price_quick_view =  $('#biolife-quickview-block .product-attribute .price-amount');
            const product_detail_quick_view =  $('#biolife-quickview-block .product-attribute .excerpt');
            const product_category_quick_view =  $('#biolife-quickview-block .product-attribute .product-meta .meta-categories span');
            const product_cover_quick_view =  $('#biolife-quickview-block .media img');
            const product_explanation_quick_view =  $('#biolife-quickview-block .product-attribute .product-meta .meta-explanation');
            
            // in product_card_block 
            const product_name =   $(this).parent().next().children('.product-title').text();
            const product_detail =  $(this).parent().next().children('.product-description').text();
            const product_price = $(this).parent().next().children('.price').children('ins').children('.price-amount').children('#js-price-amount').text() ;
            const product_category =  $(this).parent().next().children('.categories').text();
            const product_packaging = $(this).parent().next().children('.product-packaging').children('.quantity').text();
            const product_quantity_unit = $(this).parent().next().children('.product-packaging').children('.quantity_unit').text();
            const product_cover =  $(this).prev().children('img').attr('src')

            // Affectation
            product_name_quick_view.text(product_name);
            product_price_quick_view.text(product_price + ' €');
            product_detail_quick_view.text(product_detail);
            product_category_quick_view.text(product_category);
            product_cover_quick_view.attr('src', product_cover);


            if(product_packaging > 1){
                price_per_unity = product_price / product_packaging;
                product_explanation_quick_view.text(price_per_unity.toFixed(2) + '€ /'+product_quantity_unit)
            }else{
                product_explanation_quick_view.text(product_price + '€ /Kg')
            }
        });
    })
   
})