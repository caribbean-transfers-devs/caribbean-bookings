let filters = {
    checked: function(checked, type){
        if (checked.checked) {
            checked.value = "1";
            // console.log("Checkbox " + type + ". Nuevo valor:", checked.value);
        } else {
            checked.value = "0";
            // console.log("Checkbox " + type + ". Nuevo valor:", checked.value);
        }
    },
}

const __is_today = document.getElementById('is_today');
const __is_duplicated = document.getElementById('is_duplicated');
const __is_agency = document.getElementById('is_agency');

if( __is_today != null ){
    filters.checked(__is_today, 'today');
    __is_today.addEventListener('change', function(){
        filters.checked(this, 'today');
    });
}

if( __is_duplicated != null ){
    filters.checked(__is_duplicated, 'duplicated');
    __is_duplicated.addEventListener('change', function(){
        filters.checked(this, 'duplicated');
    });
}

if( __is_agency != null ){
    filters.checked(__is_agency, 'agency');
    __is_agency.addEventListener('change', function(){
        filters.checked(this, 'agency');
    });
}
