if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}
components.formReset();

document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') {                
        location.reload();
    }
});

let timeoutId;
function resetTimer() {
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
    
    timeoutId = setTimeout(updateView, 300000); // 60,000 ms = 1 minuto
}

function updateView() {
    console.log('La vista se ha actualizado...');
    location.reload();  
}

document.addEventListener('mousemove', resetTimer);
document.addEventListener('keydown', resetTimer);
resetTimer();