let filters = {
    checked: function(checked, type){
        if (checked.checked) {
            checked.value = "1";
        } else {
            checked.value = "0";
        }
    },
    calendarFilter: function(selector, options = {}){
        const defaultConfig = {
            mode: "single",
            locale: "es", // Idioma dinámico
            enableTime: false,
            dateFormat: "Y-m-d", // Formato por defecto
            altInput: true, // Input visual más amigable
            altFormat: "j F Y", // Formato más legible
            allowInput: true,
            defaultDate: "today",
            minDate: "today",
            plugins: [] // Aseguramos que sea un array
        };
    
        let config = { ...defaultConfig, ...options };
    
        const fp = flatpickr(selector, config);
        return fp;
    },
}

const __is_today = document.getElementById('is_today');
const __is_duplicated = document.getElementById('is_duplicated');
const __is_agency = document.getElementById('is_agency');
const __lookup_date = document.getElementById('lookup_date');

document.addEventListener("DOMContentLoaded", function() {
    if( __is_today ){
        filters.checked(__is_today, 'today');
        __is_today.addEventListener('change', function(){
            filters.checked(this, 'today');
        });
    }

    if( __is_duplicated ){
        filters.checked(__is_duplicated, 'duplicated');
        __is_duplicated.addEventListener('change', function(){
            filters.checked(this, 'duplicated');
        });
    }

    if( __is_agency ){
        filters.checked(__is_agency, 'agency');
        __is_agency.addEventListener('change', function(){
            filters.checked(this, 'agency');
        });
    }

    if( __lookup_date ){
        filters.calendarFilter(__lookup_date, { mode: "range", minDate: null });
    }    
});
