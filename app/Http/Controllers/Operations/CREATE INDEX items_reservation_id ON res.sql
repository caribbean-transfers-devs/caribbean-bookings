CREATE INDEX items_reservation_id ON reservations_items(reservation_id);

CREATE INDEX items_op_one_pickup ON reservations_items(op_one_pickup);
CREATE INDEX items_op_two_pickup ON reservations_items(op_two_pickup);
CREATE INDEX sales_reservation_id ON sales(reservation_id);
CREATE INDEX payments_reservation_id ON payments(reservation_id);
CREATE INDEX media_reservation_id ON reservations_media(reservation_id);
CREATE INDEX follow_up_reservation_id ON reservations_follow_up(reservation_id);

CREATE INDEX reservation_client_first_name ON reservations(client_first_name);
CREATE INDEX reservation_client_last_name ON reservations(client_last_name);
CREATE INDEX reservation_client_email ON reservations(client_email);
CREATE INDEX reservation_client_phone ON reservations(client_phone);
CREATE INDEX reservation_currency ON reservations(currency);
CREATE INDEX reservation_language ON reservations(language);
CREATE INDEX reservation_is_cancelled ON reservations(is_cancelled);
CREATE INDEX reservation_is_commissionable ON reservations(is_commissionable);
CREATE INDEX reservation_is_advanced ON reservations(is_advanced);
CREATE INDEX reservation_pay_at_arrival ON reservations(pay_at_arrival);
CREATE INDEX reservation_site_id ON reservations(site_id);
CREATE INDEX reservation_destination_id ON reservations(destination_id);
CREATE INDEX reservation_created_at ON reservations(created_at);
CREATE INDEX reservation_reference ON reservations(reference);
CREATE INDEX reservation_call_center_agent_id ON reservations(call_center_agent_id);
CREATE INDEX reservation_affiliate_id ON reservations(affiliate_id);
CREATE INDEX reservation_vendor_id ON reservations(vendor_id);
CREATE INDEX reservation_payment_reconciled ON reservations(payment_reconciled);
CREATE INDEX reservation_user_id ON reservations(user_id);
CREATE INDEX reservation_terminal ON reservations(terminal);
CREATE INDEX reservation_cancellation_type_id ON reservations(cancellation_type_id);
CREATE INDEX reservation_is_duplicated ON reservations(is_duplicated);
CREATE INDEX reservation_open_credit ON reservations(open_credit);
CREATE INDEX reservation_is_complete ON reservations(is_complete);
CREATE INDEX reservation_origin_sale_id ON reservations(origin_sale_id);


update `reservations` set `site_id` = 14 where `site_id` = 9
delete from `sites` where `id` = 9

select * from `reservations` where `site_id` = 9

TAREAS COMPLETADAS
Error en el reporte de operaciones de meses anteriores --------- PENDIENTE



TAREAS PENDIENTES
completar el registro de servicios compartidos

alguna opcion mejorada para poder agregar pagos, en donde al seleccionar el metodo de pago, este me de las opciones de bancos
mejorar los filtros en las graficas de reportes


Reporte de pagos, donde se podra conciliar los pagos de manera correcta, crear el BOT para que se concilie automaticamente pago PayPal
En el reporte de operaciones, poder identificar, servicios que se se van a operarar, pero que fueron pagados en meses anteriores
sacar periodos anteriores en montos
sacar periodos actuales

Tener una propuesta del excel que se me mando para el día martes


INSERT INTO `sites` (`id`, `name`, `logo`, `payment_domain`, `color`, `transactional_email`, `transactional_email_send`, `transactional_phone`, `is_commissionable`, `created_at`, `updated_at`, `is_cxc`, `is_cxp`, `success_payment_url`, `cancel_payment_url`, `type_site`)
VALUES
	('9', 'Viator', 'https://ik.imagekit.io/zqiqdytbq/transportation-api/mailing/banner/caribbean-transfers-bg.png', 'https://caribbean-transfers.com', '#CE8506', 'bookings@caribbean-transfers.com', '1', X'2B353220393938203338372030323338', '0', '2023-09-19 17:39:21', '2023-09-19 17:39:21', '0', '0', '/thank-you', '/cancel', 'AGENCY');


bjdl6g9bc7ntxke68hmv