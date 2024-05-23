from flask import Flask, request, jsonify
from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC
from cryptography.hazmat.primitives import hashes, padding
from cryptography.hazmat.backends import default_backend
from base64 import urlsafe_b64encode, urlsafe_b64decode
import os
import time

app = Flask(__name__)

def encrypt_message(message, password):
    salt = os.urandom(16)
    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=32,
        salt=salt,
        iterations=100000,
        backend=default_backend()
    )
    key = kdf.derive(password.encode())
    iv = os.urandom(16)
    nonce = str(time.time()).encode()
    cipher = Cipher(algorithms.AES(key), modes.CFB(iv), backend=default_backend())
    encryptor = cipher.encryptor()
    padder = padding.PKCS7(128).padder()
    padded_data = padder.update(message.encode() + b'::' + nonce) + padder.finalize()
    encrypted_message = encryptor.update(padded_data) + encryptor.finalize()
    return urlsafe_b64encode(salt + iv + encrypted_message).decode()

def decrypt_message(encrypted_message, password):
    encrypted_data = urlsafe_b64decode(encrypted_message)
    salt = encrypted_data[:16]
    iv = encrypted_data[16:32]
    ciphertext = encrypted_data[32:]
    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=32,
        salt=salt,
        iterations=100000,
        backend=default_backend()
    )
    key = kdf.derive(password.encode())
    cipher = Cipher(algorithms.AES(key), modes.CFB(iv), backend=default_backend())
    decryptor = cipher.decryptor()
    padded_data = decryptor.update(ciphertext) + decryptor.finalize()
    unpadder = padding.PKCS7(128).unpadder()
    data = unpadder.update(padded_data) + unpadder.finalize()
    message, nonce = data.rsplit(b'::', 1)
    return message.decode()

@app.route('/')
def home():
    return 'Server is running!'

@app.route('/encrypt', methods=['POST'])
def encrypt():
    data = request.json
    message = data['message']
    password = data['password']
    encrypted_message = encrypt_message(message, password)
    return jsonify({"encrypted_message": encrypted_message})

@app.route('/decrypt', methods=['POST'])
def decrypt():
    data = request.json
    encrypted_message = data['encrypted_message']
    password = data['password']
    try:
        decrypted_message = decrypt_message(encrypted_message, password)
        return jsonify({"decrypted_message": decrypted_message})
    except Exception as e:
        return jsonify({"error": str(e)}), 400

if __name__ == '__main__':
    app.run(port=5000)
