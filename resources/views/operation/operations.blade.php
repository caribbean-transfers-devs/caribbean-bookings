@php
    use App\Traits\RoleTrait;
    $resume = [
        'status' => [
            'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CONFIRMED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CANCELLED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        ]
    ];
    $sites = [];
    $destinations = [];
@endphp
@extends('layout.custom')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/sections/operations/operations.min.js') }}"></script>
    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    <script>
        let managment = {
            /**
             * ===== Render Table Settings ===== *
             * @param {*} table //tabla a renderizar
            */
            actionTable: function(table){
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

                _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                                <'table-responsive'tr>
                                <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;                        
                _settings.deferRender = true;
                _settings.responsive = true;
                _settings.buttons =  _buttons;
                _settings.order = [];
                // _settings.lengthMenu = [];
                // _settings.pageLength = 10;
                _settings.paging = false;
                _settings.oLanguage = {
                    "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                    "sInfo": components.getTranslation("table.pagination") + " _PAGE_ " + components.getTranslation("table.of") + " _PAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": components.getTranslation("table.search") + "...",
                    "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
                };

                table.DataTable( _settings );
            },
        };

        if ( document.getElementById('lookup_date') != null ) {
            const picker = new easepick.create({
                element: "#lookup_date",
                css: [
                    'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                ],
                zIndex: 10,
            });   
        }

        if( document.querySelector('.table-rendering') != null ){
            managment.actionTable($('.table-rendering'));
        }
        components.formReset();

        //DECLARACION DE VARIABLES
        const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
        const __title_modal = document.getElementById('filterModalLabel');

        //ACCION PARA CREAR
        if( __create != null ){
            __create.addEventListener('click', function () {
                __title_modal.innerHTML = this.dataset.title;
            });
        }

        function setStatus(event, type, status, item_id, rez_id){
            event.preventDefault();
            var clickedRow = event.target.closest('tr');
            var statusCell = clickedRow.querySelector('td:nth-child(4)');
            //statusCell.textContent = status;

            let alert_type = 'btn-secondary';
            switch (status) {
                case 'PENDING':
                    alert_type = 'secondary';
                    break;
                case 'COMPLETED':
                    alert_type = 'success';
                    break; 
                case 'NOSHOW':
                    alert_type = 'warning';
                    break;
                case 'CANCELLED':
                    alert_type = 'danger';
                    break;  
                default:
                    alert_type = 'secondary';
                    break;
            }    

            swal.fire({
                title: '¿Está seguro de actualizar el estatus?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed == true){

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });                
                    $.ajax({
                        url: `/operation/managment/update-status`,
                        type: 'PUT',
                        data: { rez_id:rez_id, item_id:item_id, type:type, status:status },
                        beforeSend: function() {        
                            
                        },
                        success: function(resp) {
                            Swal.fire({
                                title: '¡Éxito!',
                                icon: 'success',
                                html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
                                timer: 1500,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    const b = Swal.getHtmlContainer().querySelector('b')
                                    timerInterval = setInterval(() => {
                                        b.textContent = (Swal.getTimerLeft() / 1000)
                                            .toFixed(0)
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                statusCell.innerHTML = `<span class="badge badge-light-${alert_type} mb-2 me-4">${status}</span>`;
                            })

                        }
                    }).fail(function(xhr, status, error) {
                            console.log(xhr);
                            Swal.fire(
                                '¡ERROR!',
                                xhr.responseJSON.message,
                                'error'
                            );
                    });

                }
            });    
        
        }

        function setDriver(event, item_id){
            event.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });                
            $.ajax({
                url: `/operation/managment/update-status`,
                type: 'PUT',
                data: { item_id : item_id },
                success: function(resp) {
                    Swal.fire({
                        title: '¡Éxito!',
                        icon: 'success',
                        html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                            const b = Swal.getHtmlContainer().querySelector('b')
                            timerInterval = setInterval(() => {
                                b.textContent = (Swal.getTimerLeft() / 1000)
                                    .toFixed(0)
                            }, 100)
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {
                        statusCell.innerHTML = `<span class="badge badge-light-${alert_type} mb-2 me-4">${status}</span>`;
                    })

                }
            }).fail(function(xhr, status, error) {
                    console.log(xhr);
                    Swal.fire(
                        '¡ERROR!',
                        xhr.responseJSON.message,
                        'error'
                    );
            });        
        }        
    </script>    
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => 'Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtro de reservaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            ),
        );
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
                @if ($errors->any())
                    <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th>Pickup</th>
                            <th>Sitio</th>
                            <th>Conductor</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estatus Op.</th>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Pasajeros</th>
                            <th>Desde</th>
                            <th>Hacia</th>
                            <th>Pago</th>
                            <th>Total</th>
                            <th>Moneda</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($items)>=1)
                            @foreach($items as $key => $value)                                
                                @php
                                    $payment = ( $value->total_sales - $value->total_payments );
                                    if($payment < 0) $payment = 0;

                                    $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                                    $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                    $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                    $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                                    switch ($operation_status) {
                                        case 'PENDING':
                                            $label = 'secondary';
                                            break;
                                        case 'COMPLETED':
                                            $label = 'success';
                                            break;
                                        case 'NOSHOW':
                                            $label = 'warning';
                                            break;
                                        case 'CANCELLED':
                                            $label = 'danger';
                                            break;
                                        default:
                                            $label = 'secondary';
                                            break;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                    <td>{{ $value->site_name }}</td>
                                    <td>
                                        <select class="form-control" name="driver_id" id="driver_id">
                                            <option value="0">Selecciona un conductor</option>
                                            @if ( isset($drivers) && count($drivers) >= 1 )
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->id }}" onclick="setStatus(event, {{ $value->id }}">{{ $driver->names }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                    <td>{{ $value->final_service_type }}</td>
                                    <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                    <td>
                                        @if (RoleTrait::hasPermission(38))
                                            <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                        @else
                                            {{ $value->code }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $value->client_first_name }} {{ $value->client_last_name }}
                                        @if(!empty($value->reference))
                                            [{{ $value->reference }}]
                                        @endif
                                    </td>
                                    <td>{{ $value->service_name }}</td>
                                    <td class="text-center">{{ $value->passengers }}</td>
                                    <td>{{ $operation_from }}</td>
                                    <td>{{ $operation_to }}</td>
                                    <td class="text-center">{{ $value->status }}</td>
                                    <td class="text-end">{{ number_format($payment,2) }}</td>
                                    <td class="text-center">{{ $value->currency }}</td>
                                    <td class="text-center">
                                        <div class="btn-group mb-2 me-4">
                                            <button type="button" class="btn btn-primary">Acciones</button>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                <span class="visually-hidden ">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" onclick="setStatus(event, '{{ $value->operation_type }}', 'PENDING',{{ $value->id }}, {{ $value->reservation_id }})">Pendiente</a>
                                                <a class="dropdown-item" href="#" onclick="setStatus(event, '{{ $value->operation_type }}', 'COMPLETED',{{ $value->id }}, {{ $value->reservation_id }})">Completado</a>
                                                <a class="dropdown-item" href="#" onclick="setStatus(event, '{{ $value->operation_type }}', 'NOSHOW',{{ $value->id }}, {{ $value->reservation_id }})">No show</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#" onclick="setStatus(event, '{{ $value->operation_type }}', 'CANCELLED',{{ $value->id }}, {{ $value->reservation_id }})">Cancelado</a>                                                                
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @php
        // dump($date_search);
    @endphp
    <x-modals.reservations.reports :data="$date" />
@endsection