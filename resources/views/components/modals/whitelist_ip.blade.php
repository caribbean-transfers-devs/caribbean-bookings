@props(['valid_ips'])
<div class="modal" tabindex="-1" id="whiteIPsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Direcciones IP sin restricción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                    <button class="btn btn-danger btn-sm" onclick="DelIP({{ $ip->id }})">Eliminar</button>
                                </td>
                            </tr>                            
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
