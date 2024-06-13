const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
const language = document.documentElement.lang;
const _LOCAL_URL = window.location.origin;
const __loading = document.getElementById('content');
const __load_screen = document.getElementById("load_screen");
const __btn_filter = document.querySelector('.btn-filter');
const translations = {
    en: {
        "table.results": "Results",
        "table.search": "Search",
        "table.pagination": "Showing page",
        "table.of": "of"
    },
    es: {
        "table.results": "Resultados",
        "table.search": "Buscar",
        "table.pagination": "Mostrando pagina",
        "table.of": "de"
    }
};

let components = {
    getTranslation: function(item){
        return (translations[language][item]) ? translations[language][item] : 'Translate not found';
    },

    /**
     * ===== Render Table Settings ===== *
     * @param {*} table //tabla a renderizar
    */
    actionTable: function(table){
        let buttons = [];
        const _settings = {},
            _buttons = table.data('button');

        if( _buttons != undefined && _buttons.length > 0 ){
            // _buttons.forEach(_btn => {
            //     buttons.push(_btn);
            // });

            console.log(_buttons);
            $.each(_buttons, function(index, button) {
                console.log(button);
                const __params = new Object();
                ( button.hasOwnProperty('extend') ? __params.extend = button.extend : "" );
                ( button.hasOwnProperty('text') ? __params.text = button.text : "" );
                ( button.hasOwnProperty('className') ? __params.className = button.className : "" );
                ( button.hasOwnProperty('titleAttr') ? __params.titleAttr = button.titleAttr : "" );
                ( button.hasOwnProperty('attr') ? __params.attr = button.attr : "" );
                if( button.hasOwnProperty('url') ){
                    __params.action = function(e, dt, node, config){
                        window.location.href = button.url;
                    }
                };
            });
        }

        console.log(buttons);

        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                        <'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;                        
        _settings.deferRender = true;
        _settings.responsive = true;
        _settings.buttons =  _buttons;        
        _settings.order = [[ 0, "DESC" ]];
        _settings.lengthMenu = [10, 20, 50];
        _settings.pageLength = 10;                
        _settings.oLanguage = {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": this.getTranslation("table.pagination") + " _PAGE_ " + this.getTranslation("table.of") + " _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": this.getTranslation("table.search") + "...",
            "sLengthMenu": this.getTranslation("table.results") + " :  _MENU_",
        };

        table.DataTable( _settings );
    },

    formReset: function(){
        const __closes = document.querySelectorAll('.__close');
        if( __closes.length > 0 ){
            __closes.forEach(__close => {
                __close.addEventListener('click', function (event) {
                    const __form = this.parentNode.parentNode;
                    __form.reset();
                });
            });
        }
    },

    /**
     * 
     * @param {*} _this 
     * @returns 
     */
    serialize: function(_this = null, _type = "array"){
        let _params = ( _type == "object" ? {} : [] );
        if( _this != null ){
            for (let i = 0; i < _this.elements.length; i++) {
                    let element = _this.elements[i];
                    if (element.name) {
                        _params[element.name] = element.value;
                    }
            }
        }
        return _params;
    },

    /**
     *
     * @param {*} url // url where we request response
     * @param {*} method // method with which we process the request
     * @param {*} data // Object data form
     * @returns
     */
    request_exec_fetch: async function (url, method, _params = "") {
        try {
            let _headers = new Object();
            _headers.method = method;
            if( method == "POST" || method == "PUT" || method == "DELETE" ){
                _headers.headers = { 
                    'Content-Type': 'application/json; charset=utf-8',
                    'X-CSRF-TOKEN': csrfToken
                };
                _headers.body = JSON.stringify(_params);
            }else{ url += ( _params != "" ? "?" + components.objectParams(_params) : "" ); }
            const response = await fetch(url, _headers);
            return await response.json();
        } catch (error) {
            components.sweetAlert({"status": "error", "message": error});
        }
    },

    request_exec_ajax: function(url, method, _params){
        $.ajax({
            type: method, // Método HTTP de la solicitud
            url: url, // Ruta del archivo PHP que manejará la solicitud
            data: JSON.stringify(_params), // Datos a enviar al servidor
            dataType: "json", // Tipo de datos que se espera en la respuesta del servidor
            contentType: 'application/json; charset=utf-8',
            beforeSend: function(){
                components.loadScreen();
            },
            success: function(response) {
                // Manejar la respuesta exitosa del servidor
                components.proccessResponse(response);
            }
        });
    },

    /**
     *
     * @param {*} response
     */
    proccessResponse: function (response){
        console.log(response);
        var _response = new Object();
        _response = response;
        _response.reload = (response.hasOwnProperty('reload') ? response.reload : true );
        _response.status = ( response.hasOwnProperty('success') && response.success ? ( response.success ? 'success' : 'error' ) : ( response.hasOwnProperty('status') ? response.status : ( response.hasOwnProperty('code') ? response.code : 'error' ) ) );
        _response.message = ( _response.status == "error" && response.hasOwnProperty('errors') && response.errors != null ? components.getStringErrors(response.errors) : response.message );
        if( response.hasOwnProperty('return') && response.return && response.hasOwnProperty('link_return') && response.link_return != "" ){
            _response.link_return = response.link_return;
        }
        console.log(response);
        components.sweetAlert(_response);
    },

    /**
     *
     * @param {*} errors
     * @returns
     */
    getStringErrors: function (errors){
        var err_messages = '';
        for (let index in errors) {
            let _msg = errors[index];
            let _index = index.replaceAll("_"," ");
            _index = _index.substring(0,1).toUpperCase() + _index.substring(1);
            _index = '<strong>'+ _index +'</strong>';
            if(_msg.includes(index)){
                _msg = _msg.replace(index,_index);
            }else {
                _msg = _index +', '+ _msg;
            }
            err_messages += _msg + "<br>";
        }
        return err_messages;
    },

    sweetAlert:function (_response){
        var _settings = {
            icon: _response.status,
            html: _response.message,
        };
        ( _response.hasOwnProperty('timer') && _response.timer != "" ? _settings.timer = _response.timer : "" );
        Swal.fire(_settings).then((result) => {
            if( _response.hasOwnProperty('link_return') && _response.link_return != "" ){// AQUI VALIDAMOS QUE EL RESPONSE CONTENGA UNA URL DE RETORNO
                window.location.href = _response.link_return
            }else if( _response.hasOwnProperty('reload') && _response.reload ){
                location.reload();
            }
        })
    },
    
    /**
     *
     * @param {*} object
     */
    objectParams: function(object){
        let params = [];
        components.setParams(params, object);
        return params.join('&');
    },

    /**
     *
     * @param {*} params
     * @param {*} object
     */
    setParams: function(params, object, name = ''){
        if (name && components.isAValue(object)) {
            params.push([name, object + ''].map(encodeURIComponent).join('='));
        } else if (name && Array.isArray(object)) {
            object.map((val, index) => {
                components.setParams(params, val, name + '[' + (components.isAValue(val) ? '' : index) + ']');
            });
        } else if (typeof object === 'object') {
            Object.keys(object).map(prop => {
                components.setParams(params, object[prop], name ? name + '[' + prop + ']' : prop);
            })
        }
    },

    /**
     *
     * @param {*} value
     * @returns
     */
    isAValue: function(value) {
        return typeof value === 'number' ||
        typeof value === 'string' ||
        value instanceof Date ||
        value instanceof Boolean ||
        value === null;
    },    

    /**
     * global loading method
     * @param {*} options //options object
     */
    waitMe: function(options){
        var settings = {
            effect: 'bounce',
            container: 'body',
            message: this.getTranslation('loading.message'),
            onClose: function(){},
            callback: function(){}
        };

        $.extend(settings, options);

        if(typeof options.event == 'string'){
            $(settings.container).waitMe(options.event);
        } else {
            $(settings.container).waitMe({
                effect : settings.effect,
                text : settings.message,
                color : '#00a395',
                maxSize : '',
                waitTime : -1,
                textPos : 'vertical',
                fontSize : '',
                source : '',
                onClose : settings.onClose
            });
        }
    },

    waitMeHide: function(_container = null){
        _container = ( _container != null ? _container : 'body' );
        components.waitMe({
            container: _container,
            event: 'hide'
        })
    },

    loadScreen: function(){
        // let __body = document.querySelector('body');
        // let __div = document.createElement("div");
        // __div.setAttribute('id', 'load_screen');
        // __div.innerHTML = '<div class="loader"> <div class="loader-content"><img src="/assets/img/logos/brand_white.png" alt="loading" class="img-fluid" width="220"><img src="/assets/img/loader.gif" alt="loading"><p class="text-white mb-0" style="font-size: 16px;">' + this.getTranslation('loading.message') + '</p></div></div>';
        // __body.insertBefore(__div, __body.firstChild);
        __load_screen.classList.remove('d-none');
    },

    removeLoadScreen(){
        // let __load_screen = document.getElementById("load_screen");
        if( __load_screen != null ){
            // document.body.removeChild(load_screen);
            __load_screen.classList.add('d-none');
        }
    }    
}

/**
 * se utiliza para ejecutar código cuando el usuario está a punto de abandonar una página web. Esto incluye acciones como cerrar la ventana del navegador, 
 * recargar la página o navegar a una página diferente. La propiedad beforeunload es un evento que se puede capturar y manipular para proporcionar al usuario 
 * una advertencia o una confirmación antes de permitir que abandonen la página.
 */
window.addEventListener("beforeunload", function(event) {
    // components.loadScreen();
});

// Mostrar el indicador de carga cuando se navega hacia atrás o hacia adelante
window.addEventListener('popstate', function (event) {
    console.log("popstate");
    // components.removeLoadScreen();
    // components.loadScreen();
    // components.removeLoadScreen();
});

window.addEventListener("DOMContentLoaded", function() {

    console.log("The page has fully loaded.");
    //OCULTAMOS LOADING CUANDO DOM ESTA CARGADO COMPLETAMENTE
    components.removeLoadScreen();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        complete : function(xhr, status) {
            components.removeLoadScreen();
        },
        error : function(xhr, status, error) {
            console.log(xhr, status, error);
            components.sweetAlert({"status": "error", "message": xhr.responseJSON.message});
        },
    });
    
    if( __btn_filter != null ){
        __btn_filter.addEventListener('click', function(){
            components.loadScreen();
        });
    }

});