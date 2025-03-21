SELECT 
    er.prefijo, 
    er.codigo, 
    us.nombres, 
    us.apellidos, 
    us.usuario, 
    ev.url_excel
FROM eventos_reservados as er 
INNER JOIN usuarios as us ON us.id_usuario = er.id_usuario  
LEFT JOIN eventos as ev ON ev.id_evento = er.id_evento 
WHERE er.session_id = '".$session_id."'