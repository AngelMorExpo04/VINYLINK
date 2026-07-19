<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Vinylink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .vinyl-record.playing {
            animation: spin 3s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .vinyl-container {
            perspective: 800px;
        }
        .vinyl-wrapper {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform-style: preserve-3d;
        }
        .vinyl-container:hover .vinyl-wrapper,
        .vinyl-wrapper.is-tilted {
            transform: rotateX(60deg) scale(0.95) translateY(10px);
            box-shadow: 0 35px 20px -15px rgba(0,0,0,0.4);
        }
        
        /* Estado inicial del brazo: debajo del disco, plegado (pequeño), escondido y transparente */
        .tonearm {
            transform: translateZ(-20px) rotate(70deg) scale(0.5);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        /* Al hacer clic (cuando se queda fijado): aparece, crece y se coloca encima */
        .vinyl-wrapper.is-tilted .tonearm {
            transform: translateZ(20px) rotate(-12deg) scale(1);
            opacity: 1;
        }
        
        /* Al darle a Play: el brazo se mueve hacia los surcos */
        .vinyl-wrapper.is-playing .tonearm {
            transform: translateZ(20px) rotate(15deg) scale(1) !important;
            opacity: 1 !important;
        }

        /* Animación Letra 'i' -> Vinilo */
        .interactive-i {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .interactive-dot {
            position: absolute;
            top: -0.2em; /* Elevado para que el punto grande no se superponga mal con la I mayúscula */
            left: 50%;
            transform: translateX(-50%) translateY(0);
            width: 0.32em; /* Punto más grande */
            height: 0.32em; /* Punto más grande */
            border-radius: 50%;
            transition: transform 0.4s ease-in, background-color 0.4s ease, border 0.4s ease, box-shadow 0.4s ease;
            pointer-events: none;
        }
        @keyframes bounce-up {
            0% { transform: translateX(-50%) translateY(0); }
            30% { transform: translateX(-50%) translateY(-35px); }
            55% { transform: translateX(-50%) translateY(0); }
            75% { transform: translateX(-50%) translateY(-15px); }
            88% { transform: translateX(-50%) translateY(0); }
            94% { transform: translateX(-50%) translateY(-5px); }
            100% { transform: translateX(-50%) translateY(0); }
        }
        @keyframes shine-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .interactive-i.is-animating .interactive-dot {
            background-color: #111 !important;
            box-shadow: 0 3px 6px rgba(0,0,0,0.5); /* Sombra estática hacia abajo */
            border: 0.5px solid #000;
            animation: bounce-up 1s linear forwards; /* Solo salta, no gira */
            transition: none;
        }
        /* Reflejo del vinilo que girará para dar la ilusión */
        .vinyl-shine {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            border-radius: 50%;
            background: conic-gradient(from 45deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.4) 15%, rgba(255,255,255,0) 30%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.4) 65%, rgba(255,255,255,0) 80%);
            opacity: 0;
            pointer-events: none;
            z-index: 10;
        }
        .interactive-i.is-animating .vinyl-shine {
            opacity: 1;
            transition: opacity 0.2s ease 0.8s; /* Aparece al final del salto */
            animation: shine-spin 1s linear infinite 1s;
        }
        .interactive-dot::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 35%; height: 35%;
            background-color: #2FA084;
            border-radius: 50%;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .interactive-dot::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 10%; height: 10%;
            background-color: #111;
            border-radius: 50%;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .interactive-i.is-animating .interactive-dot::before,
        .interactive-i.is-animating .interactive-dot::after {
            transform: translate(-50%, -50%) scale(1);
            transition-delay: 0.1s;
        }
        .interactive-dot .mini-groove {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0);
            border-radius: 50%;
            border: 0.5px solid rgba(255,255,255,0.15);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease;
            opacity: 0;
        }
        .interactive-i.is-animating .interactive-dot .mini-groove {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            transition-delay: 0.05s;
        }
    </style>
</head>
<body class="bg-brand-yellow text-brand-navy min-h-screen font-sans antialiased selection:bg-brand-orange/40">

    <div class="max-w-7xl mx-auto p-4 lg:p-12">
        <!-- Header -->
        <header class="flex flex-col lg:flex-row items-center justify-between mb-6 lg:mb-16 border-b border-brand-purple/20 pb-4 lg:pb-6 pt-2 lg:pt-4 gap-1 lg:gap-0">
            <!-- Izquierda (Subtítulo) -->
            <div class="flex-1 flex justify-center lg:justify-start order-2 lg:order-1 w-full lg:self-end lg:pb-3">
                <p class="text-brand-purple font-semibold text-base lg:text-xl text-center lg:text-left">Gestor de biblioteca musical física</p>
            </div>
            
            <!-- Centro (Título) -->
            <div class="flex-shrink-0 order-1 lg:order-2 text-center">
                <h1 class="text-6xl md:text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-brand-navy to-brand-purple tracking-tight pb-2 relative z-50">
                    V<span class="interactive-i" onclick="this.classList.toggle('is-animating')">I<div class="interactive-dot bg-[#237A69]"><div class="vinyl-shine"></div><div class="mini-groove" style="width: 75%; height: 75%;"></div><div class="mini-groove" style="width: 55%; height: 55%;"></div></div></span>NYL<span class="interactive-i" onclick="this.classList.toggle('is-animating')">I<div class="interactive-dot bg-[#2B937C]"><div class="vinyl-shine"></div><div class="mini-groove" style="width: 75%; height: 75%;"></div><div class="mini-groove" style="width: 55%; height: 55%;"></div></div></span>NK
                </h1>
            </div>

            <!-- Derecha (Logo con animación de Tocadiscos - Oculto en móvil) -->
            <div class="hidden lg:flex flex-1 justify-end order-3 w-full">
                <div class="group h-16 w-16 rounded-2xl bg-brand-navy/10 flex items-center justify-center shadow-[inset_4px_4px_8px_rgba(0,0,0,0.1),inset_-4px_-4px_8px_rgba(255,255,255,0.9)] overflow-hidden relative cursor-pointer" title="¡Pon el disco!">
                    
                    <!-- Brazo del tocadiscos original (oculto arriba a la derecha, cae al hacer hover) -->
                    <div class="absolute top-[-8px] right-[-4px] origin-[top_center] rotate-[-50deg] group-hover:rotate-[22deg] transition-transform duration-500 ease-in-out z-20 pointer-events-none">
                        <!-- Eje/Base del brazo -->
                        <div class="absolute top-2 left-[-2px] w-2.5 h-2.5 bg-gray-500 rounded-full shadow-sm z-30"></div>
                        <!-- Palo del brazo -->
                        <div class="w-1 h-8 bg-gradient-to-b from-gray-300 to-gray-500 rounded-full shadow-sm ml-[1px] mt-2"></div>
                        <!-- Aguja / Cabezal -->
                        <div class="absolute bottom-[-3px] left-[-2px] w-2 h-3.5 bg-gray-700 rounded-sm shadow-md"></div>
                    </div>

                    <!-- Logo (Disco) -->
                    <!-- Usamos duration-1000 y rotate-[360deg] para que dé una vuelta completa tipo DJ al pasar el ratón -->
                    <img src="{{ asset('logo-tocadisco.svg') }}" alt="Logo Tocadiscos" class="w-14 h-14 object-contain transition-transform duration-[1500ms] ease-in-out group-hover:rotate-[720deg] relative z-10">
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-6 lg:mb-8 p-4 bg-brand-orange/30 border border-brand-purple/30 rounded-xl text-brand-navy font-bold flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-4 bg-red-100 border border-red-300 rounded-xl text-red-700 font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <main class="mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Left Column: Upload Form -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Contenedor con efecto hundido (neumorfismo) -->
                    <div class="bg-[#e8e4db] rounded-3xl p-8 relative overflow-hidden shadow-[inset_6px_6px_12px_rgba(0,0,0,0.1),inset_-6px_-6px_12px_rgba(255,255,255,0.8)]">
                        
                        <!-- SVG Vinilo Decorativo cortado en la esquina -->
                        <div class="absolute -top-12 -right-12 text-gray-300/40 pointer-events-none">
                            <svg class="w-32 h-32 text-brand-navy" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"/></svg>
                        </div>
    
                        <h2 class="text-xl font-extrabold text-brand-navy mb-6 flex items-center gap-2 relative z-10">
                        <svg class="w-5 h-5 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Disco
                    </h2>
                    
                    <form action="{{ route('songs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 relative z-10">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-brand-navy mb-2">UID de la Tarjeta NFC</label>
                            <input type="text" name="nfc_uid" required placeholder="Ej: 04 8B 21 42" class="w-full bg-[#EEEEEE] border border-gray-300 rounded-xl px-4 py-3 text-brand-navy font-medium placeholder-gray-400 focus:outline-none focus:border-brand-purple focus:ring-1 focus:ring-brand-purple transition shadow-inner">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-brand-navy mb-2">Cantante / Artista</label>
                            <input type="text" name="artist" required placeholder="Ej: Queen" class="w-full bg-[#EEEEEE] border border-gray-300 rounded-xl px-4 py-3 text-brand-navy font-medium placeholder-gray-400 focus:outline-none focus:border-brand-purple focus:ring-1 focus:ring-brand-purple transition shadow-inner">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-brand-navy mb-2">Título de la Canción</label>
                            <input type="text" name="title" required placeholder="Ej: Bohemian Rhapsody" class="w-full bg-[#EEEEEE] border border-gray-300 rounded-xl px-4 py-3 text-brand-navy font-medium placeholder-gray-400 focus:outline-none focus:border-brand-purple focus:ring-1 focus:ring-brand-purple transition shadow-inner">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-brand-navy mb-2">Archivo de Audio (MP3/WAV)</label>
                            <div class="relative group">
                                <input type="file" name="audio_file" accept=".mp3,.wav" required class="w-full bg-[#EEEEEE] border border-gray-300 rounded-xl px-4 py-3 text-brand-navy font-medium file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-[#DFDFDF] file:text-brand-navy hover:file:bg-gray-300 cursor-pointer transition shadow-inner">
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-4 bg-gradient-to-r from-brand-navy to-brand-purple hover:from-brand-purple hover:to-brand-navy text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-brand-navy/20 hover:shadow-brand-navy/40 transition-all transform hover:-translate-y-0.5 border border-brand-navy">
                            Grabar Disco Físico
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Vinyl Library -->
            <div class="lg:col-span-8">
                <div class="bg-brand-navy rounded-3xl p-8 shadow-[12px_12px_24px_rgba(0,0,0,0.15),-12px_-12px_24px_rgba(255,255,255,0.8)] min-h-full">
                    <h2 class="text-xl font-extrabold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Aquí puedes añadir tus propios círculos para el vinilo -->
                            <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" />
                            <circle cx="12" cy="10" r="9" stroke-width="2" stroke-linecap="round" />

                            
                            <!-- Funda / Caja -->
                            <rect x="2" y="13" width="20" height="9" rx="1.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="#1F6F5F" />
                        </svg>
                        Colección de Discos
                    </h2>

                    @if($songs->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-[#e8e4db]">
                            <svg class="w-20 h-20 mb-4 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"/></svg>
                            <p class="text-lg font-bold text-[#e8e4db]">Tu colección está vacía</p>
                            <p class="text-sm opacity-70 mt-2 text-[#e8e4db]">Sube tu primer disco para empezar a escuchar</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @php
                                $gradients = [
                                    'from-indigo-500 to-purple-600',
                                    'from-green-400 to-blue-500',
                                    'from-red-500 to-orange-500',
                                    'from-pink-500 to-rose-500',
                                    'from-yellow-500 to-orange-500',
                                    'from-teal-400 to-emerald-500',
                                    'from-cyan-500 to-blue-600',
                                    'from-fuchsia-500 to-pink-500',
                                    'from-violet-500 to-fuchsia-500',
                                    'from-emerald-400 to-cyan-500',
                                    'from-red-600 to-rose-400',
                                    'from-amber-500 to-yellow-400',
                                    'from-lime-400 to-green-500',
                                    'from-cyan-400 to-teal-500',
                                    'from-blue-600 to-indigo-400',
                                    'from-purple-500 to-violet-600',
                                    'from-rose-500 to-red-500',
                                    'from-orange-400 to-red-500',
                                    'from-green-500 to-teal-400',
                                    'from-slate-600 to-gray-500',
                                ];
                            @endphp
                            @foreach($songs as $song)
                                @php
                                    $gradient = $gradients[$song->id % count($gradients)];
                                @endphp
                                <div class="bg-white/5 rounded-2xl p-5 flex flex-col items-center group relative transition shadow-[inset_6px_6px_12px_rgba(0,0,0,0.35),inset_-6px_-6px_12px_rgba(255,255,255,0.05)] hover:shadow-[inset_8px_8px_16px_rgba(0,0,0,0.45),inset_-8px_-8px_16px_rgba(255,255,255,0.08)]">
                                    
                                    <!-- Botón de Eliminar (esquina) -->
                                    <form action="{{ route('songs.destroy', $song) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este disco?')" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition z-20">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-brand-orange hover:text-red-400 bg-brand-navy/80 hover:bg-red-500/20 rounded-full transition shadow-sm" title="Eliminar disco">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>

                                    <!-- Vinilo Giratorio 3D -->
                                    <div class="vinyl-container relative w-36 h-36 mb-5 mt-2 cursor-pointer" onclick="toggleTilt(this)" title="Clic para mantener inclinado">
                                        <div class="vinyl-wrapper w-full h-full relative rounded-full shadow-[0_15px_25px_rgba(0,0,0,0.6)]">
                                            
                                            <!-- Brazo del Tocadiscos (Diseño Realista) -->
                                            <div class="tonearm absolute -top-4 -right-4 w-[80px] h-[170px] origin-[56px_32px] drop-shadow-2xl pointer-events-none z-30">
                                                <!-- Contrapeso -->
                                                <div class="w-[16px] h-[14px] absolute top-[0px] right-[16px] bg-gradient-to-b from-gray-600 to-gray-900 rounded-sm shadow-md border-t border-gray-400 z-20"></div>
                                                <div class="w-[6px] h-[28px] absolute top-[4px] right-[21px] bg-gradient-to-r from-gray-300 to-gray-500 z-10"></div>
                                                
                                                <!-- Base del Pivote -->
                                                <div class="w-[44px] h-[44px] absolute top-[10px] right-[2px] rounded-full bg-gradient-to-b from-[#2a2a2a] to-[#111] shadow-lg border-2 border-[#444] z-10"></div>
                                                <div class="w-[24px] h-[24px] absolute top-[20px] right-[12px] rounded-full bg-gradient-to-br from-gray-200 to-gray-400 shadow-inner border border-gray-500 z-20"></div>
                                                <div class="w-[6px] h-[6px] absolute top-[29px] right-[21px] rounded-full bg-[#111] shadow-inner z-30"></div>
                                                <div class="w-[32px] h-[32px] absolute top-[16px] right-[8px] rounded-full border border-gray-600/30 z-20 pointer-events-none"></div>

                                                <!-- Brazo principal (tramo recto) -->
                                                <div class="w-[6px] h-[60px] absolute top-[32px] right-[21px] bg-gradient-to-r from-gray-200 via-gray-100 to-gray-400 shadow-md z-20 border-l border-white/60"></div>
                                                
                                                <!-- Brazo curvo (codo) y Cabezal -->
                                                <div class="w-[6px] h-[45px] absolute top-[90px] right-[21px] bg-gradient-to-r from-gray-200 via-gray-100 to-gray-400 rotate-[30deg] origin-top shadow-md z-20 border-l border-white/60 rounded-b-sm">
                                                    
                                                    <!-- Junta / Conector -->
                                                    <div class="w-[8px] h-[10px] absolute -bottom-[2px] -left-[1px] bg-gray-500 rounded-sm z-20"></div>

                                                    <!-- Headshell (Cabezal) -->
                                                    <div class="w-[14px] h-[26px] absolute -bottom-[26px] -left-[4px] bg-gradient-to-b from-[#333] to-[#111] rounded-sm shadow-xl border border-gray-600 z-30">
                                                        <!-- Palanquita (Handle) -->
                                                        <div class="w-[6px] h-[3px] bg-gray-300 absolute top-[6px] -right-[5px] rounded-r-sm shadow-sm"></div>
                                                        <!-- Líneas decorativas -->
                                                        <div class="w-[2px] h-[8px] bg-gray-500 absolute top-[4px] left-[3px]"></div>
                                                        <div class="w-[2px] h-[8px] bg-gray-500 absolute top-[4px] right-[3px]"></div>
                                                        <!-- Punta / Aguja -->
                                                        <div class="w-[4px] h-[4px] bg-red-500 absolute -bottom-[2px] left-[4px] rounded-full shadow-sm"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="vinyl-{{ $song->id }}" class="vinyl-record absolute inset-0 rounded-full bg-[#111] border-[4px] border-[#0a0a0a] flex items-center justify-center">
                                                <!-- Grooves (Surcos) -->
                                                <div class="absolute w-32 h-32 rounded-full border border-gray-700/50"></div>
                                                <div class="absolute w-28 h-28 rounded-full border border-gray-700/50"></div>
                                                <div class="absolute w-24 h-24 rounded-full border border-gray-700/50"></div>
                                                <div class="absolute w-20 h-20 rounded-full border border-gray-700/50"></div>
                                                <div class="absolute w-16 h-16 rounded-full border border-gray-700/50"></div>
                                                
                                                <!-- Luz de Reflejo -->
                                                <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-transparent via-white/10 to-transparent pointer-events-none"></div>

                                                <!-- Centro / Etiqueta -->
                                                <div class="w-16 h-16 rounded-full bg-gradient-to-br {{ $gradient }} flex flex-col items-center justify-center relative z-10 p-1 shadow-inner border border-white/20">
                                                    <!-- Agujero -->
                                                    <div class="w-2.5 h-2.5 bg-[#111] rounded-full absolute z-20 shadow-inner"></div>
                                                    
                                                    <!-- Nombre de la Cancion -->
                                                    <div class="absolute bottom-2.5 w-full flex justify-center px-0.5">
                                                        <span class="text-[0.42rem] font-bold text-white text-center leading-tight truncate w-full z-10 opacity-95" style="text-shadow: 0px 1px 2px rgba(0,0,0,0.9);">
                                                            {{ Str::limit($song->title, 17) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info de la Canción -->
                                    <div class="text-center mb-5 w-full">
                                        <h3 class="text-white font-extrabold text-lg truncate" title="{{ $song->title }}">{{ $song->title }}</h3>
                                        <p class="text-brand-orange text-sm truncate font-bold">{{ $song->artist }}</p>
                                        <p class="text-brand-orange/80 text-xs mt-1.5 font-mono bg-brand-navy/60 inline-block px-2 py-0.5 rounded-md font-semibold border border-brand-purple">UID: {{ $song->nfc_uid }}</p>
                                    </div>

                                    <!-- Reproductor -->
                                    <div class="w-full mt-auto">
                                        <audio id="audio-{{ $song->id }}" data-song-id="{{ $song->id }}" controls class="w-full h-9 outline-none [&::-webkit-media-controls-panel]:bg-brand-purple/70 [&::-webkit-media-controls-current-time-display]:text-white [&::-webkit-media-controls-time-remaining-display]:text-white border border-brand-purple shadow-sm rounded-full">
                                            <source src="{{ $song->audio_url }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    <script>
        function toggleTilt(element) {
            const wrapper = element.querySelector('.vinyl-wrapper');
            if (wrapper) {
                wrapper.classList.toggle('is-tilted');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const audios = document.querySelectorAll('audio');
            
            audios.forEach(audio => {
                const songId = audio.getAttribute('data-song-id');
                const vinyl = document.getElementById(`vinyl-${songId}`);

                if (vinyl) {
                    audio.addEventListener('play', () => {
                        audios.forEach(a => {
                            if(a !== audio) a.pause();
                        });
                        vinyl.classList.add('playing');
                        vinyl.closest('.vinyl-wrapper').classList.add('is-playing');
                    });

                    audio.addEventListener('pause', () => {
                        vinyl.classList.remove('playing');
                        vinyl.closest('.vinyl-wrapper').classList.remove('is-playing');
                    });

                    audio.addEventListener('ended', () => {
                        vinyl.classList.remove('playing');
                        vinyl.closest('.vinyl-wrapper').classList.remove('is-playing');
                    });
                }
            });
        });
    </script>
</body>
</html>
