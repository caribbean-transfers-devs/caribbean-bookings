@props(['bookings'])
<!-- Modal -->
<div class="modal fade" id="bookingsDayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="bookingsDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="affiliateModalLabel">Reservas del día</h5>
                <button type="button" class="btn-close __close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-rendering">
                        <thead>
                            <tr>
                                <th class="w-50"><div class="th-content">Información</div></th>
                                <th class="w-15"><div class="th-content">Estatus de reservación</div></th>
                                <th class="w-35"><div class="th-content">Total</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ( isset($bookings['bookings']) )
                                @foreach ($bookings['bookings'] as $key5 => $item)
                                    <tr>
                                        <td>
                                            <span><strong>Fecha de creación:</strong> {{$item->created_at}}</span><br>
                                            <span><strong>ID:</strong> {{ $item->id }}</span><br>
                                            <span>#<a class="text-primary" href="reservations/detail/{{ $item->id }}"> {{ $item->reservation_codes }}</a></span><br>                                                
                                            <span><strong>Canal:</strong> {{ $item->site_name }}</span><br>
                                            <span><strong>Cliente:</strong> {{ ucwords( strtolower( $item->client_full_name ) ) }}</span><br>
                                            <span><strong>Servicio:</strong> {{ $item->service_type_name }}</span><br>
                                            <span><strong>Destino:</strong> {{ $item->destination_name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-">{{ strtolower($item->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span><strong>Total:</strong> <?=number_format($item->total_sales, 2).' '.$item->currency?></span><br>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>