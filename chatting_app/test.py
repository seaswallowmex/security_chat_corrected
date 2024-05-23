import requests

# 測試加密接口
encrypt_response = requests.post('http://127.0.0.1:5000/encrypt', json={
    'message': 'Hello, this is a secure message.',
    'password': 'your-secure-password'
})
print("Encrypt Response:", encrypt_response.json())

# 從加密響應中獲取加密訊息
encrypted_message = encrypt_response.json().get('encrypted_message')

# 測試解密接口
decrypt_response = requests.post('http://127.0.0.1:5000/decrypt', json={
    'encrypted_message': encrypted_message,
    'password': 'your-secure-password'
})
print("Decrypt Response:", decrypt_response.json())
