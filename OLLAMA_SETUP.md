# Setup Ollama untuk IRON SMART

## Restart Ollama dengan Support Embeddings

Untuk menggunakan fitur RAG (Retrieval-Augmented Generation) pada AI Arsip, Ollama harus dijalankan dengan flag `--embeddings`.

### Langkah-langkah:

#### 1. Stop Ollama yang sedang berjalan
```bash
# Jika Ollama berjalan sebagai service
Stop-Service -Name ollama

# Atau jika berjalan sebagai background process
Get-Process ollama | Stop-Process
```

#### 2. Restart Ollama dengan flag embeddings
```bash
# Method 1: Jalankan langsung dengan flag
ollama serve --embeddings

# Method 2: Jalankan di background (PowerShell)
Start-Process ollama -ArgumentList "serve --embeddings" -NoNewWindow

# Method 3: Buat service dengan flag (Windows)
sc config ollama binPath= "C:\Program Files\Ollama\ollama.exe serve --embeddings"
sc start ollama
```

#### 3. Verifikasi embeddings support
```bash
# Test dengan PowerShell
$body = @{
    model="qwen3:8b"
    prompt="test"
} | ConvertTo-Json
Invoke-WebRequest -Uri http://localhost:11434/api/embeddings -Method POST -Body $body -ContentType "application/json"
```

Jika berhasil, akan menerima response dengan array embedding. Jika error, berarti embeddings belum aktif.

#### 4. Update konfigurasi aplikasi
Pastikan file `.env` memiliki konfigurasi:
```env
AI_PROVIDER=ollama
AI_SEARCH_STRATEGY=embedding
AI_TEMPERATURE=0.7
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=qwen3:8b
OLLAMA_MAX_TOKENS=2000
OLLAMA_EMBEDDING_MODEL=qwen3:8b
OLLAMA_EMBEDDING_DIMENSION=768
```

#### 5. Clear cache Laravel
```bash
php artisan config:clear
php artisan cache:clear
```

#### 6. Generate embeddings untuk arsip yang ada
```bash
# Ekstrak isi dokumen terlebih dahulu (jika belum)
php artisan arsip:extract-content

# Generate embeddings
php artisan app:generate-embeddings
```

## Troubleshooting

### Error: "This server does not support embeddings"
- Pastikan Ollama dijalankan dengan flag `--embeddings`
- Restart Ollama setelah menambahkan flag

### Error: Connection refused
- Pastikan Ollama berjalan di port 11434
- Cek dengan: `http://localhost:11434/api/tags`

### Embeddings tidak digenerate
- Pastikan arsip memiliki isi_dokumen
- Cek dengan: `php artisan arsip:check-data`
- Ekstrak isi dokumen: `php artisan arsip:extract-content`

## Model yang Mendukung Embeddings

Tidak semua model Ollama mendukung embeddings. Model yang direkomendasikan:
- `qwen3:8b` (sedang digunakan)
- `llama3:8b`
- `mistral:7b`
- `nomic-embed-text` (khusus untuk embeddings)

Untuk mengganti model, update `.env`:
```env
OLLAMA_MODEL=llama3:8b
OLLAMA_EMBEDDING_MODEL=llama3:8b
```

Lalu pull model:
```bash
ollama pull llama3:8b
```
