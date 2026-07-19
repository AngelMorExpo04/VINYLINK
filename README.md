# VINYLINK 🎵

VINYLINK es un proyecto diseñado para revivir la magia y la experiencia táctil de los tocadiscos físicos, pero modernizada y conectada al mundo digital mediante tecnología NFC y almacenamiento en la nube. 

## ¿Para qué está creado?
La idea principal detrás de VINYLINK es que puedas tener "vinilos" físicos (discos decorativos con una pegatina NFC en el centro) que al posarlos sobre un tocadiscos modificado (controlado por una Raspberry Pi o dispositivo similar con lector NFC), comiencen a sonar automáticamente por un altavoz Bluetooth.

VINYLINK es el **panel de control web** de todo este sistema. Te permite registrar tus discos, subir la música y gestionar toda tu colección.

## ¿Cómo funciona?

El proyecto se divide en el sistema físico (lector NFC y reproductor de audio) y esta aplicación web. La web de VINYLINK funciona de la siguiente manera:

1. **Interfaz Neumórfica:** Una interfaz web moderna, minimalista y con diseño neumórfico (simulando huecos y relieves físicos), construida con Laravel y Tailwind CSS.
2. **Registro de Discos:** A través del panel, puedes registrar un nuevo disco. Defines el UID (Identificador Único) de la tarjeta NFC que vas a pegar en el vinilo físico, el cantante, la canción y subes el archivo de audio.
3. **Almacenamiento en la Nube:** Para no sobrecargar la base de datos ni el servidor, los pesados archivos de audio (MP3/WAV) se suben directamente a un *bucket* en **Backblaze B2**. La base de datos de VINYLINK solo guarda el enlace directo a esa canción.
4. **Reproducción Física:** Cuando posas el vinilo en el tocadiscos, el lector lee el código NFC, consulta a la base de datos de VINYLINK qué canción le corresponde a ese código, recibe el enlace de Backblaze y lo empieza a reproducir por el altavoz.

## Tecnologías Utilizadas

- **Backend:** Laravel (PHP)
- **Frontend:** Blade, Tailwind CSS (con diseño interactivo y neumorfismo avanzado)
- **Base de Datos:** MySQL / SQLite
- **Almacenamiento en la nube:** Backblaze B2 (vía el driver de S3 de Flysystem)

---
*Nota: Este repositorio contiene la parte del software (Aplicación Web). Las credenciales de base de datos y de conexión al almacenamiento en la nube (Backblaze B2) deben configurarse localmente a través de un archivo `.env` el cual se omite por seguridad.*
