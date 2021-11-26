
//WHEN ADD ONE PRODUCT
function onClickBtnAddItem(event){
    event.preventDefault();

    // this repésente l'élément => a 
    const url = this.href;
    
    var allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
    var total_price =  $('.minicart-block .sub-total .total-price')[0]
    var product_element_dom_mini_cart = $('.minicart-block .cart-content .products')[0];
    var last_li_in_mini_cart =  $('.minicart-block .cart-content .products li:last')[0];
    
    var cln = last_li_in_mini_cart.cloneNode(true).text("Hello World");



    var txt1 = "<b>I qmlkjfmlsqjflsjq </b>";           // Create element with HTML
    var txt2 = $("<i></i>").text("love ");  // Create with jQuery
    var txt3 = document.createElement("b");   // Create with DOM
    txt3.innerHTML = "jQuery!";
    
    const a = this;
    this.textContent = "...";
    this.classList.add("disabled");
    
    
    axios.get(url)
        .then(function(response){
            // last_li_in_mini_cart.after(last_li_in_mini_cart);
            last_li_in_mini_cart.appendChild(cln);


            // On modifie les textes dans le mini-panier 
            allQuantityItem.textContent = response.data.allQuantityItem;
            total_price.textContent = response.data.totalPrice.toFixed(2);
            
            // On anime le bouton 
            a.textContent  = "Ajouté";
            setTimeout(function(){
                a.classList.remove("disabled");
                a.textContent  = "Ajouter au panier";
            }, 500)


        })
        // .catch(function(error) {
        //     alert('Une erreur s\'est produite')
        // })
}

document.querySelectorAll('a.js-add-cart').forEach(function(link){
    // link représente tout les éléments => a 
    link.addEventListener('click', onClickBtnAddItem)
})


//WHEN REMOVE ONE PRODUCT
function onClickBtnRemoveProduct(event){
    event.preventDefault();
    const url = this.href;

    const allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
    const total_price =  $('.minicart-block .sub-total .total-price')[0]

    axios.post(url)
        .then(function(response){
            allQuantityItem.textContent = response.data.allQuantityItem;
            total_price.textContent = response.data.totalPrice.toFixed(2)
        })
    
}

document.querySelectorAll('.action a.remove-product-js').forEach(function(link){
    // link représente tout les éléments => a 
    link.addEventListener('click', onClickBtnRemoveProduct)
})




//After click (Loupe) quick-view-block
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


