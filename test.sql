CREATE DATABASE transportation_api;
CREATE USER 'caribbean_user'@'localhost' IDENTIFIED BY 'caribbean2025##,().';
GRANT ALL PRIVILEGES ON transportation_api.* TO 'caribbean_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

CREATE USER 'caribbean_user'@'%' IDENTIFIED BY 'C@ribb3anT2025!##()';
GRANT ALL PRIVILEGES ON transportation_api.* TO 'caribbean_user'@'%';
FLUSH PRIVILEGES;
EXIT;

sudo mysql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'TuContraseñaSegura';
FLUSH PRIVILEGES;
exit;


COMANDO PARA ENTRAR DIRECTO A MYSQL:
sudo mysql
COMANDO PARA ENTRAR SOLICITANDO CONTRASEÑA:
sudo mysql -u root -p
COMANDO PARA SABER LOS USUARIO QUE TENEMOS Y A QUE HOST SE CONECTAN
SELECT user, host FROM mysql.user


//COMANDO PARA CAMBIAR LAS POLITICA DE CONTRASEÑA DE USUARIOS MYSQL
SET GLOBAL validate_password.policy = MEDIUM;

//COMANDO PARA CONFIGURAR FIREWALL
sudo ufw allow from 187.190.174.84 to any port 3306
sudo ufw allow from 138.68.15.200 to any port 3306
-- sudo ufw allow from 100.64.0.9     to any port 3306
-- # Permitir IP de Railway (reemplaza <IP_RAILWAY> con la IP que obtuviste)
sudo ufw allow from 100.64.0.9 to any port 80,443,3306
sudo ufw allow from 100.64.0.9 to any port 80,443,3306 proto tcp
# Permitir rangos de IPs de Cloudflare (HTTP/HTTPS)
sudo ufw allow from 173.245.48.0/20 to any port 80,443 proto tcp
sudo ufw allow from 103.21.244.0/22 to any port 80,443 proto tcp
-- # Agrega más rangos de Cloudflare si es necesario: https://www.cloudflare.com/ips/

sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw reload
sudo ufw status
sudo ufw status numbered

sudo systemctl restart mysql
sudo systemctl status mysql

sudo systemctl restart apache2
sudo systemctl status apache2

sudo ufw delete allow from 100.64.0.9 to any port 3306
USE transportation_api; SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'transportation_api'

sudo chown -R www-data:www-data /var/www/transportation_api
sudo chmod -R 775 storage bootstrap/cache

[ERROR en la consulta 42] Data truncated for column 'status_unit' at row 2
[ERROR en la consulta 148] Incorrect string value: '\xF0\x9F\x99\x84' for column 'text' at row 412
[ERROR en la consulta 165] Incorrect string value: '\xF0\x9F\x8E\x88' for column 'text' at row 360
[ERROR en la consulta 182] Incorrect string value: '\xF0\x9F\x98\x81' for column 'text' at row 3334
[ERROR en la consulta 186] Incorrect string value: '\xF0\x9F\x91\x8D' for column 'text' at row 3951
[ERROR en la consulta 208] Incorrect string value: '\xF0\x9F\x98\x8A' for column 'text' at row 3145
[ERROR en la consulta 215] Incorrect string value: '\xF0\x9F\xA5\xB3' for column 'text' at row 2348
[ERROR en la consulta 239] Incorrect string value: '\xF0\x9F\x98\x8A' for column 'text' at row 1128
[ERROR en la consulta 244] Incorrect string value: '\xF0\x9F\x98\x8A' for column 'text' at row 4660
[ERROR en la consulta 262] Incorrect string value: '\xF0\x9D\x99\xB2\xF0\x9D...' for column 'text' at row 921
[ERROR en la consulta 264] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 1194
[ERROR en la consulta 266] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 3598
[ERROR en la consulta 271] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 2963
[ERROR en la consulta 272] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 2604
[ERROR en la consulta 274] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 4487
[ERROR en la consulta 275] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 4512
[ERROR en la consulta 276] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 890
[ERROR en la consulta 317] Incorrect string value: '\xF0\x9D\x99\xB3\xF0\x9D...' for column 'flight_number' at row 1112
[ERROR en la consulta 343] Incorrect string value: '\xF0\x9D\x99\xB2\xF0\x9D...' for column 'client_first_name' at row 2295
[ERROR en la consulta 344] Incorrect string value: '\xF0\x9D\x99\xB3\xF0\x9D...' for column 'client_first_name' at row 314

[ERROR en la consulta 148] Incorrect string value: '\xF0\x9F\x93\xA7. ...' for column 'text' at row 1527
[ERROR en la consulta 165] Incorrect string value: '\xF0\x9F\x8E\x88' for column 'text' at row 360
[ERROR en la consulta 182] Incorrect string value: '\xF0\x9F\x98\x81' for column 'text' at row 3334
[ERROR en la consulta 186] Incorrect string value: '\xF0\x9F\x91\x8D' for column 'text' at row 3951
[ERROR en la consulta 208] Incorrect string value: '\xF0\x9F\x98\x8A' for column 'text' at row 3145
[ERROR en la consulta 215] Incorrect string value: '\xF0\x9F\xA5\xB3' for column 'text' at row 2348
[ERROR en la consulta 239] Incorrect string value: '\xF0\x9F\x98\x8A' for column 'text' at row 1128
[ERROR en la consulta 244] Incorrect string value: '\xF0\x9F\x98\x8A' for column 'text' at row 4660
[ERROR en la consulta 262] Incorrect string value: '\xF0\x9D\x99\xB2\xF0\x9D...' for column 'text' at row 921
[ERROR en la consulta 264] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 1194
[ERROR en la consulta 266] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 3598
[ERROR en la consulta 271] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 2963
[ERROR en la consulta 272] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 2604
[ERROR en la consulta 274] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 4487
[ERROR en la consulta 275] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 4512
[ERROR en la consulta 276] Incorrect string value: '\xF0\x9F\x8C\xB4\xF0\x9F...' for column 'text' at row 890
[ERROR en la consulta 318] Incorrect string value: '\xF0\x9D\x99\xB3\xF0\x9D...' for column 'flight_number' at row 1114
[ERROR en la consulta 344] Incorrect string value: '\xF0\x9D\x99\xB2\xF0\x9D...' for column 'client_first_name' at row 2295
[ERROR en la consulta 345] Incorrect string value: '\xF0\x9D\x99\xB3\xF0\x9D...' for column 'client_first_name' at row 314

ID de reservations_items 42775, 42776, tema con flight_number
reservations tema con client_first_name

update driver_schedules set status = NULL where status not in ('A','F','DT','PSG','INC','D','v')
update driver_schedules set status_unit = NULL where status_unit not in ('T','FO','OPB','S','OP')