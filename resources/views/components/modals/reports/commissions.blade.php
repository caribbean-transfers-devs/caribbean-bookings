@php
    use App\Traits\RoleTrait;
@endphp
@props(['users'])
<!-- Modal -->
<div class="modal fade" id="commissionsModal" tabindex="-1" role="dialog" aria-labelledby="commissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commissionsModalLabel">Tabla de comisiones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">                    
                    <div class="col-12">
                        <table class="table table-chart">
                            <thead>
                                <tr>                                                        
                                    <th>NOMBRE</th>
                                    <th class="text-ceneter">CANTIDAD</th>                                    
                                    <th class="text-ceneter">USD</th>
                                    <th class="text-ceneter">MXN</th>
                                    <th class="text-ceneter">TOTAL DE VENTA</th>
                                    <th class="text-ceneter">TOTAL OPERADA</th>
                                    <th>TOTAL PENDIENTE</th>
                                    @if ( RoleTrait::hasPermission(96) )
                                        <th class="text-center">COMISIÓN</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($users) >= 1)
                                    @foreach($users as $key => $value)
                                        @php
                                            $total = $value['TOTAL_CONFIRMED'];
                                            $commission = 0;
                                            if($total >= 50000 && $total <= 74999):
                                                //$commission = 2500;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 75000 && $total <= 99999):
                                                //$commission = 3750;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 100000 && $total <= 124999):
                                                //$commission = 6250;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 125000 && $total <= 174999):
                                                //$commission = 8750;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 175000):
                                                //$commission = 100000;
                                                $commission = 0.05 * $total;
                                            endif;
                                            $users[$key]['COMMISSION'] = $commission;
                                        @endphp
                                        <tr>
                                            <td>{{ $value['NAME'] }}</td>
                                            <td class="text-center">{{ $value['QUANTITY'] }}</td>                                            
                                            <td class="text-center">{{ number_format($value['USD'],2) }}</td>
                                            <td class="text-center">{{ number_format($value['MXN'],2) }}</td>
                                            <td class="text-center">{{ number_format($value['TOTAL'],2) }}</td>
                                            <td class="text-center">{{ number_format($value['TOTAL_CONFIRMED'],2) }}</td>
                                            <td class="text-center">{{ number_format($value['TOTAL_PENDING'],2) }}</td>
                                            @if ( RoleTrait::hasPermission(96) )
                                                <td class="text-center">{{ number_format($commission,2) }}</td>                                                
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="col-12">
                        <canvas class="chartSale" id="ChartSalesUsers"></canvas>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
            </div> --}}
        </div>
    </div>
</div>