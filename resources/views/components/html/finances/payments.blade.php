<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Método</th>
                <th>Descripción</th>
                <th class="text-center">Total</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">TC</th>
                <th class="text-start">Ref.</th>
                <th class="text-start">Categoria.</th>
                <th class="text-center">Fecha de pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr style="{{ $payment->category == "REFUND" ? 'background-color: #fbeced;' : '' }}">
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ $payment->description }}</td>
                    <td class="text-end">{{ number_format($payment->total) }}</td>
                    <td class="text-center">{{ $payment->currency }}</td>
                    <td class="text-end">{{ number_format($payment->exchange_rate) }}</td>
                    <td class="text-start">{{ $payment->reference }}</td>
                    <td class="text-start">{{ $payment->category }}</td>
                    <td class="text-center">{{ $payment->created_at }}</td>
                </tr>
            @endforeach                                   
        </tbody>
    </table>
</div>