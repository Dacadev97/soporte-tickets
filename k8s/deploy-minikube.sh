#!/bin/bash

# ==============================================================================
# SCRIPT DE DESPLIEGUE PARA MINIKUBE - Sistema de Tickets de Soporte
# ==============================================================================
#
# Este script automatiza todo el proceso de despliegue de la aplicación
# Laravel de tickets de soporte en un cluster de Minikube.
#
# Requisitos previos:
# 1. Minikube instalado y funcionando
# 2. kubectl configurado para usar Minikube
# 3. Docker para construir la imagen de la aplicación
#
# Uso:
#   ./deploy-minikube.sh [opciones]
#
# Opciones:
#   --build     : Construir la imagen Docker antes del despliegue
#   --clean     : Limpiar todos los recursos antes del despliegue
#   --help      : Mostrar esta ayuda
#
# ==============================================================================

set -e  # Salir si cualquier comando falla

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables de configuración
PROJECT_NAME="soporte-tickets"
NAMESPACE="soporte-tickets"
IMAGE_NAME="dacadev/soporte-tickets"
IMAGE_TAG="latest"
MINIKUBE_PROFILE="minikube"

# Función para imprimir mensajes con colores
print_step() {
    echo -e "${BLUE}[PASO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✅]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[⚠️]${NC} $1"
}

print_error() {
    echo -e "${RED}[❌]${NC} $1"
}

print_info() {
    echo -e "${BLUE}[ℹ️]${NC} $1"
}

# Función para mostrar ayuda
show_help() {
    cat << EOF
🚀 SCRIPT DE DESPLIEGUE PARA MINIKUBE
Sistema de Tickets de Soporte Laravel

DESCRIPCIÓN:
    Este script automatiza el despliegue completo de la aplicación
    en un cluster de Minikube, incluyendo la configuración de todos
    los recursos necesarios.

USO:
    $0 [opciones]

OPCIONES:
    --build     Construir la imagen Docker antes del despliegue
    --clean     Limpiar todos los recursos existentes antes del despliegue
    --help      Mostrar esta ayuda y salir

EJEMPLOS:
    $0                    # Despliegue básico
    $0 --build           # Construir imagen y desplegar
    $0 --clean --build   # Limpiar, construir y desplegar

REQUISITOS:
    - Minikube instalado y ejecutándose
    - kubectl configurado
    - Docker (si se usa --build)

EOF
}

# Función para verificar requisitos
check_requirements() {
    print_step "Verificando requisitos..."
    
    # Verificar Minikube
    if ! command -v minikube &> /dev/null; then
        print_error "Minikube no está instalado"
        exit 1
    fi
    
    # Verificar kubectl
    if ! command -v kubectl &> /dev/null; then
        print_error "kubectl no está instalado"
        exit 1
    fi
    
    # Verificar si Minikube está ejecutándose
    if ! minikube status --profile="$MINIKUBE_PROFILE" &> /dev/null; then
        print_warning "Minikube no está ejecutándose. Iniciando..."
        minikube start --profile="$MINIKUBE_PROFILE"
    fi
    
    # Configurar kubectl para usar Minikube
    minikube update-context --profile="$MINIKUBE_PROFILE"
    
    print_success "Todos los requisitos están satisfechos"
}

# Función para construir la imagen Docker
build_image() {
    print_step "Construyendo imagen Docker..."
    
    # Cambiar al directorio del proyecto
    cd "$(dirname "$0")/.."
    
    # Configurar Docker para usar el daemon de Minikube
    eval $(minikube docker-env --profile="$MINIKUBE_PROFILE")
    
    # Construir la imagen
    docker build -t "$IMAGE_NAME:$IMAGE_TAG" .
    
    print_success "Imagen Docker construida: $IMAGE_NAME:$IMAGE_TAG"
}

# Función para habilitar addons necesarios
enable_addons() {
    print_step "Habilitando addons de Minikube..."
    
    # Habilitar Ingress Controller (NGINX)
    minikube addons enable ingress --profile="$MINIKUBE_PROFILE"
    
    # Habilitar Metrics Server para HPA
    minikube addons enable metrics-server --profile="$MINIKUBE_PROFILE"
    
    # Habilitar Dashboard (opcional)
    minikube addons enable dashboard --profile="$MINIKUBE_PROFILE"
    
    print_success "Addons habilitados"
}

# Función para limpiar recursos existentes
clean_resources() {
    print_step "Limpiando recursos existentes..."
    
    # Eliminar namespace (esto elimina todos los recursos dentro)
    kubectl delete namespace "$NAMESPACE" --ignore-not-found=true
    
    # Esperar a que el namespace sea completamente eliminado
    print_info "Esperando a que el namespace sea eliminado..."
    kubectl wait --for=delete namespace/"$NAMESPACE" --timeout=60s || true
    
    print_success "Recursos limpiados"
}

# Función para aplicar manifiestos de Kubernetes
apply_manifests() {
    print_step "Aplicando manifiestos de Kubernetes..."
    
    # Cambiar al directorio k8s
    K8S_DIR="$(dirname "$0")"
    cd "$K8S_DIR"
    
    # Aplicar los manifiestos en orden
    print_info "Aplicando namespace..."
    kubectl apply -f 01-namespace.yaml
    
    print_info "Aplicando ConfigMap..."
    kubectl apply -f 02-configmap.yaml
    
    print_info "Aplicando Secrets..."
    kubectl apply -f 03-secret.yaml
    
    print_info "Aplicando volúmenes persistentes..."
    kubectl apply -f 04-persistent-volume.yaml
    
    print_info "Aplicando deployment de MySQL..."
    kubectl apply -f 05-mysql.yaml
    
    # Esperar a que MySQL esté listo
    print_info "Esperando a que MySQL esté listo..."
    kubectl wait --for=condition=available --timeout=300s deployment/mysql-deployment -n "$NAMESPACE"
    
    print_info "Aplicando deployment de Laravel..."
    kubectl apply -f 06-laravel-app.yaml
    
    # Esperar a que Laravel esté listo
    print_info "Esperando a que Laravel esté listo..."
    kubectl wait --for=condition=available --timeout=300s deployment/laravel-deployment -n "$NAMESPACE"
    
    print_info "Aplicando Ingress..."
    kubectl apply -f 07-ingress.yaml
    
    print_success "Todos los manifiestos aplicados correctamente"
}

# Función para configurar acceso local
setup_local_access() {
    print_step "Configurando acceso local..."
    
    # Obtener la IP de Minikube
    MINIKUBE_IP=$(minikube ip --profile="$MINIKUBE_PROFILE")
    
    # Agregar entrada al /etc/hosts si no existe
    HOSTS_ENTRY="$MINIKUBE_IP soporte-tickets.local app.soporte-tickets.local api.soporte-tickets.local admin.soporte-tickets.local"
    
    if ! grep -q "soporte-tickets.local" /etc/hosts; then
        print_info "Agregando entrada a /etc/hosts..."
        echo "$HOSTS_ENTRY" | sudo tee -a /etc/hosts > /dev/null
        print_success "Entrada agregada a /etc/hosts"
    else
        print_warning "La entrada ya existe en /etc/hosts, verificar manualmente"
    fi
    
    print_info "IP de Minikube: $MINIKUBE_IP"
    print_info "URLs disponibles:"
    print_info "  - http://soporte-tickets.local"
    print_info "  - http://app.soporte-tickets.local"
    print_info "  - http://api.soporte-tickets.local"
    print_info "  - http://admin.soporte-tickets.local"
}

# Función para mostrar el estado del despliegue
show_deployment_status() {
    print_step "Estado del despliegue:"
    
    echo ""
    print_info "=== PODS ==="
    kubectl get pods -n "$NAMESPACE" -o wide
    
    echo ""
    print_info "=== SERVICIOS ==="
    kubectl get services -n "$NAMESPACE"
    
    echo ""
    print_info "=== INGRESS ==="
    kubectl get ingress -n "$NAMESPACE"
    
    echo ""
    print_info "=== VOLÚMENES ==="
    kubectl get pv,pvc -n "$NAMESPACE"
    
    echo ""
    print_info "=== HPA (Horizontal Pod Autoscaler) ==="
    kubectl get hpa -n "$NAMESPACE" || print_warning "HPA no disponible (requiere metrics-server)"
}

# Función para mostrar logs útiles
show_logs() {
    print_step "Comandos útiles para debugging:"
    
    echo ""
    print_info "Ver logs de la aplicación Laravel:"
    echo "kubectl logs -f deployment/laravel-deployment -n $NAMESPACE"
    
    echo ""
    print_info "Ver logs de MySQL:"
    echo "kubectl logs -f deployment/mysql-deployment -n $NAMESPACE"
    
    echo ""
    print_info "Acceder al pod de Laravel:"
    echo "kubectl exec -it deployment/laravel-deployment -n $NAMESPACE -- /bin/bash"
    
    echo ""
    print_info "Acceder al pod de MySQL:"
    echo "kubectl exec -it deployment/mysql-deployment -n $NAMESPACE -- mysql -u laravel_user -p soporte_tickets"
    
    echo ""
    print_info "Ver eventos del namespace:"
    echo "kubectl get events -n $NAMESPACE --sort-by=.metadata.creationTimestamp"
    
    echo ""
    print_info "Dashboard de Minikube:"
    echo "minikube dashboard --profile=$MINIKUBE_PROFILE"
}

# Función principal
main() {
    local build_image_flag=false
    local clean_resources_flag=false
    
    # Procesar argumentos
    while [[ $# -gt 0 ]]; do
        case $1 in
            --build)
                build_image_flag=true
                shift
                ;;
            --clean)
                clean_resources_flag=true
                shift
                ;;
            --help)
                show_help
                exit 0
                ;;
            *)
                print_error "Opción desconocida: $1"
                show_help
                exit 1
                ;;
        esac
    done
    
    echo "🚀 INICIANDO DESPLIEGUE DE SISTEMA DE TICKETS DE SOPORTE"
    echo "=================================================="
    
    # Ejecutar pasos del despliegue
    check_requirements
    enable_addons
    
    if [ "$clean_resources_flag" = true ]; then
        clean_resources
    fi
    
    if [ "$build_image_flag" = true ]; then
        build_image
    fi
    
    apply_manifests
    setup_local_access
    
    # Esperar un poco para que todo se estabilice
    print_step "Esperando a que el sistema se estabilice..."
    sleep 10
    
    show_deployment_status
    show_logs
    
    echo ""
    echo "🎉 ¡DESPLIEGUE COMPLETADO!"
    echo "========================="
    print_success "La aplicación está disponible en: http://soporte-tickets.local"
    print_info "Para ver el estado en tiempo real: kubectl get all -n $NAMESPACE"
    print_info "Para ver logs: kubectl logs -f deployment/laravel-deployment -n $NAMESPACE"
}

# Ejecutar función principal con todos los argumentos
main "$@"
