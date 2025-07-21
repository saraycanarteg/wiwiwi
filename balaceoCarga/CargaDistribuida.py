import threading
import time
import random
import queue
from collections import deque
from datetime import datetime
import json

class HTTPRequest:
    """Representa una petición HTTP"""
    def __init__(self, request_id, method, path, client_ip, min_time, max_time):
        self.request_id = request_id
        self.method = method
        self.path = path
        self.client_ip = client_ip
        self.timestamp = datetime.now()
        self.min_processing_time = min_time
        self.max_processing_time = max_time
        self.processing_time = random.uniform(min_time, max_time)
        self.response_status = None
        self.completed_at = None
        self.start_processing_time = None
        self.end_processing_time = None
        self.failed = False
    
    def __str__(self):
        return f"Request {self.request_id}: {self.method} {self.path}"

class WebServerWorkStealing:
    """Sistema de work stealing para servidores web con 4 servidores de diferentes capacidades"""
    
    def __init__(self):
        self.num_servers = 4
        self.servers = []
        self.request_queues = [deque() for _ in range(self.num_servers)]
        self.locks = [threading.Lock() for _ in range(self.num_servers)]
        self.running = True
        
        # Métricas globales
        self.processed_requests = 0
        self.failed_requests = 0
        self.total_response_time = 0
        self.stolen_requests = 0
        self.steal_operations = []
        self.request_processing_times = []
        self.metrics_lock = threading.Lock()
        self.start_time = None
        self.end_time = None
        self.actual_start_time = None  # Tiempo real cuando inicia el procesamiento
        self.actual_end_time = None    # Tiempo real cuando termina el procesamiento
        
        # Métricas por servidor
        self.server_metrics = [
            {
                'processed_requests': 0,
                'failed_requests': 0,
                'total_response_time': 0.0,
                'response_times': [],
                'start_time': None,
                'end_time': None
            } for _ in range(self.num_servers)
        ]
        
        # Configuración de servidores con diferentes capacidades
        self.server_configs = [
            {"name": "WebServer-Light", "cpu_cores": 2, "memory": "4GB", "capacity_factor": 1.5, "port": 8080},
            {"name": "WebServer-Medium", "cpu_cores": 4, "memory": "6GB", "capacity_factor": 1.2, "port": 8081},
            {"name": "WebServer-Heavy", "cpu_cores": 8, "memory": "8GB", "capacity_factor": 0.8, "port": 8082},
            {"name": "WebServer-Premium", "cpu_cores": 6, "memory": "10GB", "capacity_factor": 1.0, "port": 8083}
        ]
    
    def add_request(self, server_id, request):
        """Añade una petición a la cola de un servidor específico"""
        with self.locks[server_id]:
            self.request_queues[server_id].append(request)
    
    def get_local_request(self, server_id):
        """Obtiene una petición de la cola local del servidor"""
        with self.locks[server_id]:
            if self.request_queues[server_id]:
                return self.request_queues[server_id].pop()  # LIFO - más reciente primero
        return None
    
    def steal_request(self, thief_server_id):
        """Intenta robar una petición de otros servidores"""
        # Crear lista de víctimas potenciales
        victims = [i for i in range(self.num_servers) if i != thief_server_id]
        
        # Priorizar servidores con más carga
        victims.sort(key=lambda x: len(self.request_queues[x]), reverse=True)
        
        for victim_id in victims:
            with self.locks[victim_id]:
                if len(self.request_queues[victim_id]) > 1:  # Solo robar si hay más de 1 petición
                    stolen_request = self.request_queues[victim_id].popleft()  # FIFO - más antigua
                    
                    steal_time = time.time()
                    with self.metrics_lock:
                        self.stolen_requests += 1
                        self.steal_operations.append({
                            'time': steal_time,
                            'thief': self.server_configs[thief_server_id]['name'],
                            'victim': self.server_configs[victim_id]['name'],
                            'request_id': stolen_request.request_id
                        })
                    
                    print(f"🔄 {self.server_configs[thief_server_id]['name']} robó {stolen_request.request_id} de {self.server_configs[victim_id]['name']}")
                    return stolen_request
        
        return None
    
    def process_request(self, server_id, request):
        """Simula el procesamiento de una petición HTTP con capacidad diferenciada"""
        server_name = self.server_configs[server_id]['name']
        capacity_factor = self.server_configs[server_id]['capacity_factor']
        
        request.start_processing_time = time.time()
        
        # Marcar el primer procesamiento como inicio global
        with self.metrics_lock:
            if self.actual_start_time is None:
                self.actual_start_time = request.start_processing_time
        
        # Simular fallo ocasional (5% de probabilidad)
        if random.random() < 0.05:
            request.failed = True
            request.response_status = random.choice([500, 503, 504])
            request.completed_at = datetime.now()
            request.end_processing_time = time.time()
            
            # Actualizar métricas de fallo
            with self.metrics_lock:
                self.failed_requests += 1
                self.server_metrics[server_id]['failed_requests'] += 1
                self.actual_end_time = request.end_processing_time  # Actualizar tiempo final
            
            print(f"❌ {server_name} falló {request.request_id} - Status: {request.response_status}")
            return
        
        # Ajustar tiempo de procesamiento según capacidad del servidor
        adjusted_processing_time = request.processing_time * capacity_factor
        time.sleep(adjusted_processing_time)
        
        # Simular respuesta exitosa
        request.response_status = random.choice([200, 201, 204])
        request.completed_at = datetime.now()
        request.end_processing_time = time.time()
        
        actual_processing_time = request.end_processing_time - request.start_processing_time
        
        # Actualizar métricas
        with self.metrics_lock:
            self.processed_requests += 1
            self.total_response_time += actual_processing_time
            self.actual_end_time = request.end_processing_time  # Actualizar tiempo final
            
            # Métricas por servidor
            self.server_metrics[server_id]['processed_requests'] += 1
            self.server_metrics[server_id]['total_response_time'] += actual_processing_time
            self.server_metrics[server_id]['response_times'].append(actual_processing_time)
            
            self.request_processing_times.append({
                'request_id': request.request_id,
                'server': server_name,
                'server_id': server_id,
                'processing_time': actual_processing_time,
                'start_time': request.start_processing_time,
                'end_time': request.end_processing_time
            })
        
        print(f"✅ {server_name} completó {request.request_id} - Tiempo: {actual_processing_time:.3f}s")
    
    def server_worker(self, server_id):
        """Función principal de cada servidor web"""
        server_name = self.server_configs[server_id]['name']
        print(f"🟢 {server_name} iniciado")
        
        # Marcar tiempo de inicio del servidor
        self.server_metrics[server_id]['start_time'] = time.time()
        
        while self.running:
            request = None
            
            # 1. Intentar procesar petición local
            request = self.get_local_request(server_id)
            
            # 2. Si no hay peticiones locales, intentar robar
            if request is None:
                request = self.steal_request(server_id)
            
            # 3. Si encontró una petición, procesarla
            if request:
                self.process_request(server_id, request)
            else:
                # No hay trabajo, esperar un poco
                time.sleep(0.05)
        
        # Marcar tiempo de fin del servidor
        self.server_metrics[server_id]['end_time'] = time.time()
        print(f"🔴 {server_name} detenido")
    
    def start_servers(self):
        """Inicia todos los servidores"""
        print("🌐 Iniciando sistema de servidores web con Work Stealing...\n")
        self.start_time = time.time()
        
        self.servers = []
        for i in range(self.num_servers):
            server_thread = threading.Thread(target=self.server_worker, args=(i,))
            server_thread.daemon = True
            server_thread.start()
            self.servers.append(server_thread)
    
    def stop_servers(self):
        """Detiene todos los servidores"""
        print("\n🔴 Deteniendo servidores...")
        self.running = False
        self.end_time = time.time()
        
        for server in self.servers:
            server.join(timeout=2)
    
    def wait_for_completion(self, target_requests):
        """Espera hasta que se procesen todas las peticiones"""
        while True:
            with self.metrics_lock:
                total_completed = self.processed_requests + self.failed_requests
                if total_completed >= target_requests:
                    break
                
                # Verificar si hay peticiones pendientes
                total_pending = sum(len(queue) for queue in self.request_queues)
                if total_pending == 0 and total_completed > 0:
                    break
            
            time.sleep(0.1)
    
    def get_detailed_metrics(self):
        """Obtiene métricas detalladas del sistema"""
        with self.metrics_lock:
            # Usar los tiempos reales de procesamiento para cálculos más precisos
            if self.actual_start_time and self.actual_end_time:
                total_time = self.actual_end_time - self.actual_start_time
            elif self.end_time and self.start_time:
                total_time = self.end_time - self.start_time
            else:
                # Fallback: calcular desde métricas de servidores
                server_start_times = [m['start_time'] for m in self.server_metrics if m['start_time']]
                server_end_times = [m['end_time'] for m in self.server_metrics if m['end_time']]
                if server_start_times and server_end_times:
                    total_time = max(server_end_times) - min(server_start_times)
                else:
                    total_time = 1.0  # Valor por defecto para evitar división por cero
            
            total_requests = self.processed_requests + self.failed_requests
            
            # Métricas globales
            avg_response_time = self.total_response_time / self.processed_requests if self.processed_requests > 0 else 0
            throughput = total_requests / total_time if total_time > 0 else 0
            success_rate = (self.processed_requests / total_requests * 100) if total_requests > 0 else 0
            
            # Calcular min/max de tiempos de respuesta global
            all_response_times = [item['processing_time'] for item in self.request_processing_times]
            global_min_time = min(all_response_times) if all_response_times else 0
            global_max_time = max(all_response_times) if all_response_times else 0
            
            # Métricas por servidor
            server_detailed_metrics = []
            for i in range(self.num_servers):
                server_data = self.server_metrics[i]
                server_name = self.server_configs[i]['name']
                
                server_total_requests = server_data['processed_requests'] + server_data['failed_requests']
                
                # Tiempo individual del servidor
                if server_data['end_time'] and server_data['start_time']:
                    server_total_time = server_data['end_time'] - server_data['start_time']
                else:
                    server_total_time = total_time
                
                server_avg_response_time = (server_data['total_response_time'] / server_data['processed_requests']) if server_data['processed_requests'] > 0 else 0
                server_rps = server_total_requests / server_total_time if server_total_time > 0 else 0
                server_success_rate = (server_data['processed_requests'] / server_total_requests * 100) if server_total_requests > 0 else 0
                
                # Min/Max por servidor
                server_min_time = min(server_data['response_times']) if server_data['response_times'] else 0
                server_max_time = max(server_data['response_times']) if server_data['response_times'] else 0
                
                server_detailed_metrics.append({
                    'name': server_name,
                    'processed_requests': server_data['processed_requests'],
                    'failed_requests': server_data['failed_requests'],
                    'total_requests': server_total_requests,
                    'avg_response_time': server_avg_response_time,
                    'min_response_time': server_min_time,
                    'max_response_time': server_max_time,
                    'rps': server_rps,
                    'success_rate': server_success_rate,
                    'server_time': server_total_time
                })
            
            metrics = {
                # Métricas globales
                "total_simulation_time": total_time,
                "total_successful_requests": self.processed_requests,
                "total_failed_requests": self.failed_requests,
                "total_requests": total_requests,
                "global_avg_response_time": avg_response_time,
                "global_min_response_time": global_min_time,
                "global_max_response_time": global_max_time,
                "throughput": throughput,
                "success_rate": success_rate,
                "stolen_requests": self.stolen_requests,
                "steal_percentage": (self.stolen_requests / total_requests * 100) if total_requests > 0 else 0,
                
                # Métricas por servidor
                "server_metrics": server_detailed_metrics,
                
                # Datos adicionales
                "queue_sizes": [len(queue) for queue in self.request_queues],
                "steal_operations": self.steal_operations,
                "processing_times": self.request_processing_times,
                
                # Información de depuración
                "debug_info": {
                    "start_time": self.start_time,
                    "end_time": self.end_time,
                    "actual_start_time": self.actual_start_time,
                    "actual_end_time": self.actual_end_time,
                    "calculated_total_time": total_time
                }
            }
        
        return metrics

class TrafficGenerator:
    """Generador de tráfico web simulado"""
    
    def __init__(self, server_system):
        self.server_system = server_system
        self.request_counter = 0
        self.running = True
        
        # Peticiones web predefinidas con tiempos específicos
        self.peticiones_web = [
            ("GET", "/", 0.1, 0.3),
            ("GET", "/api/users", 0.2, 0.5),
            ("POST", "/login", 0.3, 0.6),
            ("GET", "/images/logo.png", 0.1, 0.2),
            ("POST", "/api/upload", 0.5, 1.0),
            ("GET", "/dashboard", 0.2, 0.4),
            ("POST", "/api/process", 0.4, 0.8),
            ("GET", "/static/css/style.css", 0.1, 0.2),
            ("PUT", "/api/users/123", 0.3, 0.5),
            ("DELETE", "/api/posts/456", 0.2, 0.4),
            ("GET", "/api/reports", 0.4, 0.7),
            ("POST", "/api/backup", 0.6, 1.2),
            ("POST", "/api/analytics", 0.3, 0.6),
            ("GET", "/api/dashboard/stats", 0.2, 0.5),
            ("DELETE", "/cache/clear", 0.1, 0.3),
        ]
        
    def generate_request(self):
        """Genera una petición HTTP basada en las peticiones predefinidas"""
        self.request_counter += 1
        
        # Seleccionar aleatoriamente una petición de la lista predefinida
        method, path, min_time, max_time = random.choice(self.peticiones_web)
        
        return HTTPRequest(
            request_id=f"req_{self.request_counter:03d}",
            method=method,
            path=path,
            client_ip=f"192.168.1.{random.randint(100, 200)}",
            min_time=min_time,
            max_time=max_time
        )
    
    def generate_requests_batch(self, num_requests):
        """Genera un lote de peticiones con distribución desigual inicial"""
        print(f"📊 Generando {num_requests} peticiones...")
        
        for i in range(num_requests):
            request = self.generate_request()
            
            # Distribución desigual: la mayoría van al servidor más ligero inicialmente
            if i < num_requests * 0.5:  # 50% van a WebServer-Light
                target_server = 0
            elif i < num_requests * 0.7:  # 20% van a WebServer-Medium
                target_server = 1
            elif i < num_requests * 0.85:  # 15% van a WebServer-Heavy
                target_server = 2
            else:  # 15% van a WebServer-Premium
                target_server = 3
            
            self.server_system.add_request(target_server, request)
            time.sleep(0.001)  # Pausa más pequeña entre peticiones

def run_performance_test(num_requests):
    """Ejecuta un test de rendimiento para un número específico de peticiones"""
    print(f"\n{'='*60}")
    print(f"🧪 EJECUTANDO TEST CON {num_requests} PETICIONES")
    print(f"{'='*60}")
    
    # Crear sistema de servidores
    server_system = WebServerWorkStealing()
    traffic_gen = TrafficGenerator(server_system)
    
    # Iniciar servidores
    server_system.start_servers()
    time.sleep(0.5)  # Dar tiempo a que se inicien
    
    try:
        # Generar peticiones
        traffic_gen.generate_requests_batch(num_requests)
        
        # Esperar a que se procesen todas las peticiones
        server_system.wait_for_completion(num_requests)
        time.sleep(0.5)  # Buffer adicional
        
        # Obtener métricas
        metrics = server_system.get_detailed_metrics()
        
        return metrics
        
    finally:
        # Detener servidores
        server_system.stop_servers()

def print_enhanced_performance_summary(metrics, num_requests):
    """Imprime un resumen detallado de rendimiento"""
    print(f"\n📈 RESUMEN DETALLADO DE RENDIMIENTO - {num_requests} PETICIONES")
    print("=" * 80)
    
    # Métricas globales
    simulation_time_ms = metrics['total_simulation_time'] * 1000  # Convertir a milisegundos
    print("🌍 MÉTRICAS GLOBALES:")
    print(f"  • Tiempo total de simulación: {simulation_time_ms:.1f}ms")
    print(f"  • Throughput: {metrics['throughput']:.2f} peticiones/segundo")
    print(f"  • Peticiones exitosas totales: {metrics['total_successful_requests']}")
    print(f"  • Peticiones fallidas totales: {metrics['total_failed_requests']}")
    print(f"  • Tasa de éxito: {metrics['success_rate']:.1f}%")
    print(f"  • Peticiones robadas: {metrics['stolen_requests']} ({metrics['steal_percentage']:.1f}%)")
    
    # Tiempos de respuesta globales
    print(f"\n⏱️  TIEMPOS DE RESPUESTA GLOBALES:")
    print(f"  • Tiempo promedio de respuesta: {metrics['global_avg_response_time']:.3f}s")
    print(f"  • Tiempo mínimo de respuesta: {metrics['global_min_response_time']:.3f}s")
    print(f"  • Tiempo máximo de respuesta: {metrics['global_max_response_time']:.3f}s")
    
    # Métricas por servidor
    print(f"\n🖥️  MÉTRICAS POR SERVIDOR:")
    print("-" * 80)
    header = f"{'Servidor':<15} {'Exitosas':<10} {'Fallidas':<9} {'RPS':<8} {'Avg(s)':<8} {'Min(s)':<8} {'Max(s)':<8}"
    print(header)
    print("-" * 80)
    
    for server_data in metrics['server_metrics']:
        print(f"{server_data['name']:<15} "
              f"{server_data['processed_requests']:<10} "
              f"{server_data['failed_requests']:<9} "
              f"{server_data['rps']:<8.2f} "
              f"{server_data['avg_response_time']:<8.3f} "
              f"{server_data['min_response_time']:<8.3f} "
              f"{server_data['max_response_time']:<8.3f}")
    
    # Distribución de carga
    print(f"\n🔄 DISTRIBUCIÓN DE CARGA:")
    for server_data in metrics['server_metrics']:
        percentage = (server_data['total_requests'] / metrics['total_requests'] * 100) if metrics['total_requests'] > 0 else 0
        cores = next(config['cpu_cores'] for config in [
            {"name": "WebServer-Light", "cpu_cores": 2},
            {"name": "WebServer-Medium", "cpu_cores": 4},
            {"name": "WebServer-Heavy", "cpu_cores": 8},
            {"name": "WebServer-Premium", "cpu_cores": 6}
        ] if config['name'] == server_data['name'])
        print(f"  • {server_data['name']} ({cores} cores): {server_data['total_requests']} peticiones ({percentage:.1f}%)")
    
    # Operaciones de robo más frecuentes
    if metrics['steal_operations']:
        print(f"\n🔄 OPERACIONES DE ROBO MÁS FRECUENTES:")
        steal_summary = {}
        for steal in metrics['steal_operations']:
            key = f"{steal['thief']} <- {steal['victim']}"
            steal_summary[key] = steal_summary.get(key, 0) + 1
        
        for steal_pattern, count in sorted(steal_summary.items(), key=lambda x: x[1], reverse=True):
            print(f"  • {steal_pattern}: {count} veces")
    
    # Información de depuración (solo si hay problemas)
    debug = metrics.get('debug_info', {})
    if debug.get('calculated_total_time', 0) <= 0:
        print(f"\n🐛 DEBUG INFO:")
        print(f"  • Start time: {debug.get('start_time')}")
        print(f"  • End time: {debug.get('end_time')}")
        print(f"  • Actual start: {debug.get('actual_start_time')}")
        print(f"  • Actual end: {debug.get('actual_end_time')}")
        print(f"  • Calculated total: {debug.get('calculated_total_time')}")

def analyze_work_stealing_performance():
    """Análisis completo de rendimiento del work stealing"""
    print("🌐 ANÁLISIS DE RENDIMIENTO - WORK STEALING WEB SERVERS")
    print("Configuración: 4 Servidores (Light, Medium, Heavy, Premium)")
    print("=" * 70)
    
    test_cases = [15, 50, 100]
    all_results = {}
    
    for num_requests in test_cases:
        metrics = run_performance_test(num_requests)
        all_results[num_requests] = metrics
        print_enhanced_performance_summary(metrics, num_requests)
        time.sleep(1)  # Pausa entre tests
    
    # Comparación final
    print(f"\n{'='*100}")
    print("📊 COMPARACIÓN FINAL DE RENDIMIENTO")
    print(f"{'='*100}")
    
    print(f"{'Peticiones':<12} {'Tiempo(ms)':<12} {'Throughput':<12} {'Exitosas':<10} {'Fallidas':<9} {'% Robos':<8} {'Avg Resp(s)':<12}")
    print("-" * 100)
    
    for num_requests in test_cases:
        m = all_results[num_requests]
        simulation_time_ms = m['total_simulation_time'] * 1000
        print(f"{num_requests:<12} "
              f"{simulation_time_ms:<12.1f} "
              f"{m['throughput']:<12.2f} "
              f"{m['total_successful_requests']:<10} "
              f"{m['total_failed_requests']:<9} "
              f"{m['steal_percentage']:<8.1f} "
              f"{m['global_avg_response_time']:<12.3f}")
    
    print(f"\n✅ Análisis completado. El work stealing muestra mayor beneficio con más peticiones.")
    
    return all_results

# Ejecutar el análisis
if __name__ == "__main__":
    results = analyze_work_stealing_performance()