
    const __clearBooking = document.getElementById('clearBooking');
    var round_trip_element = document.getElementById('aff-round-trip-element');
    var from_autocomplete = document.getElementById('aff-input-from');
    var to_autocomplete = document.getElementById('aff-input-to');
    var from_autocomplete_update = document.getElementById('aff-input-to-from');
    var to_autocomplete_update = document.getElementById('aff-input-to-to');
    var passengers = document.getElementById('aff-input-passengers');
    var passengers_update = document.getElementById('aff-input-to-passengers');

    var date_init = document.getElementById('aff-input-pickup-date');
    var date_end = document.getElementById('aff-input-to-pickup-date');

    var time_init = document.getElementById('aff-input-pickup-time');
    var time_end = document.getElementById('aff-input-to-pickup-time');

    var button = document.getElementById('aff-button-send');
    var errors = document.getElementById('aff-error-list');
    var affiliate = document.getElementById('aff-affiliate-id');
    var channel = document.getElementById('aff-channel');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var button_one = document.getElementById('btn_make_one');
    var button_two = document.getElementById('btn_make_two');
    const language = document.documentElement.lang;    

    const itemsTranslation = {
        en: {
            "quote.url": "/en/results",
            "quote.from.input": "Please, enter the pickup location",
            "quote.from.date": "Please, enter your pickup date",
            "quote.to.input": "Please, enter the destination",
            "quote.to.date": "Please, enter your return date",
            "checkout.title": "Processing your request",
            "checkout.html": "Please wait a moment while we process your request",
        },
        es: {
            "quote.url": "/resultados",
            "quote.from.input": "Por favor, introduzca el lugar de recogida",
            "quote.from.date": "Por favor, introduzca su fecha de recogida",
            "quote.to.input": "Por favor, introduzca el destino",
            "quote.to.date": "Por favor, introduzca su fecha de regreso",
            "checkout.title": "Procesando su solicitud",
            "checkout.html": "Por favor espere un momento mientras procesamos su solicitud",
        }
    };



    let setup = {        
        lang: language,
        currency: 'USD',
        deeplink: '/resultados',
        serviceType: 'OW',
        pax: 1,
        vehicle: 1,
        items: {
            from: {
                name: '',
                latitude: '',
                longitude: '',
                pickupDate: '',
                pickupTime: '00:00',
            },
            to: {
                name: '',
                latitude: '',
                longitude: '',
                pickupDate: '',
                pickupTime: '00:00',
            },
        },
        setCurrency: function(currency){
            currency = currency.toUpperCase();
            const allowedCurrency = ["USD", "MXN"];
            if( allowedCurrency.includes(currency) ){
                this.currency = currency;
            }            
        },
        setServiceType: function(serviceType){
            serviceType = serviceType.toUpperCase();
            const allowedServices = ["OW", "RT"];
            if( allowedServices.includes(serviceType) ){
                this.serviceType = serviceType;
            }  
        },
        setPax: function(pax){
            setup.pax = pax;
            passengers_update.value = pax;
        },
        setItem(element, data = {}){
            const finalElement = document.getElementById(element);
            finalElement.innerHTML = '';

            if(element === "aff-input-from-elements"){
                const initInput = document.getElementById('aff-input-from');
                initInput.value = data.name;
                setup.items.from.name = data.name;
                setup.items.from.latitude = data.geo.lat;
                setup.items.from.longitude = data.geo.lng;

                to_autocomplete_update.value = data.name;                
            }

            if(element === "aff-input-to-elements"){
                const initInput = document.getElementById('aff-input-to');
                initInput.value = data.name;
                setup.items.to.name = data.name;
                setup.items.to.latitude = data.geo.lat;
                setup.items.to.longitude = data.geo.lng;

                from_autocomplete_update.value = data.name;
            }            
        },
        setTime(element, time){
            if(element === "init"){
                setup.items.from.pickupTime = time;
            }
            if(element === "end"){
                setup.items.to.pickupTime = time;
            }            
        },
        setVehicle(id){
            this.vehicle = id;
        },
        autocomplete: function(keyword, element){
            let size = keyword.length;
            if(size < 3) return false;

            setup.loadingMessage(element);
            
            fetch(`/tpv2/autocomplete`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ keyword:keyword })
            }).then((response) => {
                return response.json()
            }).then((data) => {
                this.makeItems(data,element);
            }).catch((error) => {
                console.error('Error:', error);
            });
        },
        makeItems: function(data, element){
            const finalElement = document.getElementById(element);
            finalElement.innerHTML = '';

            data = data.items;

            for (let key in data) {
                if (data.hasOwnProperty(key)) {

                    const itemDiv = document.createElement('div');
                    itemDiv.textContent = data[key].name;

                    const span = document.createElement('span');
                    span.textContent = data[key].address;

                    itemDiv.appendChild(span);

                    itemDiv.addEventListener('click', function() { 
                        setup.setItem(element, data[key]);
                    });

                    finalElement.appendChild(itemDiv);
                }
            }            
        },
        loadingMessage: function(item){

            const loader = document.getElementById(item);
            loader.innerHTML = '';
            
            const div = document.createElement('div');
            div.classList.add("loader");
            const image = document.createElement('img');
            image.src = '/assets/img/loader.gif';
            
            div.appendChild(image);
            loader.appendChild(div);

        },
        errorMessage: function(){
            const loader = document.getElementById(item);
            loader.textContent = 'Error';
        },
        getTranslation: function(item){
            return (itemsTranslation[setup.lang][item]) ? itemsTranslation[setup.lang][item] : 'Translate not found';
        },
        hightlight: function(element, text){
            element.classList.add("hightlight");
            errors.textContent = text;
        },
        clearErrors: function(){
            from_autocomplete.classList.remove("hightlight");
            to_autocomplete.classList.remove("hightlight");
            date_init.classList.remove("hightlight");
            date_end.classList.remove("hightlight");
            errors.textContent = '';
        },
        init: function(){
            
            var data = JSON.parse(localStorage.getItem('bookingbox'));
            if(data){
                setup.setCurrency(data.currency);
                var btnCurrency = document.querySelector(`.aff-toggle-currency[data-currency="${data.currency}"]`);
                if (btnCurrency) {
                    btnCurrency.click();
                }

                setup.setServiceType(data.type);
                var btnType = document.querySelector(`.aff-toggle-type[data-type="${data.type}"]`);
                if (btnType) {
                    btnType.click();
                }
                
                from_autocomplete.value = data.from.name;
                setup.items.from.name = data.from.name;
                setup.items.from.latitude = data.from.lat;
                setup.items.from.longitude = data.from.lng;
                to_autocomplete_update.value = data.from.name;
                
                setup.items.from.pickupDate = data.from.pickupDate;
                date_init.value = data.from.pickupDate;

                setup.items.from.pickupTime = data.from.pickupTime;
                time_init.value = data.from.pickupTime;

                to_autocomplete.value = data.to.name;
                setup.items.to.name = data.to.name;
                setup.items.to.latitude = data.to.lat;
                setup.items.to.longitude = data.to.lng;
                from_autocomplete_update.value = data.to.name;

                if(data.type == "RT"){
                    setup.items.to.pickupDate = data.to.pickupDate;
                    date_end.value = data.to.pickupDate;
                    setup.items.to.pickupTime = data.to.pickupTime;
                    time_end.value = data.to.pickupTime;
                }

                setup.setPax(data.passengers);
                passengers.value = data.passengers;
                passengers_update.value = data.passengers;
            }

        },
        validate: function(){
            setup.clearErrors();
            
            //Validate from data
            if(!this.items.from.name || this.items.from.name.trim() === '' || !this.items.from.latitude || this.items.from.latitude.toString().trim() === '' || !this.items.from.longitude || this.items.from.longitude.toString().trim() === ''){
                setup.hightlight(from_autocomplete, this.getTranslation("quote.from.input"));
                return false;
            }

            //Validate to data
            if(!this.items.to.name || this.items.to.name.trim() === '' || !this.items.to.latitude || this.items.to.latitude.toString().trim() === '' || !this.items.to.longitude || this.items.to.longitude.toString().trim() === ''){
                setup.hightlight(to_autocomplete, this.getTranslation("quote.to.input"));
                return false;
            }

            //Validate date data
            if(!this.items.from.pickupDate || this.items.from.pickupDate.toString().trim() === ''){
                setup.hightlight(date_init, this.getTranslation("quote.from.date"));
                return false;
            }

            if(this.serviceType === "RT"){
                //Validate date data
                if(!this.items.to.pickupDate || this.items.to.pickupDate.toString().trim() === ''){
                    setup.hightlight(date_end, this.getTranslation("quote.to.date"));
                    return false;
                }
            }

            button.disabled = true;
            button.classList.add('loading');
            
            var data = {
                type: setup.serviceType,
                currency: setup.currency,
                language: setup.lang,
                passengers: setup.pax,
                from: {
                    name: setup.items.from.name,
                    lat: setup.items.from.latitude,
                    lng: setup.items.from.longitude,
                    pickupDate: setup.items.from.pickupDate,
                    pickupTime: setup.items.from.pickupTime
                },
                to: {
                    name: setup.items.to.name,
                    lat: setup.items.to.latitude,
                    lng: setup.items.to.longitude,
                    pickupDate: setup.items.to.pickupDate,
                    pickupTime: setup.items.to.pickupTime
                }
            };
            
            localStorage.setItem('bookingbox', JSON.stringify(data));

            data.from.pickupDate = setup.items.from.pickupDate + ' ' + setup.items.from.pickupTime;
            data.to.pickupDate = (setup.items.to.pickupDate || setup.items.from.pickupDate) + ' ' + setup.items.to.pickupTime;

            fetch(`/tpv2/quote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(data)
            })
            .then((response) => {
                if( response.status === 200 ){
                    return Promise.all([response.status, response.text()]);
                }else{                   
                    return Promise.all([response.status, response.json() ]);
                }
            })
            .then(([status, data]) => {
                button.disabled = false;
                button.classList.remove('loading');

                if(status !== 200){
                    Swal.fire({
                        icon: "error",
                        title: '¡ERROR!',
                        html: data.error.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return false;
                }

                toggleActive('two');

                const divResultado = document.getElementById('two-elements');
                divResultado.innerHTML = data;
                
                attachEventListeners();
            })
            .catch((error) => {
                console.log(error);
            });

        },
        getCheckout: function(){
            var data = {
                id: setup.vehicle,
                type: setup.serviceType,
                currency: setup.currency,
                language: setup.lang,
                passengers: setup.pax,
                from: {
                    name: setup.items.from.name,
                    lat: setup.items.from.latitude,
                    lng: setup.items.from.longitude,
                    pickupDate: setup.items.from.pickupDate,
                    pickupTime: setup.items.from.pickupTime
                },
                to: {
                    name: setup.items.to.name,
                    lat: setup.items.to.latitude,
                    lng: setup.items.to.longitude,
                    pickupDate: setup.items.to.pickupDate,
                    pickupTime: setup.items.to.pickupTime
                }
            };
            
            localStorage.setItem('bookingbox', JSON.stringify(data));

            data.from.pickupDate = setup.items.from.pickupDate + ' ' + setup.items.from.pickupTime;
            data.to.pickupDate = (setup.items.to.pickupDate || setup.items.from.pickupDate) + ' ' + setup.items.to.pickupTime;

            var returnBtn = document.getElementById("returnBtn");
            if(data.type == "OW"){
                returnBtn.style.display = "none";
            }else{
                returnBtn.style.display = "block";
            }

            fetch(`/tpv2/re-quote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(data)
            })
            .then((response) => {
                return Promise.all([response.status, response.json() ]);
            })
            .then(([status, data]) => {

                if(status !== 200){
                    Swal.fire({
                        icon: "error",
                        title: '¡ERROR!',
                        html: data.error.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return false;
                }

                document.getElementById('fill-vehicle-token').value = data.items.token;
                document.getElementById('fill-vehicle-name').textContent = data.items.name;
                document.getElementById('fill-vehicle-image').src = data.items.image;
                document.getElementById('fill-vehicle-price').textContent = data.items.price + " " + data.items.currency;
                document.getElementById('fill-passengers').textContent = data.items.passengers;
                document.getElementById('fill-suitcase').textContent = data.items.luggage;
                document.getElementById('fill-from-name').textContent = data.places.one_way.init.name;
                document.getElementById('fill-to-name').textContent = data.places.one_way.end.name;

                var init_date = data.places.one_way.init.time.split(' ');
                document.getElementById('fill-from-date').textContent = init_date[0];
                document.getElementById('fill-from-time').textContent = init_date[1];

                if( setup.serviceType === "RT"){
                    document.getElementById('fill-return-from-name').textContent = data.places.round_trip.init.name;
                    document.getElementById('fill-return-to-name').textContent = data.places.round_trip.init.name;
                    var end_date = data.places.round_trip.init.time.split(' ');
                    document.getElementById('fill-return-to-date').textContent = end_date[0];
                    document.getElementById('fill-return-to-time').textContent = end_date[1];
                }

                var div_flight = document.getElementById('fill-flight-information');
                if( data.places.config.flight_required ){
                    div_flight.style.display = "block";
                }else{
                    div_flight.style.display = "none";
                }
                
            })
            .catch((error) => {
                console.log(error);
            });

            return data;
        }
    };

    __clearBooking.addEventListener('click', function(e){
        e.preventDefault();
        if( localStorage.getItem('bookingbox') !== null && localStorage.getItem('bookingbox') !== "" ){
            localStorage.removeItem('bookingbox');
            location.reload();
        }else{
            console.log('localstorage not found');
        }
    })

    const buttons = document.querySelectorAll('.aff-toggle-currency');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const dataType = this.getAttribute('data-currency');
            setup.setCurrency(dataType);

            buttons.forEach(btn => {
                btn.classList.remove('active');
            });

            this.classList.add('active');
        });
    });

    const serviceTypeButtons = document.querySelectorAll('.aff-toggle-type');
    serviceTypeButtons.forEach(button =>{
        button.addEventListener('click', function(){
            const dataType = this.getAttribute('data-type');
            setup.setServiceType(dataType);

            if(dataType == "RT"){
                round_trip_element.classList.remove("hidden");
            }else{
                round_trip_element.classList.add("hidden");
            }

            serviceTypeButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    function affDelayAutocomplete(callback, ms) {
        var timer = 0;
        return function () {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
   
    from_autocomplete.addEventListener('keydown', affDelayAutocomplete(function (e) {
        setup.autocomplete( e.target.value, 'aff-input-from-elements');
    }, 500));
    from_autocomplete.addEventListener('focus', (e) => {
        setup.autocomplete( e.target.value, 'aff-input-from-elements');
    });
    
    to_autocomplete.addEventListener('keydown', affDelayAutocomplete(function (e) {
        setup.autocomplete( e.target.value, 'aff-input-to-elements');
    }, 500));
    to_autocomplete.addEventListener('focus', (e) => {
        setup.autocomplete( e.target.value, 'aff-input-to-elements');
    });
    
    passengers.addEventListener('change', function(){
        setup.setPax(this.value);
    });

    time_init.addEventListener('change', function(){
        setup.setTime('init', this.value);
    });
    time_end.addEventListener('change', function(){
        setup.setTime('end', this.value);
    });
    
    date_init.addEventListener('change', function(){
        setup.items.from.pickupDate = this.value;
        
        if( setup.serviceType === "OW" ){
            date_end.value = this.value;
            setup.items.to.pickupDate = this.value;
        }
        if( setup.serviceType === "RT" ){
            if( date_end.value < this.value ){
                date_end.value = this.value;
                setup.items.to.pickupDate = this.value;             
            }
        }    
    });

    date_end.addEventListener('change', function(){
        setup.items.to.pickupDate = this.value;

        if(date_init.value > this.value){
            date_init.value = this.value;
            setup.items.from.pickupDate = this.value;
        }
    });
    
    button.addEventListener('click', function(e){
        e.preventDefault();
        setup.validate();
    });

    // Función que cambia de paso
    function toggleActive(id) {
        var elements = [
            document.getElementById('one'),
            document.getElementById('two'),
            document.getElementById('three')
        ];
        elements.forEach(function(element) {
            if (element.id === id) {
                element.classList.add('active');
            } else {
                element.classList.remove('active');
            }
        });
    }

    // Eventos de los botones que cambian de paso hacia adelante o hacia atrás
    var goButtons = document.querySelectorAll('.go');
    goButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            var id = button.getAttribute('data-id');
            toggleActive(id);
        });
    });

    // Eventos de los botones de vehículos que envían al checkout
    function attachEventListeners() {
        var buttonsItems = document.querySelectorAll('.btn-item');
        buttonsItems.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                setup.setVehicle( button.getAttribute('data-id') );
                toggleActive("three");
                setup.getCheckout();
            });
        });
    }

    // Botones de ida y vuelta 
    var arrivalBtn = document.getElementById("arrivalBtn");
    var returnBtn = document.getElementById("returnBtn");
    var divOne = document.querySelector(".journey .bottom .one");
    var divTwo = document.querySelector(".journey .bottom .two");

    if(arrivalBtn){
        arrivalBtn.addEventListener("click", function () {
            arrivalBtn.classList.add("active");
            returnBtn.classList.remove("active");
            divOne.style.display = "flex";
            divTwo.style.display = "none";
        });
    }    

    if(returnBtn){
        returnBtn.addEventListener("click", function () {
            arrivalBtn.classList.remove("active");
            returnBtn.classList.add("active");
            divOne.style.display = "none";
            divTwo.style.display = "flex";
        });
    }

    setup.init();
    //setup.getCheckout();

//Validación del formulario
var btn = document.getElementById('btn_send');
let formData = {
    flight_number: document.getElementsByName('flight_number')[0],
    first_name: document.getElementsByName('first_name')[0],
    last_name: document.getElementsByName('last_name')[0],
    email: document.getElementsByName('email')[0],
    phone_input: document.getElementsByName('phone_input')[0],
    special_request: document.getElementsByName('special_request')[0],
};

function handler(){
    deleteMessages();

    let data = {};
    Object.keys(formData).forEach(function(key) {
        if(formData[key]){
            data[key] = formData[key].value;
        }
    });
      
    let rules = {
        flight_number: 'required|min:2|max:35',
        first_name: 'required|min:2|max:45',
        last_name: 'max:45',
        email: 'required|email|max:45',
        phone_input: 'required|max:25',
        special_request: 'max:150',
    }

    if(setup.serviceType == "OW"){
        delete rules.flight_number;
    }else{
        rules.flight_number = 'required|min:2|max:35';        
    }

    var messages = {
        required: 'This field is required',
        min: 'Min :min characters',
        max: 'Max :max characters',
        email: 'Incorrect E-mail'
    };

    let validation = new Validator(data, rules, messages);    
    if (validation.passes()) {
        button_one.disabled = true;
        button_one.classList.add('loading')
        button_two.disabled = true;
        button_two.classList.add('loading')

        var form = document.getElementById("checkoutForm");
        var dataToSend = new FormData(form);

        fetch( form.getAttribute("action") , {
            method: "POST",
            body: dataToSend
        })
        .then((response) => {
            return Promise.all([response.status, response.json() ]);
        })
        .then(([status, data]) => {
            if(status !== 200){
                button_one.disabled = false;
                button_one.classList.remove('loading')
                button_two.disabled = false;
                button_two.classList.remove('loading')
                Swal.fire({
                    icon: "error",
                    title: '¡ERROR!',
                    html: data.error.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                return false;
            }

            let timerInterval;
            Swal.fire({
            title: setup.getTranslation("checkout.title"),
            html: setup.getTranslation("checkout.html"),
            timer: 3000,
            timerProgressBar: true,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    // console.log("I was closed by the timer");
                    if( data.hasOwnProperty('link') && data.link != "" ){
                        window.location = data.link;   
                    }
                }
            });
                        
            // console.log(data);
        })
        .catch(error => {
            console.log(error);
        });

        return false;

        // var form = document.getElementById("checkoutForm");                
        // form.submit();
    } else {
        console.log(validation.errors.errors);
        Object.keys(validation.errors.errors).forEach(function(key) {  
            let span = document.createElement('span');
                span.classList.add("error");
                span.textContent = validation.errors.errors[key];
                if(formData[key]){
                    formData[key].parentNode.insertBefore(span, formData[key].nextSibling);
                }
        });        
        return false;
    }
}

function deleteMessages() {
    Object.keys(formData).forEach(function(key) {
        if(formData[key]){
            let after_span = formData[key].nextSibling;
            if (after_span && after_span.nodeName === 'SPAN') {
                after_span.remove();
            }
        }
    });
}

//Funcionalidad para agregar el prefijo del país al número de teléfono
var input = document.querySelector("#phone");
var iti = window.intlTelInput(input, {
    initialCountry: "us",
    preferredCountries: ["us","ca", "mx"],
    separateDialCode: true,
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});
input.addEventListener("input", function() {
    document.getElementById('fill-phone').value = iti.getNumber();    
});
input.addEventListener("countrychange", function() {
    document.getElementById('fill-phone').value = iti.getNumber();
});