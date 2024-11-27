import random

# Fungsi untuk menghasilkan 6 digit angka acak
def generate_random_number():
    return str(random.randint(100000, 999999))

# Buat list untuk menyimpan 100 data
random_numbers = []

# Loop untuk menghasilkan 100 angka acak
for _ in range(100):
    random_numbers.append(generate_random_number())

# Tampilkan hasil
for i, number in enumerate(random_numbers, 1):
    print(f"{i}. {number}")