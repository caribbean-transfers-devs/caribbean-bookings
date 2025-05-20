@php
    $totals = [
        'TOTAL' => 0,
        'USD' => 0,
        'MXN' => 0,
        'TOTAL_PENDING' => 0,
        'TOTAL_COMPLETED' => 0,
        'TOTAL_DISCOUNT' => 0,
        'TOTAL_LESS_DISCOUNT' => 0,
        'COMMISSION' => 0,
        'QUANTITY' => 0,
    ];
@endphp
<table class="table custom-table table-chart table-chart-general">
    <thead>
        <tr>                                                        
            <th class="text-center">NOMBRE</th>
            <th class="text-center">CANTIDAD</th>                                    
            <th class="text-center">USD</th>
            <th class="text-center">MXN</th>
            <th class="text-center">TOTAL DE VENTA</th>                                
            <th class="text-center">TOTAL PENDIENTE</th>
            <th class="text-center">TOTAL OPERADA</th>
            <th class="text-center">DESCUENTO POR INVERSION</th>
            <th class="text-center">TOTAL MENOS DESCUENTO</th>
            <th class="text-center">COMISIÓN</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($users) >= 1)
            @foreach($users as $key => $user)
                @php
                    $totals['TOTAL'] += $user['TOTAL'];
                    $totals['USD'] += $user['USD'];
                    $totals['MXN'] += $user['MXN'];
                    $totals['TOTAL_PENDING'] += $user['TOTAL_PENDING'];
                    $totals['TOTAL_COMPLETED'] += $user['TOTAL_COMPLETED'];
                    $totals['TOTAL_DISCOUNT'] += $user['TOTAL_DISCOUNT'];
                    $totals['TOTAL_LESS_DISCOUNT'] += $user['TOTAL_LESS_DISCOUNT'];
                    $totals['COMMISSION'] += $user['COMMISSION'];
                    $totals['QUANTITY'] += $user['QUANTITY'];
                @endphp
                <tr>
                    <td class="text-center">{{ $user['NAME'] }}</td>
                    <td class="text-center">{{ $user['QUANTITY'] }}</td>
                    <td class="text-center">{{ number_format($user['USD'],2) }}</td>
                    <td class="text-center">{{ number_format($user['MXN'],2) }}</td>
                    <td class="text-center">{{ number_format($user['TOTAL'],2) }}</td>                                        
                    <td class="text-center">{{ number_format($user['TOTAL_PENDING'],2) }}</td>
                    <td class="text-center">{{ number_format($user['TOTAL_COMPLETED'],2) }}</td>
                    <td class="text-center">{{ number_format($user['TOTAL_DISCOUNT'],2) }}</td>
                    <td class="text-center">{{ number_format($user['TOTAL_LESS_DISCOUNT'],2) }}</td>
                    <td class="text-center">{{ number_format($user['COMMISSION'],2) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center">TOTALES</th>
            <th class="text-center">{{ $totals['QUANTITY'] }}</th>
            <th class="text-center">{{ number_format($totals['USD'],2) }}</th>
            <th class="text-center">{{ number_format($totals['MXN'],2) }}</th>
            <th class="text-center">{{ number_format($totals['TOTAL'],2) }}</th>                                
            <th class="text-center">{{ number_format($totals['TOTAL_PENDING'],2) }}</th>
            <th class="text-center">{{ number_format($totals['TOTAL_COMPLETED'],2) }}</th>
            <th class="text-center">{{ number_format($totals['TOTAL_DISCOUNT'],2) }}</th>
            <th class="text-center">{{ number_format($totals['TOTAL_LESS_DISCOUNT'],2) }}</th>
            <th class="text-center">{{ number_format($totals['COMMISSION'],2) }}</th>
        </tr>
    </tfoot>
</table>