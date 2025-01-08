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

var __table_render = null;

let components = {
    getTranslation: function(item){
        return (translations[language][item]) ? translations[language][item] : 'Translate not found';
    },

    /**
     * ===== Render Table Settings ===== *
     * @param {*} table //tabla a renderizar
    */
    actionTable: function(table, action = ""){
        let buttons = [];
        const _settings = {},
            _buttons = table.data('button');

        if( _buttons != undefined && _buttons.length > 0 ){
            _buttons.forEach(_btn => {
                if( _btn.hasOwnProperty('url') ){
                    _btn.action = function(e, dt, node, config){
                        window.location.href = _btn.url;
                    }
                };
                buttons.push(_btn);
            });
        }
        // console.log(buttons);

        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-12 col-lg-8 d-flex flex-column flex-sm-row justify-content-sm-start justify-content-center'l<'dt--pages-count align-self-center'i><'dt-action-buttons align-self-center ms-3 ms-lg-3'B>><'col-12 col-sm-12 col-lg-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                        <'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;                        
        _settings.deferRender = true;
        _settings.responsive = false; // La tabla sigue siendo responsive
        _settings.buttons =  _buttons;
        _settings.order = [];
        if( action == "fixedheaderPagination" ){
            _settings.paging = true; // Aseguramos que la paginación esté activada
            _settings.pageLength = 100; // Muestra 100 elementos por página por defecto
            _settings.lengthChange = false; // Quita el selector de "mostrar X elementos por página"
        }else{
            _settings.paging = false;
        }
        // _settings.stateSave = false;

        if( action == "fixedheader" || action == "fixedheaderPagination" ){
            _settings.fixedHeader = true; // Deshabilita FixedHeader si estaba habilitado
            _settings.scrollX = true;     // Mantén el scroll horizontal si es necesario
        }

        _settings.oLanguage = {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            // "sZeroRecords": "",
            // "sInfo": ( action == "fixedheaderPagination" ? "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros" : "Mostrando _TOTAL_ registros" ),
            // "sInfo": function(){
            //     const total = table.data().count(); // Verifica el total de registros en la tabla
            //     return total > 0 ? "Mostrando _TOTAL_ registros" : "Mostrando 0 registros";
            // },
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": components.getTranslation("table.search") + "...",
            "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
            "oPaginate": { 
                "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
            },
        };
        _settings.infoCallback = function(settings, start, end, max, total, pre) {
            // Siempre muestra "Mostrando _TOTAL_ registros" independientemente del total
            return `Mostrando ${total} registros`;
        };

        __table_render = table.DataTable( _settings );        

        if( action == "fixedheader" || action == "fixedheaderPagination" ){
            // Ajustar encabezado fijo al scroll dentro del contenedor
            // new $.fn.dataTable.FixedHeader(__table_render, {
            //     header: true, // Habilita encabezado fijo
            //     footer: false // Opcional: deshabilitar footer fijo si no lo necesitas
            // });

            // Corrige el ancho al inicializar
            table.on('init', function () {
                __table_render.columns.adjust().draw();
            });

            table.on('draw', function () {
                __table_render.columns.adjust();
            });
        }        
    },

    actionTableChart: function(table, section = "general"){
        const _settings = {};

        _settings.dom = `<'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;
        _settings.deferRender = true;
        _settings.responsive = false;
        if (section == "driver") {
            _settings.order = [[3, 'desc']];
            _settings.scrollX = true;     // Mantén el scroll horizontal si es necesario
        }else if(section == "commissions"){
            _settings.order = [[1, 'desc']];
        }else{
            _settings.order = [[2, 'desc']];
        }
        _settings.paging = false; // Si no quieres paginación, puedes dejar esto en false
        _settings.oLanguage = {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sInfo": "", // Oculta el número de registros mostrados
            "sInfoFiltered": "", // Oculta el texto filtrado
            "sSearch": '', // No muestra el campo de búsqueda
            "sSearchPlaceholder": "",
            "sLengthMenu": "", // Oculta el menú de cantidad de resultados por página
            "oPaginate": { 
                "sPrevious": '', // No muestra el botón de anterior
                "sNext": '' // No muestra el botón de siguiente
            },
        };

        let __table = table.DataTable( _settings );

        table.on('init', function () {
            __table.columns.adjust().draw();
        });        
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
     * @param {*} element 
     * @param {*} selector 
     * @returns 
     */
    closest: function(element, selector) {
        // Verificar si el elemento actual cumple con el selector
        if (element.matches(selector)) {
            return element;
        }

        // Recorrer los ancestros del elemento actual
        while (element !== document.body) {
            element = element.parentNode;

            // Verificar si el ancestro cumple con el selector
            if (element && element.matches(selector)) {
                return element;
            }
        }

        // Si no se encuentra ningún ancestro que cumpla con el selector, devolver null
        return null;
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

    /**
     * 
     * @param {*} url 
     * @param {*} method 
     * @param {*} _params 
     */
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
    },

    actionButtonColumns: function(){
        const __btn_columns = document.querySelectorAll('.__btn_columns');
        __btn_columns.forEach(__btn_column => {
            __btn_column.addEventListener('click', function(event){
                event.preventDefault();
                const { table, container } = this.dataset;
                components.renderCheckboxColumns(table, container);
            });
        });
    },

    renderCheckboxColumns: function(table, container){
        const __table = document.getElementById(table);
        // console.log(__table);
        const __container = document.getElementById(container);

        if( __table != null ){
            const __ths = __table.querySelectorAll('th');
            const savedState = JSON.parse(localStorage.getItem(table)) || {};

            __ths.forEach((__th, __key) => {
                const capitalizedText = __th.innerText.toLowerCase().replace(/\b\w/g, function (c) {
                    return c.toUpperCase();
                });
    
                const __div = document.createElement('div');
                const __input = document.createElement('input');
                const __label = document.createElement('label');
    
                __div.classList.add('form-check', 'd-flex', 'align-items-center', 'gap-1', 'mb-0', 'w-100');
                    __div.appendChild(__input);
                    __input.classList.add('form-check-input', 'toggle-vis');
                    __input.setAttribute('type', 'checkbox');
                    __input.setAttribute('value', '');
                    __input.setAttribute('id', 'flexCheckDefault' + __key);
                    __input.setAttribute('data-column', __key);                    
                    __input.checked = savedState[__key] !== undefined ? savedState[__key] : true;
                    __input.setAttribute('checked', __input.checked);
                    __div.appendChild(__label);
                    __label.classList.add('form-check-label', 'w-100', 'mb-0');
                    __label.setAttribute('for', 'flexCheckDefault' + __key);
                    __label.innerText = capitalizedText;
    
                __container.appendChild(__div);
            });

            components.validateColumnVisibility(__table, table);
            components.callActionCheckboxColumns(__table, table);            
        }        
    },

    /**
     * 
     * @param {*} __table //DOM Table
     * @param {*} __storageName  //El nombre del localStorage
     */
    callActionCheckboxColumns: function(__table, __storageName){
        const __DataTable = $(__table).DataTable(); //DOM DataTable
        const __localStorageKey = __storageName; //El nombre del localStorage

        if( document.querySelectorAll('input.toggle-vis').length > 0 ){
            document.querySelectorAll('input.toggle-vis').forEach(function(checkbox) {
                checkbox.addEventListener('change', function(event) {
                    // Evitar la acción por defecto
                    event.preventDefault();
                    const columnVisibility = JSON.parse(localStorage.getItem(__localStorageKey)) || {};
                    let __key = this.getAttribute('data-column');
                    let __column = __DataTable.column(__key);

                    // Alternar la visibilidad de la columna en base al estado del checkbox
                    if (this.checked) {
                        columnVisibility[__key] = true;
                        __column.visible(true);  // Mostrar la columna si el checkbox está marcado
                    } else {
                        columnVisibility[__key] = false;
                        __column.visible(false); // Ocultar la columna si el checkbox no está marcado
                    }
                    
                    localStorage.setItem(__localStorageKey, JSON.stringify(columnVisibility));                    
                });
            });
        }
    },

    validateColumnVisibility: function(__table, __storageName){
        const __DataTable = $(__table).DataTable(); //DOM DataTable
        const localStorageKey = __storageName; //El nombre del localStorage        
        const savedState = JSON.parse(localStorage.getItem(localStorageKey)) || {};
    
        if (__table != null) {
            const __ths = __table.querySelectorAll('th');
            __ths.forEach((__th, __key) => {
                const __column = __DataTable.column(__key);                
                let  __checked = savedState[__key] !== undefined ? savedState[__key] : true;
                if (__checked) {
                    __column.visible(true);
                } else {
                    __column.visible(false);
                }
            });
        }
    },

    setValueSelectpicker: function(){
        const __selectpickers = document.querySelectorAll('.selectpicker');
        if( __selectpickers.length > 0 ){
            __selectpickers.forEach(__selectpicker => {
                const { value } = __selectpicker.dataset;        
                if( value !== undefined ){
                    $("#" + __selectpicker.getAttribute('id')).selectpicker('val', JSON.parse(value));
                }
            });
        }
    }
}

/**
 * se utiliza para ejecutar código cuando el usuario está a punto de abandonar una página web. Esto incluye acciones como cerrar la ventana del navegador, 
 * recargar la página o navegar a una página diferente. La propiedad beforeunload es un evento que se puede capturar y manipular para proporcionar al usuario 
 * una advertencia o una confirmación antes de permitir que abandonen la página.
 */
window.addEventListener("beforeunload", function(event) {
    // console.log(event);
    // components.loadScreen();
});

// Mostrar el indicador de carga cuando se navega hacia atrás o hacia adelante
window.addEventListener('popstate', function (event) {
    // components.removeLoadScreen();
    // components.loadScreen();
    // components.removeLoadScreen();
});

window.onload = function () {
    if (__table_render != null) {
        __table_render.columns.adjust().draw();
    }
};

window.addEventListener("DOMContentLoaded", function() {
    //OCULTAMOS LOADING CUANDO DOM ESTA CARGADO COMPLETAMENTE
    components.removeLoadScreen();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        complete : function(xhr, status) {
            console.log('complete', xhr, status);
            components.removeLoadScreen();
        },
        error : function(xhr, status, error) {
            console.log('error', xhr, status, error);
            let __response = xhr.responseJSON;
            components.sweetAlert({"status": ( __response.hasOwnProperty('status') ? __response.status : 'error' ), "message": __response.message});
        },
    });
    
    if( __btn_filter != null ){
        __btn_filter.addEventListener('click', function(){
            components.loadScreen();
        });
    }

    if (__table_render != null) {
        __table_render.columns.adjust();
        __table_render.columns.adjust().draw();
    }    
});

window.addEventListener('resize', function() {
    if (__table_render != null) {
        __table_render.columns.adjust().draw();        
    }
});