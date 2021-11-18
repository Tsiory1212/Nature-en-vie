
//WHEN ADD ONE PRODUCT
function onClickBtnAddItem(event){
    event.preventDefault();

    // this repésente l'élément => a 
    const url = this.href;

    const allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
    const total_price =  $('.minicart-block .sub-total .total-price')[0]
    
    axios.get(url).then(function(response){
        allQuantityItem.textContent = response.data.allQuantityItem;
        total_price.textContent = response.data.totalPrice.toFixed(2);
    })
}

document.querySelectorAll('a.js-add-cart').forEach(function(link){
    // link représente tout les éléments => a 
    link.addEventListener('click', onClickBtnAddItem)
})


//WHEN REMOVE ONE PRODUCT
function onClickBtnRemoveProduct(event){
    const url = this.href;

    const allQuantityItem =  $('.minicart-block .sub-total .quantity-item')[0]
    const total_price =  $('.minicart-block .sub-total .total-price')[0]

    axios.post(url)
    .then(function(response){
        allQuantityItem.textContent = response.data.allQuantityItem;
        total_price.textContent = response.data.totalPrice.toFixed(2);

    })
    .catch(function (error) {
        alert("Erreur de suppression");
    })
    
}

document.querySelectorAll('.action a.remove').forEach(function(link){
    // link représente tout les éléments => a 
    link.addEventListener('click', onClickBtnRemoveProduct)
})