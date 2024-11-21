CREATE INDEX items_reservation_id ON reservations_items(reservation_id);

CREATE INDEX items_op_one_pickup ON reservations_items(op_one_pickup);
CREATE INDEX items_op_two_pickup ON reservations_items(op_two_pickup);
CREATE INDEX sales_reservation_id ON sales(reservation_id);
CREATE INDEX payments_reservation_id ON payments(reservation_id);
CREATE INDEX media_reservation_id ON reservations_media(reservation_id);
CREATE INDEX follow_up_reservation_id ON reservations_follow_up(reservation_id);

select * from `reservations` where `site_id` = 9
update `reservations` set `site_id` = 14 where `site_id` = 9
delete from `sites` where `id` = 9

TAREAS COMPLETADAS
Error en el reporte de operaciones de meses anteriores
Movimos las agencias de viator que estaban con un ID valido
Ordenamos de mayor a menor, mediante el campo cantidad
ID de usuario en el HEAD
agregamos el filtro "ver agencias", en el reporte de reservaciones
agregamos otra opción de origen de venta llamada cuentas


TAREAS PENDIENTES
los header se mantengan fixed
completar el registro de tiempos compartidos
En el reporte de operaciones, poder identificar, servicios que se se van a operarar, pero que fueron pagados en meses anteriores
sacar periodos anteriores en montos
sacar periodos actuales

INSERT INTO `sites` (`id`, `name`, `logo`, `payment_domain`, `color`, `transactional_email`, `transactional_email_send`, `transactional_phone`, `is_commissionable`, `created_at`, `updated_at`, `is_cxc`, `is_cxp`, `success_payment_url`, `cancel_payment_url`, `type_site`)
VALUES
	('9', 'Viator', 'https://ik.imagekit.io/zqiqdytbq/transportation-api/mailing/banner/caribbean-transfers-bg.png', 'https://caribbean-transfers.com', '#CE8506', 'bookings@caribbean-transfers.com', '1', X'2B353220393938203338372030323338', '0', '2023-09-19 17:39:21', '2023-09-19 17:39:21', '0', '0', '/thank-you', '/cancel', 'AGENCY');
