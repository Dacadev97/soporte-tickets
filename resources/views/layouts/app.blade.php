{{-- 
    Layout Principal - Sistema de Tickets de Soporte
    ===============================================
    
    Este es el layout base que se extiende en todas las vistas del sistema.
    Proporciona:
    - Estructura HTML5 completa con metadatos
    - Navegación responsive con Bootstrap 5
    - Sistema de alertas para mensajes de éxito/error
    - Footer con información de copyright
    - Integración de Font Awesome para iconos
    
    Características:
    - Diseño responsive que se adapta a móviles y desktop
    - Navegación principal con enlaces a tickets y creación
    - Sistema de mensajes flash para feedback al usuario
    - Validación de errores automática
    - SEO optimizado con meta tags apropiados
--}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gestión de tickets de soporte técnico">
    <meta name="keywords" content="tickets, soporte, sistema, gestión">
    <meta name="author" content="Sistema de Tickets">
    
    {{-- Título dinámico que se puede personalizar en cada vista --}}
    <title>@yield('title', 'Sistema de Tickets')</title>
    
    {{-- Bootstrap 5 para el diseño responsive y componentes UI --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Font Awesome para iconos profesionales --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    {{-- Barra de navegación principal --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            {{-- Logo y nombre del sistema --}}
            <a class="navbar-brand" href="{{ route('tickets.index') }}">
                <i class="fas fa-ticket-alt me-2"></i>Sistema de Tickets
            </a>
            
            {{-- Botón hamburguesa para móviles --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            {{-- Menú de navegación --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- Enlace a la lista de tickets --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tickets.index') }}">
                            <i class="fas fa-list me-1"></i>Tickets
                        </a>
                    </li>
                    {{-- Enlace para crear nuevo ticket --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tickets.create') }}">
                            <i class="fas fa-plus me-1"></i>Nuevo Ticket
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Contenido principal de la aplicación --}}
    <main class="container my-4">
        {{-- Sistema de alertas para mensajes flash --}}
        
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Mensaje de error --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Errores de validación del formulario --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Contenido específico de cada vista --}}
        @yield('content')
    </main>

    {{-- Footer de la aplicación --}}
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Sistema de Tickets. Todos los derechos reservados.</p>
        </div>
    </footer>

    {{-- Scripts de Bootstrap para funcionalidad JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
