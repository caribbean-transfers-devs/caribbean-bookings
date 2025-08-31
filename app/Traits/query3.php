<?php
    function queryBookings($query, $query2, $queryData){
        $bookings = DB::select("SELECT 
                                    rez.id AS reservation_id, 
                                    CONCAT(rez.client_first_name,' ',rez.client_last_name) as full_name,
                                    rez.client_email,
                                    rez.client_phone,
                                    rez.currency,
                                    rez.is_cancelled,
                                    rez.is_commissionable,                                    
                                    rez.site_id,
                                    rez.pay_at_arrival,
                                    rez.reference,
                                    rez.affiliate_id,
                                    rez.terminal,
                                    rez.comments,
                                    rez.is_duplicated,
                                    rez.open_credit,
                                    rez.is_complete,
                                    rez.created_at,
                                    rez.is_quotation,
                                    rez.was_is_quotation,
                                    rez.campaign,
                                    rez.reserve_rating,
                                    rez.is_last_minute,
                                    
                                    us.id AS employee_code,
                                    us.status AS employee_status,
                                    us.name AS employee,

                                    site.id as site_code,
                                    site.type_site AS type_site,
                                    site.name AS site_name,
                                    origin.code AS origin_code,
                                    tc.name_es AS cancellation_reason,
                                    GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                                    GROUP_CONCAT(DISTINCT it.zone_one_name ORDER BY it.zone_one_name ASC SEPARATOR ',') AS destination_name_from,
                                    GROUP_CONCAT(DISTINCT it.zone_one_id ORDER BY it.zone_one_id ASC SEPARATOR ',') AS zone_one_id,
                                    GROUP_CONCAT(DISTINCT it.from_name SEPARATOR ',') AS from_name,
                                    GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name_to,
                                    GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                                    GROUP_CONCAT(DISTINCT it.to_name SEPARATOR ',') AS to_name,
                                    GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                                    GROUP_CONCAT(DISTINCT it.pickup_from ORDER BY it.pickup_from ASC SEPARATOR ',') AS pickup_from,
                                    GROUP_CONCAT(DISTINCT it.pickup_to ORDER BY it.pickup_to ASC SEPARATOR ',') AS pickup_to,
                                    GROUP_CONCAT(DISTINCT it.one_service_status ORDER BY it.one_service_status ASC SEPARATOR ',') AS one_service_status,
                                    GROUP_CONCAT(DISTINCT it.two_service_status ORDER BY it.two_service_status ASC SEPARATOR ',') AS two_service_status,
                                    
                                    -- NUEVO: Tipo de servicio para cada item (mejorado para round trips)
                                    GROUP_CONCAT(DISTINCT it.service_types ORDER BY it.service_types ASC SEPARATOR ',') AS service_types,

                                    GROUP_CONCAT(DISTINCT it.one_cancellation_level ORDER BY it.one_cancellation_level ASC SEPARATOR ',') AS one_cancellation_level,
                                    GROUP_CONCAT(DISTINCT it.two_cancellation_level ORDER BY it.two_cancellation_level ASC SEPARATOR ',') AS two_cancellation_level,                                    
                                    SUM(it.passengers) as passengers,
                                    COALESCE(SUM(it.op_one_pickup_today) + SUM(it.op_two_pickup_today), 0) as is_today,
                                    COALESCE(SUM(it.op_one_pickup_tomorrow) + SUM(it.op_two_pickup_tomorrow), 0) as is_tomorrow,
                                    SUM(it.is_round_trip) as is_round_trip,
                                    MAX(it.is_same_day_round_trip) AS is_same_day_round_trip,

                                    COALESCE(SUM(s.total_sales), 0) as total_sales,
                                    COALESCE(SUM(s.total_sales_credit), 0) as total_sales_credit,
                                    -- NUEVO: Monto de ventas de transportación
                                    COALESCE(s.transportation_sales, 0) as transportation_sales_amount,

                                    COALESCE(SUM(p.total_payments), 0) as total_payments,
                                    COALESCE(SUM(p.total_payments_credit), 0) as total_payments_credit,
                                    -- NUEVO: Campos de pago de transportación
                                    COALESCE(p.transportation_payment_amount, 0) as transportation_payment_amount,
                                    p.transportation_payment_date,
                                    CASE 
                                        WHEN p.transportation_paid_later = 1 THEN 'PAID_AFTER_SALE'
                                        WHEN s.transportation_sales > 0 AND p.transportation_payment_amount >= s.transportation_sales THEN 'PAID'
                                        WHEN s.transportation_sales > 0 AND p.transportation_payment_amount > 0 AND p.transportation_payment_amount < s.transportation_sales THEN 'PARTIALLY_PAID'
                                        WHEN s.transportation_sales > 0 THEN 'NOT_PAID'
                                        ELSE 'NO_TRANSPORTATION_SALE'
                                    END AS transportation_payment_status,
                                    
                                    p.credit_conciliation_status as credit_conciliation_status,
                                    COALESCE(p.credit_payment_ids) AS credit_payment_ids,

                                    GROUP_CONCAT(DISTINCT p.credit_references_agency ORDER BY p.credit_references_agency ASC SEPARATOR ', ') AS credit_references_agency,
                                    GROUP_CONCAT(DISTINCT p.credit_references_payment ORDER BY p.credit_references_payment ASC SEPARATOR ', ') AS credit_references_payment,
                                    GROUP_CONCAT(DISTINCT p.credit_comments ORDER BY p.credit_comments ASC SEPARATOR ', ') AS credit_comments,
                                    GROUP_CONCAT(DISTINCT p.credit_conciliation_dates ORDER BY p.credit_conciliation_dates ASC SEPARATOR ', ') AS credit_conciliation_dates,
                                    GROUP_CONCAT(DISTINCT p.credit_deposit_dates ORDER BY p.credit_deposit_dates ASC SEPARATOR ', ') AS credit_deposit_dates,

                                    COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) AS total_balance,

                                    -- Información de reembolsos
                                    CASE WHEN rr.reservation_id IS NOT NULL THEN 1 ELSE 0 END as has_refund_request,
                                    COALESCE(rr.refund_count, 0) as refund_request_count,
                                    CASE 
                                        WHEN rr.reservation_id IS NULL THEN 'NO_REFUND'
                                        WHEN rr.pending_refund_count > 0 THEN 'REFUND_REQUESTED'
                                        ELSE 'REFUND_COMPLETED'
                                    END as refund_status,
                                    CASE
                                        WHEN rez.is_cancelled = 1   AND rez.was_is_quotation = 1  THEN 'EXPIRED_QUOTATION'
                                        WHEN rez.is_cancelled = 1   THEN 'CANCELLED'
                                        WHEN rez.is_duplicated = 1  THEN 'DUPLICATED'
                                        WHEN rez.open_credit = 1    THEN 'OPENCREDIT'
                                        WHEN rez.is_quotation = 1   THEN 'QUOTATION'
                                        WHEN site.is_cxc = 1 AND ( COALESCE(SUM(p.total_payments), 0) = 0 OR ( COALESCE(SUM(p.total_payments), 0) < COALESCE(SUM(s.total_sales), 0) ) ) THEN 'CREDIT'
                                        WHEN rez.pay_at_arrival = 1 AND COALESCE(SUM(p.total_payments), 0) = 0 THEN 'PAY_AT_ARRIVAL'
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'CONFIRMED'
                                        ELSE 'UNKNOWN'
                                    END AS reservation_status,
                                    CASE
                                        WHEN site.is_cxc = 1 AND COALESCE(SUM(p.total_payments), 0) = 0 THEN 'CREDIT'
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'PAID'
                                        ELSE 'PENDING'
                                    END AS payment_status,
                                    GROUP_CONCAT(
                                        DISTINCT 
                                        CASE
                                            WHEN site.is_cxc = 1 AND p.payment_type_name IS NULL THEN 'CREDIT'
                                            WHEN p.payment_type_name IS NOT NULL THEN p.payment_type_name
                                            WHEN rez.pay_at_arrival = 1 THEN 'CASH'  -- Asumiendo que pay_at_arrival=1 significa pago en efectivo
                                            ELSE 'SIN METODO DE PAGO'
                                        END
                                    ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                                    GROUP_CONCAT(DISTINCT p.payment_details ORDER BY p.payment_details ASC SEPARATOR ', ') AS payment_details
                                FROM reservations as rez
                                    INNER JOIN sites as site ON site.id = rez.site_id
                                    LEFT JOIN users as us ON us.id = rez.call_center_agent_id
                                    LEFT JOIN origin_sales as origin ON origin.id = rez.origin_sale_id
                                    LEFT JOIN types_cancellations as tc ON tc.id = rez.cancellation_type_id                                    
                                    
                                    -- Reembolsos optimizados
                                    LEFT JOIN (
                                        SELECT 
                                            reservation_id,
                                            COUNT(*) as refund_count,
                                            SUM(CASE WHEN status != 'REFUND_COMPLETED' THEN 1 ELSE 0 END) as pending_refund_count
                                        FROM reservations_refunds
                                        GROUP BY reservation_id
                                    ) as rr ON rr.reservation_id = rez.id
                                    
                                    -- Ventas optimizadas
                                    LEFT JOIN (
                                        SELECT 
                                            reservation_id, 
                                            ROUND(SUM(total), 2) as total_sales,
                                            ROUND(SUM(CASE WHEN sale_type_id = 1 THEN total ELSE 0 END), 2) AS total_sales_credit,
                                            ROUND(SUM(CASE WHEN sale_type_id = 1 THEN total ELSE 0 END), 2) AS transportation_sales,
                                            MAX(CASE WHEN sale_type_id = 1 THEN created_at END) AS transportation_sale_date
                                        FROM sales
                                        WHERE deleted_at IS NULL 
                                        GROUP BY reservation_id
                                    ) as s ON s.reservation_id = rez.id

                                    -- JOINs para tabla de pagos (payments)
                                    -- PAGOS OPTIMIZADOS (CRÍTICO)
                                    LEFT JOIN (
                                        SELECT 
                                            p.reservation_id,
                                            -- Totales
                                            ROUND(SUM(CASE
                                                WHEN p.category IN ('PAYOUT', 'PAYOUT_CREDIT_PAID') THEN 
                                                    CASE p.operation
                                                        WHEN 'multiplication' THEN p.total * p.exchange_rate
                                                        WHEN 'division' THEN p.total / p.exchange_rate
                                                        ELSE p.total 
                                                    END
                                                ELSE 0
                                            END), 2) AS total_payments,

                                            ROUND(SUM(CASE
                                                WHEN p.category IN ('PAYOUT_CREDIT_PENDING', 'PAYOUT_CREDIT_PAID') THEN 
                                                    CASE p.operation
                                                        WHEN 'multiplication' THEN p.total * p.exchange_rate
                                                        WHEN 'division' THEN p.total / p.exchange_rate
                                                        ELSE p.total 
                                                    END
                                                ELSE 0
                                            END), 2) AS total_payments_credit,                                            

                                            -- Transportación (SIN subconsultas correlacionadas)
                                            MAX(CASE 
                                                WHEN st.transportation_total > 0 
                                                    AND p.total >= st.transportation_total
                                                    AND DATE(p.created_at) > DATE(st.max_transportation_date)
                                                THEN 1 
                                                ELSE 0 
                                            END) AS transportation_paid_later,

                                            ROUND(SUM(CASE
                                                WHEN st.transportation_total > 0 
                                                    AND p.total >= st.transportation_total
                                                    AND DATE(p.created_at) > DATE(st.max_transportation_date)
                                                THEN 
                                                    CASE p.operation
                                                        WHEN 'multiplication' THEN p.total * p.exchange_rate
                                                        WHEN 'division' THEN p.total / p.exchange_rate
                                                        ELSE p.total 
                                                    END
                                                ELSE 0
                                            END), 2) AS transportation_payment_amount,

                                            MAX(CASE 
                                                WHEN st.transportation_total > 0 
                                                    AND p.total >= st.transportation_total
                                                    AND DATE(p.created_at) > DATE(st.max_transportation_date)
                                                THEN p.created_at 
                                                ELSE NULL 
                                            END) AS transportation_payment_date,

                                            -- Campos existentes de pagos
                                            CONCAT('[',
                                                GROUP_CONCAT(DISTINCT 
                                                    CASE WHEN p.payment_method = 'CREDIT' OR p.payment_method LIKE '%CREDIT%' THEN p.id END
                                                    ORDER BY p.id ASC SEPARATOR ','
                                                ), ']'
                                            ) AS credit_payment_ids,                                        
                                            
                                            CASE 
                                                WHEN MAX(CASE WHEN p.category IN ('PAYOUT_CREDIT', 'PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN p.is_conciliated END) = 2 THEN 'pre-reconciled'
                                                WHEN MAX(CASE WHEN p.category IN ('PAYOUT_CREDIT', 'PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN p.is_conciliated END) = 1 THEN 'reconciled'
                                                WHEN MAX(CASE WHEN p.category IN ('PAYOUT_CREDIT', 'PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN p.is_conciliated END) = 3 THEN 'cxc'
                                                ELSE NULL
                                            END AS credit_conciliation_status,

                                            GROUP_CONCAT(DISTINCT CASE WHEN p.category IN ('PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN p.reference_invoice END SEPARATOR ', ') AS credit_references_agency,
                                            GROUP_CONCAT(DISTINCT CASE WHEN p.category IN ('PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN p.reference END SEPARATOR ', ') AS credit_references_payment,
                                            GROUP_CONCAT(DISTINCT CASE WHEN p.category IN ('PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN p.conciliation_comment END SEPARATOR ', ') AS credit_comments,
                                            GROUP_CONCAT(DISTINCT CASE WHEN p.category IN ('PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN DATE_FORMAT(p.date_conciliation, '%Y-%m-%d') END SEPARATOR ', ') AS credit_conciliation_dates,
                                            GROUP_CONCAT(DISTINCT CASE WHEN p.category IN ('PAYOUT_CREDIT_PAID', 'PAYOUT_CREDIT_PENDING') THEN DATE_FORMAT(p.deposit_date, '%Y-%m-%d') END SEPARATOR ', ') AS credit_deposit_dates,                                            
                                            GROUP_CONCAT(DISTINCT p.payment_method ORDER BY p.payment_method ASC SEPARATOR ',') AS payment_type_name,
                                            GROUP_CONCAT(
                                                DISTINCT CONCAT(
                                                    p.payment_method, ' | ', 
                                                    ROUND(CASE 
                                                        WHEN p.operation = 'multiplication' THEN p.total * p.exchange_rate
                                                        WHEN p.operation = 'division' THEN p.total / p.exchange_rate
                                                        ELSE p.total END, 2), ' | ', 
                                                    p.reference
                                            ) ORDER BY p.payment_method ASC SEPARATOR ', ') AS payment_details
                                        FROM payments p

                                        -- JOIN CRÍTICO: Precalculamos datos de transportación
                                        LEFT JOIN (
                                            SELECT reservation_id,
                                                SUM(CASE WHEN sale_type_id = 1 THEN total ELSE 0 END) AS transportation_total,
                                                MAX(CASE WHEN sale_type_id = 1 THEN created_at END) AS max_transportation_date
                                            FROM sales
                                            WHERE deleted_at IS NULL
                                            GROUP BY reservation_id
                                        ) st ON st.reservation_id = p.reservation_id
                                        
                                        WHERE p.deleted_at IS NULL
                                        GROUP BY p.reservation_id
                                    ) as p ON p.reservation_id = rez.id

                                    -- JOINs para tabla de items de reservacion (reservations_items)
                                    LEFT JOIN (
                                        SELECT  
                                            it.reservation_id, 
                                            it.is_round_trip,
                                            SUM(it.passengers) as passengers,
                                            GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS code,
                                            
                                            GROUP_CONCAT(DISTINCT zone_one.name ORDER BY zone_one.name ASC SEPARATOR ',') AS zone_one_name,
                                            GROUP_CONCAT(DISTINCT zone_one.id ORDER BY zone_one.id ASC SEPARATOR ',') AS zone_one_id,
                                            GROUP_CONCAT(DISTINCT it.from_name SEPARATOR ',') AS from_name,

                                            GROUP_CONCAT(DISTINCT zone_two.name ORDER BY zone_two.name ASC SEPARATOR ',') AS zone_two_name, 
                                            GROUP_CONCAT(DISTINCT zone_two.id ORDER BY zone_two.id ASC SEPARATOR ',') AS zone_two_id,
                                            GROUP_CONCAT(DISTINCT it.to_name SEPARATOR ',') AS to_name,

                                            GROUP_CONCAT(DISTINCT dest.id ORDER BY dest.id ASC SEPARATOR ',') AS service_type_id,
                                            GROUP_CONCAT(DISTINCT dest.name ORDER BY dest.name ASC SEPARATOR ',') AS service_type_name,
                                            GROUP_CONCAT(DISTINCT it.op_one_pickup ORDER BY it.op_one_pickup ASC SEPARATOR ',') AS pickup_from,
                                            GROUP_CONCAT(DISTINCT it.op_two_pickup ORDER BY it.op_two_pickup ASC SEPARATOR ',') AS pickup_to,
                                            GROUP_CONCAT(DISTINCT it.op_one_status ORDER BY it.op_one_status ASC SEPARATOR ',') AS one_service_status,
                                            GROUP_CONCAT(DISTINCT it.op_two_status ORDER BY it.op_two_status ASC SEPARATOR ',') AS two_service_status,
                                            
                                            -- NUEVO: Determinar el tipo de servicio para cada tramo del round trip
                                            GROUP_CONCAT(
                                                DISTINCT 
                                                CASE 
                                                    -- Primer tramo (ida)
                                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                                END
                                                ORDER BY it.op_one_pickup ASC 
                                                SEPARATOR ','
                                            ) AS service_types,

                                            GROUP_CONCAT(DISTINCT it.op_one_cancellation_level ORDER BY it.op_one_cancellation_level ASC SEPARATOR ',') AS one_cancellation_level,
                                            GROUP_CONCAT(DISTINCT it.op_two_cancellation_level ORDER BY it.op_two_cancellation_level ASC SEPARATOR ',') AS two_cancellation_level,                                            
                                            MAX(CASE WHEN DATE(it.op_one_pickup) = DATE(rez.created_at) THEN 1 ELSE 0 END) AS op_one_pickup_today,
                                            MAX(CASE WHEN DATE(it.op_two_pickup) = DATE(rez.created_at) THEN 1 ELSE 0 END) AS op_two_pickup_today,

                                            MAX(CASE WHEN DATE(it.op_one_pickup) = DATE(DATE_ADD(rez.created_at, INTERVAL 1 DAY)) THEN 1 ELSE 0 END) AS op_one_pickup_tomorrow,
                                            MAX(CASE WHEN DATE(it.op_two_pickup) = DATE(DATE_ADD(rez.created_at, INTERVAL 1 DAY)) THEN 1 ELSE 0 END) AS op_two_pickup_tomorrow,                                            

                                            -- Nueva condición para verificar si es round trip y las fechas son el mismo día
                                            MAX(CASE WHEN it.is_round_trip = 1 AND DATE(it.op_one_pickup) = DATE(it.op_two_pickup) THEN 1 ELSE 0 END) AS is_same_day_round_trip
                                        FROM reservations_items as it
                                            INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                            INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                            INNER JOIN destination_services as dest ON dest.id = it.destination_service_id
                                            INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                        GROUP BY it.reservation_id, it.is_round_trip
                                    ) as it ON it.reservation_id = rez.id
                                    WHERE 1=1 {$query}
                                GROUP BY rez.id, 
                                        rez.client_first_name, 
                                        rez.client_last_name,
                                        rez.client_email,
                                        rez.client_phone,
                                        rez.currency,
                                        rez.is_cancelled,
                                        rez.is_commissionable,
                                        rez.site_id,
                                        rez.pay_at_arrival,
                                        rez.reference,
                                        rez.affiliate_id,
                                        rez.terminal,
                                        rez.comments,
                                        rez.is_duplicated,
                                        rez.open_credit,
                                        rez.is_complete,
                                        rez.created_at,
                                        rez.is_quotation,
                                        rez.was_is_quotation,
                                        rez.campaign,
                                        rez.reserve_rating,
                                        rez.is_last_minute,
                                        us.id,
                                        us.status,
                                        us.name,
                                        site.id,
                                        site.type_site,
                                        site.name,
                                        site.is_cxc,
                                        origin.code,
                                        tc.name_es,
                                        p.credit_payment_ids,
                                        p.credit_conciliation_status,
                                        
                                        p.transportation_paid_later,
                                        p.transportation_payment_amount,
                                        p.transportation_payment_date,

                                        s.transportation_sales,

                                        rr.reservation_id,
                                        rr.refund_count,
                                        rr.pending_refund_count {$query2}",
                                    $queryData);

        if(sizeof( $bookings ) > 0):
            usort($bookings, array($this, 'orderByDateTime'));
        endif;

        return $bookings;
    }