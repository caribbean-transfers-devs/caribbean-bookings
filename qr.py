import webbrowser

# Datos del CFDI
uuid = "378EDC38-7B2A-4D22-A6C7-5DCEF0AE9612"
rfc_emisor = "SVE060825RD5"
rfc_receptor = "LACE790120I26"
total = "114499.980000"
sello = "05142230"

# Construir URL de verificación
url = f"https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id={uuid}&re={rfc_emisor}&rr={rfc_receptor}&tt={total}&fe={sello}"

# Parámetros personalizados para el QR
qr_params = {
    'size': '500x500',  # Tamaño más grande
    'data': url,
    'color': '0-0-0',  # Color negro (RGB)
    'bgcolor': '255-255-255',  # Fondo blanco
    'margin': '20',  # Margen más amplio
    'qzone': '2',  # Zona tranquila (quiet zone)
    'format': 'png',  # Formato de alta calidad
    'ecc': 'H'  # Alto nivel de corrección de errores
}

# Construir URL de la API con parámetros
qr_url = "https://api.qrserver.com/v1/create-qr-code/?" + "&".join(
    f"{k}={v}" for k, v in qr_params.items()
)

print("Generando QR con estas características:")
print(f"Tamaño: {qr_params['size']}")
print(f"Color: #{qr_params['color'].replace('-','')}")
print(f"Margen: {qr_params['margin']}px")
webbrowser.open(qr_url)