$(document).ready(function(){

    // Add one OR remove one item
    $('a.js-cart-item').on('click', function(e) {
        e.preventDefault();

        // $(this)[0] repésente l'élément => a 
        const url = $(this)[0].href;
        
        const quantity_item =  $(this).parent().prev().prev()
        const total_price_item =  $(this).parent().prev()
        const total_price =  $('td.total-general')

        axios.get(url).then(function(response){
            quantity_item.text( "x"+response.data.quantity);
            total_price_item.text(response.data.total_price_item.toFixed(2));
            total_price.text(response.data.total_price.toFixed(2));
        })
    })

    // Delete item
    $('a.js-cart-item_delete').on('click',function(e) {
        e.preventDefault();
        const total_price =  $('td.total-general')

        $(this).parent().parent().remove()
        const url = $(this)[0].href;
        axios.get(url).then(function(response){
            total_price.text(response.data.total_price.toFixed(2));
        })
    })





    /** Mini cart */
    // Add one OR remove one item

    $( document ).ready(function() {
        $('.js-add-cart-and-toast').on('click', function(e) {
            e.preventDefault();

            // this repésente l'élément => a 
            const url = $(this)[0].href;
            
            var allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
            var total_price =  $('.minicart-block .sub-total .total-price')[0]
            var product_element_dom_mini_cart = $('.minicart-block .cart-content .products')[0];
            
            var hidden_li_item_mini_cart =  $('#hidden-li-item-mini-cart')[0];
            const a = $(this)

            a.text("...") ;
            a.addClass("disabled");
            
            axios.get(url)
                .then(function(response){

                    allQuantityItem.textContent = response.data.allQuantityItem;
                    total_price.textContent = response.data.totalPrice.toFixed(2);
                    
                    // On anime le bouton 
                    a.text("Ajouté") ;
                    setTimeout(function(){
                        a.removeClass("disabled");
                        a.text("Ajouter au panier");
                    }, 500)

                    $("#Toast-add-product").toast("show").removeClass("fade");
                })
        })
    });

    // Delete item
    $('a.js-mini_cart-item_delete').on('click',function(e) {
        e.preventDefault();
        var allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
        var total_price =  $('.minicart-block .sub-total .total-price')[0]

        $(this).parent().parent().parent().remove()
        const url = $(this)[0].href;
        axios.get(url).then(function(response){
            allQuantityItem.textContent = response.data.allQuantityItem;
            total_price.textContent = response.data.totalPrice.toFixed(2);        
        })
    })



    
    //After click (Loupe modal) quick-view-block
    function onClickBtnQuickViewProduct(event){
        // in quick_view_block (pop-up)
        const product_name_quick_view =  $('#biolife-quickview-block .product-attribute .title')[0]
        const product_price_quick_view =  $('#biolife-quickview-block .product-attribute .price-amount')[0]
        const product_detail_quick_view =  $('#biolife-quickview-block .product-attribute .excerpt')[0]
        const product_category_quick_view =  $('#biolife-quickview-block .product-attribute .product-meta li')[0]
    
        const product_cover_quick_view =  $('#biolife-quickview-block .media img')[0]
        
        // in product_card_block 
        const product_name =  $(this)[0].parentNode.nextElementSibling.querySelector('.product-title').textContent
        const product_detail =  $(this)[0].parentNode.nextElementSibling.querySelector('.product-description').textContent
        const product_price =  $(this)[0].parentNode.nextElementSibling.querySelector('ins').textContent
        const product_category =  $(this)[0].parentNode.nextElementSibling.querySelector('.categories').textContent

        const product_cover =  $(this)[0].previousElementSibling.querySelector('img').getAttribute('src')

        
        product_name_quick_view.textContent = product_name;
        product_price_quick_view.textContent = product_price;
        product_detail_quick_view.textContent = product_detail;
        product_category_quick_view.textContent =  product_category;

        product_cover_quick_view.src =  product_cover;

    }

    document.querySelectorAll('.quick-view-block-js').forEach(function(link){
        link.addEventListener('click', onClickBtnQuickViewProduct)
    })


})