@props(['valid_ips'])
<!-- Modal -->
<div class="modal fade" id="whiteIPsModal" tabindex="-1" role="dialog" aria-labelledby="whiteIPsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="whiteIPsModalLabel">Direcciones IP sin restricción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_whitelist" action="#">
                    @csrf
                    <div class="row mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nueva IP (255.255.255.255)" id="valid_ip">
                            <button class="btn btn-success" type="button" onclick="StoreIP()">Agregar IP</button>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-bordered" id="tbl_whitelist">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Creador</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($valid_ips as $ip)
                            <tr>
                                <td>{{ $ip->ip_address }}</td>
                                <td>{{ $ip->user->name }}</td>
                                <td>{{ $ip->created_at }}</td>
                                <td>
                                    <button class="btn btn-danger" onclick="DelIP({{ $ip->id }})">Eliminar</button>
                                </td>
                            </tr>                            
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

