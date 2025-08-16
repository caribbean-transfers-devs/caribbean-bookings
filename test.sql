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


sudo ufw delete allow from 100.64.0.9 to any port 3306
USE transportation_api; SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'transportation_api'


sudo chown -R www-data:www-data /var/www/transportation_api
sudo chmod -R 775 storage bootstrap/cache