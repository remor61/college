function getChart(chartID){
    
    document.getElementById('removeAsset').style.display = 'none';
    document.getElementById('addAsset').style.display = 'none';
    document.getElementById('netWorth').style.display = 'block';

    charts = document.getElementsByClassName('chart');

    //hide charts
    for(let i = 0; i < charts.length;i++){
        charts[i].style.display = 'none';
    }
    //make chart for current coin visible
    document.getElementById(chartID).style.display = 'block';

    //make net worth for current coin visible
    if(chartID !== 'pnlChart'){
        document.getElementById('pnl').style.display = 'none';
        document.getElementById('pnlcoin').style.display = 'block';
    }else{
        document.getElementById('pnl').style.display = 'block';
        document.getElementById('pnlcoin').style.display = 'none';
    }
}

function hideNetWorth(current){

    chartInterchange = document.getElementsByClassName('chartInterchange');

    for(let i = 0; i < chartInterchange.length;i++){
        chartInterchange[i].style.display = 'none';
    }

    document.getElementById(current).style.display = 'block';
}

function add(price,quantity){

    const re = /^\d+(\.\d+)*$/;

    if(!re.test(price)){
        alert("Invalid price. Insert numbers only.")
    }
    else if(!re.test(quantity)){
        alert("Invalid quantity. Insert numbers only.");
    }else{
        document.getElementById('addAsset').style.display = 'none';
        document.getElementById('netWorth').style.display = 'block';
    }

}

function remove(quantity){

    const re = /^\d+(\.\d+)*$/;

    if(!re.test(quantity)){
        alert("Invalid quantity. Insert numbers only.");
    }else{
        document.getElementById('removeAsset').style.display = 'none';
        document.getElementById('netWorth').style.display = 'block';
    }

}

function cancel(current){
    document.getElementById(current).style.display = 'none';
    document.getElementById('netWorth').style.display = 'block';
}