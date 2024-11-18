const plus = document.querySelector(".plus"),
    minus = document.querySelector(".minus"),
    num = document.querySelector(".num"),
    quantityInput = document.getElementById("quantityInput");

    let a = 1;

    plus.addEventListener("click", ()=> {
        a++;
        num.value = a; // Update the input field value
        quantityInput.value = a; // Update the hidden input value
    });

    minus.addEventListener("click", ()=> {
        if(a > 1){
            a--;
            num.value = a; // Update the input field value
            quantityInput.value = a; // Update the hidden input value
        }
    });