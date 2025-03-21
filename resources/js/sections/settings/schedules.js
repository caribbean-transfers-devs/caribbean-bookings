let schedules = {

    calendarFilter: function(selector, options = {}){
        const defaultConfig = {
            mode: "single",
            locale: "es", // Idioma dinámico
            enableTime: false, // Activamos o Desactivamos la selección de hora
            // noCalendar: true, // Ocultamos la selección de fecha
            dateFormat: "Y-m-d", // Formato por defecto
            altInput: true, // Input visual más amigable
            altFormat: "j F Y", // Formato más legible
            allowInput: true,
            // altFormat: "h:i K", // Formato 12h con AM/PM
            defaultDate: "today",
            minDate: "today",
            plugins: [] // Aseguramos que sea un array
        };
    
        let config = { ...defaultConfig, ...options };
    
        const fp = flatpickr(selector, config);
        return fp;
    },        

}
if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}
components.formReset();

const __date_schedule = document.getElementById('date_schedule');
const __check_in = document.getElementById('check_in_time');
const __check_out = document.getElementById('check_out_time');

document.addEventListener("DOMContentLoaded", function() {
    if( __date_schedule ){
        schedules.calendarFilter(__date_schedule, { mode: "single", minDate: null });
    }

    if( __check_in ){
        schedules.calendarFilter(__check_in, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: "12:00", minDate: null });
    }

    if( __check_out ){
        schedules.calendarFilter(__check_out, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: "12:00", minDate: null });
    }
});