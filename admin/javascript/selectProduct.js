document.getElementById('dropdown').addEventListener('change',function(){
    const selectedCategories  = this.value;
    const hiddenBlock = document.querySelector('.hidden');
    const hiddenStock = document.querySelector('.stock');
  
    if (selectedCategories === 'Apparel') {  
      hiddenBlock.style.display = 'block';  
      hiddenStock.style.display = 'none' 
    } else {
      hiddenBlock.style.display = 'none';   
      hiddenStock.style.display = 'block'
    }
  });