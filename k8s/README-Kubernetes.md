# 🚀 Sistema de Tickets de Soporte en Kubernetes


Este proyecto implementa un sistema completo de tickets de soporte usando **Laravel** desplegado en **Kubernetes** con **Minikube**. Está diseñado específicamente para fines educativos y demostrar todos los conceptos fundamentales de Kubernetes.

## 📚 Tabla de Contenidos

1. [Arquitectura del Sistema](#-arquitectura-del-sistema)
2. [Componentes de Kubernetes](#-componentes-de-kubernetes)
3. [Estructura de Archivos](#-estructura-de-archivos)
4. [Guía de Despliegue](#-guía-de-despliegue)
5. [Conceptos Explicados](#-conceptos-explicados)
6. [Comandos Útiles](#-comandos-útiles)
7. [Troubleshooting](#-troubleshooting)
8. [Ejercicios Prácticos](#-ejercicios-prácticos)

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                        INTERNET                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                    INGRESS CONTROLLER                           │
│                    (NGINX en Minikube)                         │
│                                                                 │
│  Rules:                                                        │
│  - soporte-tickets.local → laravel-service:80                │
│  - app.soporte-tickets.local → laravel-service:80            │
│  - api.soporte-tickets.local → laravel-service:80            │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                    KUBERNETES CLUSTER                          │
│                                                                 │
│  ┌─────────────────┐                   ┌─────────────────┐     │
│  │  Laravel Pods   │◄──────────────────┤ MySQL Pod      │     │
│  │                 │                   │                 │     │
│  │  ┌───────────┐  │    Service        │  ┌───────────┐  │     │
│  │  │ Pod 1     │  │◄───Communication──┤  │ MySQL     │  │     │
│  │  │ Laravel   │  │                   │  │ Database  │  │     │
│  │  │ Apache    │  │                   │  └───────────┘  │     │
│  │  └───────────┘  │                   │        │        │     │
│  │                 │                   │        ▼        │     │
│  │  ┌───────────┐  │                   │ ┌───────────┐   │     │
│  │  │ Pod 2     │  │                   │ │    PV     │   │     │
│  │  │ Laravel   │  │                   │ │ (Storage) │   │     │
│  │  │ Apache    │  │                   │ │   2Gi     │   │     │
│  │  └───────────┘  │                   │ └───────────┘   │     │
│  │        │        │                   │                 │     │
│  │        ▼        │                   └─────────────────┘     │
│  │ ┌───────────┐   │                                           │
│  │ │    PV     │   │                                           │
│  │ │ (Storage) │   │                                           │
│  │ │   1Gi     │   │                                           │
│  │ └───────────┘   │                                           │
│  └─────────────────┘                                           │
│                                                                 │
│  Configuration Sources:                                         │
│  ├── ConfigMap (env vars)                                      │
│  └── Secrets (passwords, keys)                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Flujo de Datos

1. **Usuario** accede a `http://soporte-tickets.local`
2. **DNS local** resuelve a la IP de Minikube (`/etc/hosts`)
3. **Ingress Controller** recibe la petición y la dirige al **Service**
4. **Service** balancea la carga entre los **Pods** de Laravel
5. **Laravel Pods** procesan la petición y consultan **MySQL**
6. **MySQL** lee/escribe datos en **Persistent Volume**

## 🧩 Componentes de Kubernetes

### Jerarquía de Recursos

```
Cluster
└── Namespace (soporte-tickets)
    ├── ConfigMap (laravel-config)
    ├── Secrets (laravel-secrets, mysql-init-config)
    ├── PersistentVolumes
    │   ├── mysql-pv (2Gi)
    │   └── laravel-storage-pv (1Gi)
    ├── PersistentVolumeClaims
    │   ├── mysql-pvc
    │   └── laravel-storage-pvc
    ├── Deployments
    │   ├── mysql-deployment (1 replica)
    │   └── laravel-deployment (2 replicas)
    ├── Services
    │   ├── mysql-service (ClusterIP)
    │   ├── mysql-headless-service
    │   └── laravel-service (ClusterIP)
    ├── Ingress (soporte-tickets-ingress)
    └── HorizontalPodAutoscaler (laravel-hpa)
```

## 📁 Estructura de Archivos

```
k8s/
├── 01-namespace.yaml           # ← Namespace para aislamiento
├── 02-configmap.yaml          # ← Variables de configuración
├── 03-secret.yaml             # ← Datos sensibles (contraseñas)
├── 04-persistent-volume.yaml  # ← Almacenamiento persistente
├── 05-mysql.yaml              # ← Base de datos MySQL
├── 06-laravel-app.yaml        # ← Aplicación Laravel
├── 07-ingress.yaml            # ← Exposición externa
├── deploy-minikube.sh         # ← Script de despliegue
└── README-Kubernetes.md       # ← Esta documentación
```

## 🚀 Guía de Despliegue

### Prerequisitos

```bash
# 1. Instalar Minikube
curl -LO https://storage.googleapis.com/minikube/releases/latest/minikube-linux-amd64
sudo install minikube-linux-amd64 /usr/local/bin/minikube

# 2. Instalar kubectl
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
sudo install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl

# 3. Verificar instalación
minikube version
kubectl version --client
```

### Despliegue Automático

```bash
# Opción 1: Despliegue básico
./k8s/deploy-minikube.sh

# Opción 2: Construir imagen y desplegar
./k8s/deploy-minikube.sh --build

# Opción 3: Limpiar todo y desplegar desde cero
./k8s/deploy-minikube.sh --clean --build
```

### Despliegue Manual (Paso a Paso)

```bash
# 1. Iniciar Minikube
minikube start

# 2. Habilitar addons necesarios
minikube addons enable ingress
minikube addons enable metrics-server

# 3. Construir imagen (opcional)
eval $(minikube docker-env)
docker build -t dacadev/soporte-tickets:latest .

# 4. Aplicar manifiestos en orden
kubectl apply -f k8s/01-namespace.yaml
kubectl apply -f k8s/02-configmap.yaml
kubectl apply -f k8s/03-secret.yaml
kubectl apply -f k8s/04-persistent-volume.yaml
kubectl apply -f k8s/05-mysql.yaml

# Esperar a que MySQL esté listo
kubectl wait --for=condition=available --timeout=300s deployment/mysql-deployment -n soporte-tickets

# 5. Aplicar Laravel
kubectl apply -f k8s/06-laravel-app.yaml

# Esperar a que Laravel esté listo
kubectl wait --for=condition=available --timeout=300s deployment/laravel-deployment -n soporte-tickets

# 6. Aplicar Ingress
kubectl apply -f k8s/07-ingress.yaml

# 7. Configurar acceso local
echo "$(minikube ip) soporte-tickets.local" | sudo tee -a /etc/hosts
```

### Verificación del Despliegue

```bash
# Ver todos los recursos
kubectl get all -n soporte-tickets

# Ver el estado de los pods
kubectl get pods -n soporte-tickets -o wide

# Ver logs de la aplicación
kubectl logs -f deployment/laravel-deployment -n soporte-tickets

# Ver eventos del namespace
kubectl get events -n soporte-tickets --sort-by=.metadata.creationTimestamp
```

## 📖 Conceptos Explicados

### 1. Namespace 🏠

```yaml
apiVersion: v1
kind: Namespace
metadata:
  name: soporte-tickets
```

**¿Qué es?** Un namespace proporciona aislamiento lógico de recursos dentro del cluster.

**¿Por qué lo usamos?**
- Separar nuestra aplicación de otros proyectos
- Organizar recursos por proyecto/equipo
- Aplicar políticas de seguridad específicas
- Evitar conflictos de nombres

**Analogía:** Como tener diferentes carpetas en tu computadora para organizar archivos.

### 2. ConfigMap 📋

```yaml
apiVersion: v1
kind: ConfigMap
data:
  APP_NAME: "Sistema de Tickets de Soporte"
  DB_HOST: "mysql-service"
```

**¿Qué es?** Almacena datos de configuración no sensibles en pares clave-valor.

**¿Por qué lo usamos?**
- Separar configuración del código
- Cambiar configuración sin reconstruir imágenes
- Reutilizar configuración entre diferentes pods
- Facilitar diferentes entornos (dev, staging, prod)

**Analogía:** Como un archivo `.env` compartido entre múltiples aplicaciones.

### 3. Secret 🔐

```yaml
apiVersion: v1
kind: Secret
type: Opaque
data:
  DB_PASSWORD: bGFyYXZlbF9zZWN1cmVfMjAyNCE=  # base64
```

**¿Qué es?** Almacena datos sensibles como contraseñas, tokens, claves SSH.

**¿Por qué lo usamos?**
- Seguridad: datos codificados y cifrados
- Separar secretos de configuración general
- Control de acceso granular
- Auditabilidad de acceso a secretos

**Analogía:** Como una caja fuerte donde guardas información confidencial.

### 4. PersistentVolume (PV) & PersistentVolumeClaim (PVC) 💾

```yaml
# PV - Recurso del cluster
apiVersion: v1
kind: PersistentVolume
spec:
  capacity:
    storage: 2Gi
  hostPath:
    path: "/mnt/data/mysql"

---
# PVC - Solicitud de almacenamiento
apiVersion: v1
kind: PersistentVolumeClaim
spec:
  resources:
    requests:
      storage: 2Gi
```

**¿Qué son?**
- **PV:** Recurso de almacenamiento en el cluster
- **PVC:** Solicitud de almacenamiento por parte de un pod

**¿Por qué los usamos?**
- Persistencia: datos sobreviven al reinicio de pods
- Separación: el almacenamiento vive independiente del pod
- Flexibilidad: diferentes tipos de almacenamiento

**Analogía:** 
- PV = Disco duro disponible en la tienda
- PVC = Compra de un disco duro específico

### 5. Deployment 🚢

```yaml
apiVersion: apps/v1
kind: Deployment
spec:
  replicas: 2
  strategy:
    type: RollingUpdate
```

**¿Qué es?** Gestiona el despliegue y escalado de aplicaciones stateless.

**¿Por qué lo usamos?**
- **Rolling updates:** Actualizaciones sin downtime
- **Self-healing:** Reemplaza pods que fallan
- **Scaling:** Aumenta/disminuye réplicas según demanda
- **Rollback:** Vuelve a versiones anteriores

**Analogía:** Como un gerente que supervisa a trabajadores, los reemplaza si fallan, y contrata más cuando hay más trabajo.

### 6. Service 🌐

```yaml
apiVersion: v1
kind: Service
spec:
  type: ClusterIP
  selector:
    app: soporte-tickets
  ports:
  - port: 80
    targetPort: 80
```

**¿Qué es?** Expone un conjunto de pods como un servicio de red.

**¿Por qué lo usamos?**
- **Service discovery:** Nombres DNS estables para comunicación
- **Load balancing:** Distribuye tráfico entre pods
- **Desacoplamiento:** Frontend no necesita saber IPs de backend

**Analogía:** Como una recepcionista que dirige visitantes a diferentes oficinas disponibles.

### 7. Ingress 🚪

```yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
spec:
  rules:
  - host: soporte-tickets.local
    http:
      paths:
      - path: /
        backend:
          service:
            name: laravel-service
```

**¿Qué es?** Gestiona acceso externo HTTP/HTTPS a servicios del cluster.

**¿Por qué lo usamos?**
- **Single entry point:** Un punto de entrada para múltiples servicios
- **Routing inteligente:** Basado en hostnames y paths
- **SSL termination:** Gestión centralizada de certificados
- **Cost effective:** No necesita LoadBalancer por servicio

**Analogía:** Como la entrada principal de un edificio que dirige visitantes a diferentes oficinas.

### 8. HorizontalPodAutoscaler (HPA) 📈

```yaml
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
spec:
  minReplicas: 2
  maxReplicas: 5
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        averageUtilization: 70
```

**¿Qué es?** Escala automáticamente el número de pods basándose en métricas.

**¿Por qué lo usamos?**
- **Elasticidad:** Se adapta automáticamente a la demanda
- **Cost optimization:** Usa solo los recursos necesarios
- **Performance:** Mantiene tiempos de respuesta bajo carga
- **Automation:** Sin intervención manual

**Analogía:** Como contratar trabajadores temporales durante épocas ocupadas.

## 🛠️ Comandos Útiles

### Comandos de Consulta

```bash
# Ver todos los recursos en el namespace
kubectl get all -n soporte-tickets

# Ver pods con más detalles
kubectl get pods -n soporte-tickets -o wide

# Describir un pod específico
kubectl describe pod <pod-name> -n soporte-tickets

# Ver logs de un deployment
kubectl logs -f deployment/laravel-deployment -n soporte-tickets

# Ver logs de un pod específico
kubectl logs -f <pod-name> -n soporte-tickets

# Ver eventos del namespace
kubectl get events -n soporte-tickets --sort-by=.metadata.creationTimestamp

# Ver configuración de un recurso
kubectl get deployment laravel-deployment -n soporte-tickets -o yaml
```

### Comandos de Debugging

```bash
# Ejecutar shell en un pod
kubectl exec -it deployment/laravel-deployment -n soporte-tickets -- /bin/bash

# Ejecutar comando específico
kubectl exec deployment/laravel-deployment -n soporte-tickets -- php artisan --version

# Ver configuración de red
kubectl get svc,endpoints -n soporte-tickets

# Verificar PVs y PVCs
kubectl get pv,pvc -n soporte-tickets

# Verificar Ingress
kubectl get ingress -n soporte-tickets
kubectl describe ingress soporte-tickets-ingress -n soporte-tickets
```

### Comandos de Gestión

```bash
# Escalar un deployment
kubectl scale deployment laravel-deployment --replicas=3 -n soporte-tickets

# Actualizar imagen de un deployment
kubectl set image deployment/laravel-deployment laravel-app=dacadev/soporte-tickets:v2 -n soporte-tickets

# Ver estado de rollout
kubectl rollout status deployment/laravel-deployment -n soporte-tickets

# Hacer rollback
kubectl rollout undo deployment/laravel-deployment -n soporte-tickets

# Reiniciar un deployment
kubectl rollout restart deployment/laravel-deployment -n soporte-tickets
```

### Comandos de Port Forwarding

```bash
# Acceso directo a MySQL
kubectl port-forward svc/mysql-service 3306:3306 -n soporte-tickets

# Acceso directo a Laravel
kubectl port-forward svc/laravel-service 8080:80 -n soporte-tickets

# Acceso directo a un pod específico
kubectl port-forward pod/<pod-name> 8080:80 -n soporte-tickets
```

## 🔧 Troubleshooting

### Problemas Comunes

#### 1. Pods en estado `Pending`

```bash
# Verificar eventos
kubectl describe pod <pod-name> -n soporte-tickets

# Posibles causas:
# - Recursos insuficientes
# - PVC no disponible
# - Node selector no coincide
```

#### 2. Pods en estado `CrashLoopBackOff`

```bash
# Ver logs del pod
kubectl logs <pod-name> -n soporte-tickets --previous

# Posibles causas:
# - Error en la aplicación
# - Configuración incorrecta
# - Dependencias no disponibles (MySQL)
```

#### 3. `ImagePullBackOff`

```bash
# Verificar imagen
kubectl describe pod <pod-name> -n soporte-tickets

# Soluciones:
# - Construir imagen en Minikube: eval $(minikube docker-env)
# - Verificar nombre de imagen
# - Configurar imagePullPolicy: IfNotPresent
```

#### 4. PVC en estado `Pending`

```bash
# Verificar PV disponibles
kubectl get pv

# Verificar detalles del PVC
kubectl describe pvc mysql-pvc -n soporte-tickets

# Posibles causas:
# - No hay PV que coincida con los requisitos
# - StorageClass no existe
```

#### 5. Ingress no funciona

```bash
# Verificar addon de Ingress
minikube addons list | grep ingress

# Habilitar si no está activo
minikube addons enable ingress

# Verificar configuración
kubectl describe ingress soporte-tickets-ingress -n soporte-tickets

# Verificar /etc/hosts
grep soporte-tickets /etc/hosts
```

#### 6. Base de datos no conecta

```bash
# Verificar que MySQL esté ejecutándose
kubectl get pods -n soporte-tickets | grep mysql

# Verificar logs de MySQL
kubectl logs deployment/mysql-deployment -n soporte-tickets

# Verificar configuración de conexión
kubectl exec deployment/laravel-deployment -n soporte-tickets -- env | grep DB_
```

### Scripts de Debugging

```bash
# Script para verificar conectividad
kubectl run debug --image=busybox -n soporte-tickets --rm -it --restart=Never -- sh

# Dentro del pod de debug:
nslookup mysql-service
wget -qO- http://laravel-service
```

## 🎯 Ejercicios Prácticos

### Ejercicio 1: Escalar la Aplicación

**Objetivo:** Aprender sobre escalado horizontal

```bash
# 1. Ver número actual de réplicas
kubectl get deployment laravel-deployment -n soporte-tickets

# 2. Escalar a 4 réplicas
kubectl scale deployment laravel-deployment --replicas=4 -n soporte-tickets

# 3. Observar cómo se crean nuevos pods
kubectl get pods -n soporte-tickets -w

# 4. Verificar balanceo de carga
curl -H "Host: soporte-tickets.local" http://$(minikube ip)
```

**Preguntas:**
- ¿Cuánto tiempo tomó crear los nuevos pods?
- ¿Cómo distribuye Kubernetes los pods entre nodos?
- ¿Qué pasa con las conexiones existentes durante el escalado?

### Ejercicio 2: Rolling Update

**Objetivo:** Entender las actualizaciones sin downtime

```bash
# 1. Crear nueva versión (cambiar tag en deployment)
kubectl set image deployment/laravel-deployment laravel-app=dacadev/soporte-tickets:v2 -n soporte-tickets

# 2. Observar el rollout
kubectl rollout status deployment/laravel-deployment -n soporte-tickets

# 3. Ver historial de rollouts
kubectl rollout history deployment/laravel-deployment -n soporte-tickets

# 4. Hacer rollback si es necesario
kubectl rollout undo deployment/laravel-deployment -n soporte-tickets
```

### Ejercicio 3: ConfigMap Updates

**Objetivo:** Cambiar configuración sin reconstruir imágenes

```bash
# 1. Modificar ConfigMap
kubectl edit configmap laravel-config -n soporte-tickets

# 2. Reiniciar deployment para aplicar cambios
kubectl rollout restart deployment/laravel-deployment -n soporte-tickets

# 3. Verificar nuevos valores
kubectl exec deployment/laravel-deployment -n soporte-tickets -- env | grep APP_
```

### Ejercicio 4: Monitoring y Logging

**Objetivo:** Aprender a monitorear aplicaciones

```bash
# 1. Ver métricas de recursos
kubectl top pods -n soporte-tickets

# 2. Ver logs en tiempo real
kubectl logs -f deployment/laravel-deployment -n soporte-tickets

# 3. Filtrar logs
kubectl logs deployment/laravel-deployment -n soporte-tickets | grep ERROR

# 4. Ver métricas del HPA
kubectl get hpa -n soporte-tickets
```

### Ejercicio 5: Networking

**Objetivo:** Entender la comunicación entre servicios

```bash
# 1. Crear pod temporal para testing
kubectl run test-pod --image=curlimages/curl -n soporte-tickets --rm -it --restart=Never -- sh

# Dentro del pod:
# 2. Probar resolución DNS
nslookup mysql-service
nslookup laravel-service

# 3. Probar conectividad HTTP
curl http://laravel-service

# 4. Probar conectividad MySQL
nc -zv mysql-service 3306
```

### Ejercicio 6: Persistent Storage

**Objetivo:** Entender almacenamiento persistente

```bash
# 1. Ver volúmenes actuales
kubectl get pv,pvc -n soporte-tickets

# 2. Eliminar pod de MySQL (los datos deben persistir)
kubectl delete pod -l component=database -n soporte-tickets

# 3. Verificar que los datos persisten
kubectl exec deployment/mysql-deployment -n soporte-tickets -- mysql -u laravel_user -plaravel_secure_2024! -e "SHOW DATABASES;"
```

## 📚 Recursos Adicionales

### Documentación Oficial
- [Kubernetes Documentation](https://kubernetes.io/docs/)
- [Minikube Documentation](https://minikube.sigs.k8s.io/docs/)
- [kubectl Cheat Sheet](https://kubernetes.io/docs/reference/kubectl/cheatsheet/)

### Herramientas Útiles
- [K9s](https://k9scli.io/) - UI en terminal para Kubernetes
- [Lens](https://k8slens.dev/) - IDE para Kubernetes
- [Helm](https://helm.sh/) - Package manager para Kubernetes

### Comandos de Limpieza

```bash
# Eliminar toda la aplicación
kubectl delete namespace soporte-ticketss

# Limpiar volúmenes persistentes
kubectl delete pv mysql-pv laravel-storage-pv

# Parar Minikube
minikube stop

# Eliminar cluster de Minikube
minikube delete

```

