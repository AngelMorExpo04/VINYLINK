<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Song extends Model
{
    use HasFactory;

    protected $fillable = ['nfc_uid', 'title', 'artist', 'file_path'];

    // Para que la URL firmada se serialice automáticamente si mandas el modelo completo
    protected $appends = ['audio_url'];

    public function getAudioUrlAttribute()
    {
        try {
            // Genera una URL temporal que expira en 60 minutos
            return Storage::disk('s3')->temporaryUrl($this->file_path, now()->addMinutes(60));
        } catch (\Exception $e) {
            // Fallback si no están puestas las credenciales en el .env aún
            return ''; 
        }
    }
}
