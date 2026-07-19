<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SongController extends Controller
{
    public function index()
    {
        $songs = Song::all();
        return view('dashboard', compact('songs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nfc_uid' => 'required|string|unique:songs,nfc_uid',
            'title' => 'required|string',
            'artist' => 'required|string',
            'audio_file' => 'required|file|mimes:mp3,wav|max:20480', // Máx 20MB
        ]);

        // Generamos un nombre legible: "artista-titulo-uid.mp3"
        $extension = $request->file('audio_file')->getClientOriginalExtension();
        $filename = Str::slug($request->artist) . '-' . Str::slug($request->title) . '-' . Str::slug($request->nfc_uid) . '.' . $extension;

        // Guardamos en Backblaze B2 usando storeAs para mantener el nombre personalizado
        $path = $request->file('audio_file')->storeAs('songs', $filename, 's3');

        Song::create([
            'nfc_uid' => $request->nfc_uid,
            'title' => $request->title,
            'artist' => $request->artist,
            'file_path' => $path,
        ]);

        return redirect()->route('dashboard')->with('success', 'Disco registrado y subido a Backblaze B2 correctamente.');
    }

    public function apiFetch(Request $request)
    {
        $request->validate([
            'nfc_uid' => 'required|string',
        ]);

        $song = Song::where('nfc_uid', $request->nfc_uid)->first();

        if (!$song) {
            return response()->json(['error' => 'Canción no encontrada'], 404);
        }

        // Aquí usamos el accesor mágico del modelo que genera la URL firmada
        return response()->json([
            'url' => $song->audio_url
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function destroy(Song $song)
    {
        // Borrar archivo de Backblaze B2 (S3)
        Storage::disk('s3')->delete($song->file_path);

        // Borrar el registro de la base de datos
        $song->delete();

        return redirect()->route('dashboard')->with('success', 'Disco eliminado del sistema y de la nube correctamente.');
    }
}
