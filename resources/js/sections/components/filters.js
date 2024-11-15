let filters = {
    checked: function(checked, type){
        if (checked.checked) {
            checked.value = "1";
            console.log("Checkbox " + type + ". Nuevo valor:", checked.value);
        } else {
            checked.value = "0";
            console.log("Checkbox " + type + ". Nuevo valor:", checked.value);
        }
    },
}

const __is_today = document.getElementById('is_today');
const __is_duplicated = document.getElementById('is_duplicated');

if( __is_today != null ){
    filters.checked(__is_today, 'today');
    __is_today.addEventListener('change', function(){
        filters.checked(this, 'today');
    });
}

if( __is_duplicated != null ){
    filters.checked(__is_duplicated, 'duplicadas');
    __is_duplicated.addEventListener('change', function(){
        filters.checked(this, 'duplicadas');
    });
}
