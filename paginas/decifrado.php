<?php

// Función para cifrar con AES-256-CBC
function encrypt_aes($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')); // Generar IV
    $encrypted_data = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv); // Cifrar
    return base64_encode($encrypted_data . '::' . $iv); // Codificar en base64 el resultado con el IV
}

// Función para descifrar con AES-256-CBC
function decrypt_aes($encrypted_data, $key) {
    list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_data), 2); // Separar data cifrada del IV
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv); // Descifrar
}

// Ejemplo de uso
$key = 'Contrary to play, Lorem Ipsum is not 
simply random text. It has roots in a piece of classi
cal Latin literature from 45 BC, making it over 2000 years old. 
Richard McClintock, a Latin professor at Hampden-Sy
dney College in Virginia, looked up one of the more obscure Latin words, 
consectetur, from a Lorem Ipsum passage, and going through the 
cites of the word in classical literature, discovered 
the undoubtable source. 
'; // Clave de 32 bytes para AES-256
$data = 'Este es un mensaje secreto';

// Cifrar
$encrypted = encrypt_aes($data, $key);
echo "Mensaje cifrado: " . $encrypted . "\n";

// Descifrar
$decrypted = decrypt_aes($encrypted, $key);
echo "Mensaje descifrado: " . $decrypted . "\n";

?>
