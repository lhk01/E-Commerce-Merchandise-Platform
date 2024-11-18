async function fetchVoucher() {
  try {
    const voucher = await fetch("../function/fetch-voucher.php");

    if (!voucher.ok) {
      throw new Error(`HTTP error! Status: ${voucher.status}`);
    }

    const data = await voucher.json();
    const print = document.querySelector('#voucher');
    const total = document.getElementById('total');
    let originalPrice = parseFloat(total.textContent.replace(/[^0-9.-]+/g, ""));
 // Original price from the page
    print.innerHTML = "";

    if (data.length > 0) {
      let item = `<select class = "voucher" name='voucher' id='voucherSelect'> 
                   <option value='' disabled selected>Select Voucher</option>
                   <option value=''>None</option>`;
      data.forEach(voucher => {
        item += `
        <option value='${voucher.discount}'>${voucher.voucher_name}</option>
        `;
        
      });
      item += `</select>`;
      print.innerHTML += item;
      console.log(item);
    } else {
      let item = `<select name='voucher'>  
                   <option value='' disabled selected>No Voucher</option>
                 </select>`;
      print.innerHTML += item;
      
    }

    const voucherSelect = document.getElementById('voucherSelect');
    voucherSelect.addEventListener('change', function () {
      const displayVoucher = document.getElementById('price');
      const selectedValue = parseFloat(voucherSelect.value) || 0; // Convert selectedValue to number
      const selectedText = voucherSelect.options[voucherSelect.selectedIndex].text;
      const vocher_error = document.getElementById('vocher-eror');
      
      let newPrice;
      let result = '';
      

      if(selectedValue > originalPrice){
        vocher_error.style.display = "flex";
        vocher_error.innerHTML = "You cannot use this voucher because the discount is greater than the total amount.";
        displayVoucher.style.display = "none";

      }else if(selectedValue === 0){
        total.textContent = `RM ${originalPrice.toFixed(2)}`;
        vocher_error.style.display = "none";
        displayVoucher.style.display = "none";
      }else{
        newPrice = originalPrice - selectedValue;
        total.textContent = `RM ${newPrice.toFixed(2)}`;
        vocher_error.style.display = "none";
        displayVoucher.style.display = "flex";
        result = `<p>Discount</p><p> - RM ${selectedValue.toFixed(2)}</p>`;
        displayVoucher.innerHTML = result;
      }


      
    });

  } catch (error) {
    console.error('Error fetching voucher data:', error);
    document.querySelector('#customerTable tbody').innerHTML = '<tr><td colspan="5">Error loading voucher.</td></tr>';
  }
}

window.onload = fetchVoucher;
