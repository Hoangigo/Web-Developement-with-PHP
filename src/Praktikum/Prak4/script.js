class Warenkorb {
    constructor() {
        this.address = "";
        this.total = 0;
        this.selected = Array();
    }
    addItem(eId, ePrice, eName) {
        let pizza = {
            name: eName,
            id: eId,
            price: ePrice
        }
        this.selected.push(pizza);
        this.total += ePrice;
        this.total = Number(this.total.toFixed(2));

        var option = document.createElement("option");
        option.text = eName;

        option.value = eId;


        var x = document.getElementById('idCart');
        x.add(option);
        this.displayTotal();
    }

    displayTotal() {
        var x = document.getElementById('idTotalPrice');
        x.textContent = this.total.toFixed(2);
    }

    deleteSelectedItems() {
        var x = document.getElementById('idCart');
        for (let i = 0; i < x.length; i++) {
            if (x.options[i].selected) {
                this.total -= this.selected[i].price;
                this.total = Number(this.total.toFixed(2));
                this.selected.splice(i, 1);
                x.remove(i);
                i--;
            }
        }
        this.displayTotal();
    }
    deleteAll() {
        this.total = 0;
        alert("Warenkorb gelöscht");
        var x = document.getElementById('idCart');
        for (var i = 0; i < this.selected.length; i++) {
            x.remove(0);
        }

        this.selected = [];
        this.displayTotal();
    }
}

function submitCart(){
    let x=document.getElementById('idCart');
    let address=document.getElementById('address').value;
    if(x.length>0 && address.length>0){
        for(let i=0;i<x.length;i++){
        x.options[i].selected=true;
    }
    document.getElementById('formular').submit();
    }
}