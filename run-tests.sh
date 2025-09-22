#!/bin/bash

# Script para ejecutar las pruebas automáticas del sistema de tickets
# 
# Este script facilita la ejecución de las pruebas con diferentes opciones:
# - Ejecutar todas las pruebas
# - Ejecutar solo pruebas unitarias
# - Ejecutar solo pruebas de funcionalidad
# - Ejecutar pruebas con cobertura de código
# - Ejecutar pruebas en modo verbose

echo "🧪 Ejecutando pruebas automáticas del sistema de tickets de soporte"
echo "=================================================================="

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto Laravel."
    exit 1
fi

# Verificar que las dependencias están instaladas
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias..."
    composer install
fi

# Función para mostrar ayuda
show_help() {
    echo "Uso: $0 [OPCIÓN]"
    echo ""
    echo "Opciones:"
    echo "  all, a     Ejecutar todas las pruebas (por defecto)"
    echo "  unit, u     Ejecutar solo pruebas unitarias"
    echo "  feature, f  Ejecutar solo pruebas de funcionalidad"
    echo "  coverage, c Ejecutar pruebas con cobertura de código"
    echo "  verbose, v  Ejecutar pruebas en modo verbose"
    echo "  help, h     Mostrar esta ayuda"
    echo ""
    echo "Ejemplos:"
    echo "  $0              # Ejecutar todas las pruebas"
    echo "  $0 unit          # Solo pruebas unitarias"
    echo "  $0 feature       # Solo pruebas de funcionalidad"
    echo "  $0 coverage      # Con cobertura de código"
    echo "  $0 verbose       # Modo verbose"
}

# Función para ejecutar pruebas con diferentes opciones
run_tests() {
    local test_type="$1"
    local verbose_flag=""
    local coverage_flag=""
    
    # Configurar flags según los parámetros
    if [[ "$*" == *"verbose"* ]] || [[ "$*" == *"v"* ]]; then
        verbose_flag="--verbose"
    fi
    
    if [[ "$*" == *"coverage"* ]] || [[ "$*" == *"c"* ]]; then
        coverage_flag="--coverage"
    fi
    
    # Ejecutar pruebas según el tipo
    case "$test_type" in
        "unit"|"u")
            echo "🔬 Ejecutando pruebas unitarias..."
            ./vendor/bin/phpunit tests/Unit $verbose_flag $coverage_flag
            ;;
        "feature"|"f")
            echo "🎯 Ejecutando pruebas de funcionalidad..."
            ./vendor/bin/phpunit tests/Feature $verbose_flag $coverage_flag
            ;;
        "all"|"a"|"")
            echo "🚀 Ejecutando todas las pruebas..."
            ./vendor/bin/phpunit $verbose_flag $coverage_flag
            ;;
        *)
            echo "❌ Opción no válida: $test_type"
            show_help
            exit 1
            ;;
    esac
}

# Procesar argumentos
case "${1:-all}" in
    "help"|"h")
        show_help
        exit 0
        ;;
    *)
        run_tests "$@"
        ;;
esac

echo ""
echo "✅ Pruebas completadas"
echo "📊 Para más información sobre las pruebas, consulta la documentación en tests/README.md"
