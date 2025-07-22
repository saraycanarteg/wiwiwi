import threading
import time
import random
import queue
from collections import deque
from datetime import datetime
import json
import statistics
from typing import List, Dict, Optional

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
        self.assigned_server = None
        self.predicted_response_time = None
        self.actual_response_time = None
    
    def __str__(self):
        return f"Request {self.request_id}: {self.method} {self.path}"

class ServerPerformancePredictor:
    """Predictor de rendimiento de servidor basado en métricas históricas"""
    
    def __init__(self, server_id, server_config):
        self.server_id = server_id
        self.server_config = server_config
        self.response_time_history = deque(maxlen=100)  # Últimas 100 peticiones
        self.load_history = deque(maxlen=50)  # Últimos 50 puntos de carga
        self.current_connections = 0
        self.total_processed = 0
        self.prediction_errors = []
        
        # Factores de peso para predicción
        self.base_response_time = 0.1  # Tiempo base mínimo
        self.load_impact_factor = server_config.get('capacity_factor', 1.0)
        self.cpu_cores = server_config.get('cpu_cores', 4)
        
    def add_response_time(self, response_time):
        """Añade un nuevo tiempo de respuesta al historial"""
        self.response_time_history.append(response_time)
        self.total_processed += 1
    
    def add_load_point(self, current_load):
        """Añade un punto de carga actual"""
        self.load_history.append(current_load)
    
    def get_average_response_time(self, window=20):
        """Obtiene el tiempo promedio de respuesta en una ventana"""
        if not self.response_time_history:
            return self.base_response_time
        
        recent_times = list(self.response_time_history)[-window:]
        return statistics.mean(recent_times) if recent_times else self.base_response_time
    
    def get_load_trend(self):
        """Calcula la tendencia de carga (positiva = aumentando, negativa = disminuyendo)"""
        if len(self.load_history) < 2:
            return 0.0
        
        recent_loads = list(self.load_history)[-10:]  # Últimos 10 puntos
        if len(recent_loads) < 2:
            return 0.0
        
        # Calcular tendencia usando regresión lineal simple
        n = len(recent_loads)
        x_sum = sum(range(n))
        y_sum = sum(recent_loads)
        xy_sum = sum(i * load for i, load in enumerate(recent_loads))
        x2_sum = sum(i * i for i in range(n))
        
        if n * x2_sum - x_sum * x_sum == 0:
            return 0.0
        
        slope = (n * xy_sum - x_sum * y_sum) / (n * x2_sum - x_sum * x_sum)
        return slope
    
    def predict_response_time(self, request_complexity=1.0):
        """
        Predice el tiempo de respuesta basado en:
        - Tiempo promedio histórico
        - Carga actual
        - Tendencia de carga
        - Capacidad del servidor
        - Complejidad de la petición
        """
        # Tiempo base histórico
        base_time = self.get_average_response_time()
        
        # Factor de carga actual (más conexiones = más tiempo)
        load_factor = 1.0 + (self.current_connections / max(self.cpu_cores * 2, 1)) * 0.3
        
        # Factor de tendencia (si la carga está aumentando, será peor)
        trend = self.get_load_trend()
        trend_factor = 1.0 + max(trend * 0.1, 0)  # Solo penalizar tendencias positivas
        
        # Factor de capacidad del servidor
        capacity_factor = self.load_impact_factor
        
        # Predicción final
        predicted_time = base_time * load_factor * trend_factor * capacity_factor * request_complexity
        
        return max(predicted_time, 0.05)  # Mínimo 50ms
    
    def get_server_score(self, request_complexity=1.0):
        """
        Calcula un score para el servidor (menor es mejor)
        Combina tiempo de respuesta predicho con carga actual
        """
        predicted_time = self.predict_response_time(request_complexity)
        
        # Score basado en tiempo predicho y carga relativa
        load_weight = self.current_connections / max(self.cpu_cores, 1)
        score = predicted_time + (load_weight * 0.1)
        
        return score

class WebServerPredictiveLoadBalancer:
    """Sistema de balanceador de carga predictivo para servidores web"""
    
    def __init__(self):
        self.num_servers = 4
        self.servers = []
        self.request_queues = [queue.Queue() for _ in range(self.num_servers)]
        self.running = True
        
        # Predictores por servidor
        self.predictors = []
        
        # Métricas globales
        self.processed_requests = 0
        self.failed_requests = 0
        self.total_response_time = 0
        self.prediction_accuracy_sum = 0
        self.prediction_count = 0
        self.load_balancing_decisions = []
        self.request_processing_times = []
        self.metrics_lock = threading.Lock()
        self.start_time = None
        self.end_time = None
        self.actual_start_time = None
        self.actual_end_time = None
        
        # Métricas por servidor
        self.server_metrics = [
            {
                'processed_requests': 0,
                'failed_requests': 0,
                'total_response_time': 0.0,
                'response_times': [],
                'start_time': None,
                'end_time': None,
                'prediction_errors': [],
                'avg_prediction_error': 0.0,
                'current_load': 0
            } for _ in range(self.num_servers)
        ]
        
        # Configuración de servidores
        self.server_configs = [
            {"name": "WebServer-Light", "cpu_cores": 2, "memory": "4GB", "capacity_factor": 1.5, "port": 8080},
            {"name": "WebServer-Medium", "cpu_cores": 4, "memory": "6GB", "capacity_factor": 1.2, "port": 8081},
            {"name": "WebServer-Heavy", "cpu_cores": 8, "memory": "8GB", "capacity_factor": 0.8, "port": 8082},
            {"name": "WebServer-Premium", "cpu_cores": 6, "memory": "10GB", "capacity_factor": 1.0, "port": 8083}
        ]
        
        # Inicializar predictores
        for i, config in enumerate(self.server_configs):
            self.predictors.append(ServerPerformancePredictor(i, config))
    
    def get_request_complexity(self, request):
        """Determina la complejidad de una petición basada en su tipo y path"""
        complexity_map = {
            'GET': {
                '/': 0.5,
                '/images/': 0.3,
                '/static/': 0.3,
                '/api/': 0.8,
                '/dashboard': 1.0,
                '/api/reports': 1.2
            },
            'POST': {
                '/login': 0.7,
                '/api/upload': 1.5,
                '/api/process': 1.3,
                '/api/backup': 2.0,
                '/api/analytics': 1.1
            },
            'PUT': {
                '/api/': 1.0
            },
            'DELETE': {
                '/api/': 0.8,
                '/cache/': 0.4
            }
        }
        
        method_complexities = complexity_map.get(request.method, {})
        
        # Buscar coincidencia de path más específica
        for path_pattern, complexity in method_complexities.items():
            if request.path.startswith(path_pattern):
                return complexity
        
        return 1.0  # Complejidad por defecto
    
    def select_best_server(self, request):
        """
        Algoritmo de selección predictiva de servidor
        Basado en Weighted Least Connections with Response Time Prediction
        """
        request_complexity = self.get_request_complexity(request)
        
        best_server = 0
        best_score = float('inf')
        server_scores = []
        
        # Actualizar métricas de carga para todos los servidores
        for i, predictor in enumerate(self.predictors):
            current_load = self.request_queues[i].qsize()
            predictor.add_load_point(current_load)
            predictor.current_connections = current_load
            self.server_metrics[i]['current_load'] = current_load
        
        # Evaluar cada servidor
        for i, predictor in enumerate(self.predictors):
            score = predictor.get_server_score(request_complexity)
            server_scores.append({
                'server_id': i,
                'server_name': self.server_configs[i]['name'],
                'score': score,
                'current_load': predictor.current_connections,
                'predicted_time': predictor.predict_response_time(request_complexity)
            })
            
            if score < best_score:
                best_score = score
                best_server = i
        
        # Guardar la decisión para análisis
        decision_info = {
            'request_id': request.request_id,
            'selected_server': best_server,
            'selected_server_name': self.server_configs[best_server]['name'],
            'server_scores': server_scores,
            'request_complexity': request_complexity,
            'decision_time': time.time()
        }
        
        with self.metrics_lock:
            self.load_balancing_decisions.append(decision_info)
        
        # Asignar predicción al request
        request.predicted_response_time = self.predictors[best_server].predict_response_time(request_complexity)
        request.assigned_server = best_server
        
        print(f"🎯 Balanceador asignó {request.request_id} a {self.server_configs[best_server]['name']} "
              f"(Score: {best_score:.3f}, Pred: {request.predicted_response_time:.3f}s)")
        
        return best_server
    
    def add_request_with_balancing(self, request):
        """Añade una petición usando el balanceador de carga predictivo"""
        selected_server = self.select_best_server(request)
        self.request_queues[selected_server].put(request)
        return selected_server
    
    def process_request(self, server_id, request):
        """Procesa una petición y actualiza las métricas predictivas"""
        server_name = self.server_configs[server_id]['name']
        capacity_factor = self.server_configs[server_id]['capacity_factor']
        
        request.start_processing_time = time.time()
        
        with self.metrics_lock:
            if self.actual_start_time is None:
                self.actual_start_time = request.start_processing_time
        
        # Simular fallo ocasional (5% de probabilidad)
        if random.random() < 0.05:
            request.failed = True
            request.response_status = random.choice([500, 503, 504])
            request.completed_at = datetime.now()
            request.end_processing_time = time.time()
            request.actual_response_time = request.end_processing_time - request.start_processing_time
            
            with self.metrics_lock:
                self.failed_requests += 1
                self.server_metrics[server_id]['failed_requests'] += 1
                self.actual_end_time = request.end_processing_time
            
            print(f"❌ {server_name} falló {request.request_id} - Status: {request.response_status}")
            return
        
        # Procesar petición
        adjusted_processing_time = request.processing_time * capacity_factor
        time.sleep(adjusted_processing_time)
        
        request.response_status = random.choice([200, 201, 204])
        request.completed_at = datetime.now()
        request.end_processing_time = time.time()
        request.actual_response_time = request.end_processing_time - request.start_processing_time
        
        # Actualizar predictor con resultado real
        self.predictors[server_id].add_response_time(request.actual_response_time)
        
        # Calcular error de predicción
        if request.predicted_response_time:
            prediction_error = abs(request.actual_response_time - request.predicted_response_time)
            prediction_accuracy = 1 - min(prediction_error / max(request.actual_response_time, 0.01), 1.0)
            
            with self.metrics_lock:
                self.prediction_accuracy_sum += prediction_accuracy
                self.prediction_count += 1
                self.server_metrics[server_id]['prediction_errors'].append(prediction_error)
        
        # Actualizar métricas globales
        with self.metrics_lock:
            self.processed_requests += 1
            self.total_response_time += request.actual_response_time
            self.actual_end_time = request.end_processing_time
            
            self.server_metrics[server_id]['processed_requests'] += 1
            self.server_metrics[server_id]['total_response_time'] += request.actual_response_time
            self.server_metrics[server_id]['response_times'].append(request.actual_response_time)
            
            self.request_processing_times.append({
                'request_id': request.request_id,
                'server': server_name,
                'server_id': server_id,
                'processing_time': request.actual_response_time,
                'predicted_time': request.predicted_response_time,
                'prediction_error': prediction_error if request.predicted_response_time else 0,
                'start_time': request.start_processing_time,
                'end_time': request.end_processing_time
            })
        
        print(f"✅ {server_name} completó {request.request_id} - "
              f"Real: {request.actual_response_time:.3f}s, "
              f"Pred: {request.predicted_response_time:.3f}s")
    
    def server_worker(self, server_id):
        """Función principal de cada servidor web"""
        server_name = self.server_configs[server_id]['name']
        print(f"🟢 {server_name} iniciado")
        
        self.server_metrics[server_id]['start_time'] = time.time()
        
        while self.running:
            try:
                request = self.request_queues[server_id].get(timeout=0.1)
                if request:
                    self.process_request(server_id, request)
                    self.request_queues[server_id].task_done()
            except queue.Empty:
                continue
        
        self.server_metrics[server_id]['end_time'] = time.time()
        print(f"🔴 {server_name} detenido")
    
    def start_servers(self):
        """Inicia todos los servidores"""
        print("🌐 Iniciando sistema de balanceador de carga predictivo...\n")
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
                
                total_pending = sum(q.qsize() for q in self.request_queues)
                if total_pending == 0 and total_completed > 0:
                    break
            
            time.sleep(0.1)
    
    def get_detailed_metrics(self):
        """Obtiene métricas detalladas del sistema con métricas predictivas"""
        with self.metrics_lock:
            if self.actual_start_time and self.actual_end_time:
                total_time = self.actual_end_time - self.actual_start_time
            elif self.end_time and self.start_time:
                total_time = self.end_time - self.start_time
            else:
                server_start_times = [m['start_time'] for m in self.server_metrics if m['start_time']]
                server_end_times = [m['end_time'] for m in self.server_metrics if m['end_time']]
                if server_start_times and server_end_times:
                    total_time = max(server_end_times) - min(server_start_times)
                else:
                    total_time = 1.0
            
            total_requests = self.processed_requests + self.failed_requests
            
            # Métricas globales
            avg_response_time = self.total_response_time / self.processed_requests if self.processed_requests > 0 else 0
            throughput = total_requests / total_time if total_time > 0 else 0
            success_rate = (self.processed_requests / total_requests * 100) if total_requests > 0 else 0
            
            # Precisión de predicción global
            avg_prediction_accuracy = (self.prediction_accuracy_sum / self.prediction_count * 100) if self.prediction_count > 0 else 0
            
            # Calcular estadísticas de tiempo de respuesta
            all_response_times = [item['processing_time'] for item in self.request_processing_times]
            global_min_time = min(all_response_times) if all_response_times else 0
            global_max_time = max(all_response_times) if all_response_times else 0
            
            # Métricas por servidor
            server_detailed_metrics = []
            for i in range(self.num_servers):
                server_data = self.server_metrics[i]
                server_name = self.server_configs[i]['name']
                
                server_total_requests = server_data['processed_requests'] + server_data['failed_requests']
                
                if server_data['end_time'] and server_data['start_time']:
                    server_total_time = server_data['end_time'] - server_data['start_time']
                else:
                    server_total_time = total_time
                
                server_avg_response_time = (server_data['total_response_time'] / server_data['processed_requests']) if server_data['processed_requests'] > 0 else 0
                server_rps = server_total_requests / server_total_time if server_total_time > 0 else 0
                server_success_rate = (server_data['processed_requests'] / server_total_requests * 100) if server_total_requests > 0 else 0
                
                server_min_time = min(server_data['response_times']) if server_data['response_times'] else 0
                server_max_time = max(server_data['response_times']) if server_data['response_times'] else 0
                
                # Métricas predictivas por servidor
                server_avg_prediction_error = statistics.mean(server_data['prediction_errors']) if server_data['prediction_errors'] else 0
                
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
                    'avg_prediction_error': server_avg_prediction_error,
                    'server_time': server_total_time
                })
            
            # Análisis de decisiones del balanceador
            server_assignment_count = {}
            for decision in self.load_balancing_decisions:
                server_name = decision['selected_server_name']
                server_assignment_count[server_name] = server_assignment_count.get(server_name, 0) + 1
            
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
                
                # Métricas predictivas
                "avg_prediction_accuracy": avg_prediction_accuracy,
                "prediction_count": self.prediction_count,
                "server_assignment_distribution": server_assignment_count,
                
                # Métricas por servidor
                "server_metrics": server_detailed_metrics,
                
                # Datos adicionales
                "queue_sizes": [q.qsize() for q in self.request_queues],
                "balancing_decisions": self.load_balancing_decisions,
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
        
        method, path, min_time, max_time = random.choice(self.peticiones_web)
        
        return HTTPRequest(
            request_id=f"req_{self.request_counter:03d}",
            method=method,
            path=path,
            client_ip=f"192.168.1.{random.randint(100, 200)}",
            min_time=min_time,
            max_time=max_time
        )
    
    def generate_requests_stream(self, num_requests):
        """Genera peticiones en un flujo más realista (no por lotes)"""
        print(f"📊 Generando {num_requests} peticiones con balanceador predictivo...")
        
        for i in range(num_requests):
            request = self.generate_request()
            
            # Usar el balanceador de carga predictivo
            self.server_system.add_request_with_balancing(request)
            
            # Pausa más realista entre peticiones
            time.sleep(random.uniform(0.001, 0.01))

def run_performance_test(num_requests):
    """Ejecuta un test de rendimiento para un número específico de peticiones"""
    print(f"\n{'='*60}")
    print(f"🧪 EJECUTANDO TEST PREDICTIVO CON {num_requests} PETICIONES")
    print(f"{'='*60}")
    
    # Crear sistema de balanceador predictivo
    server_system = WebServerPredictiveLoadBalancer()
    traffic_gen = TrafficGenerator(server_system)
    
    # Iniciar servidores
    server_system.start_servers()
    time.sleep(0.5)
    
    try:
        # Generar peticiones
        traffic_gen.generate_requests_stream(num_requests)
        
        # Esperar a que se procesen todas las peticiones
        server_system.wait_for_completion(num_requests)
        time.sleep(0.5)
        
        # Obtener métricas
        metrics = server_system.get_detailed_metrics()
        
        return metrics
        
    finally:
        # Detener servidores
        server_system.stop_servers()

def print_enhanced_performance_summary(metrics, num_requests):
    """Imprime un resumen detallado de rendimiento con métricas predictivas"""
    print(f"\n📈 RESUMEN DETALLADO - BALANCEADOR PREDICTIVO - {num_requests} PETICIONES")
    print("=" * 90)
    
    # Métricas globales
    simulation_time_ms = metrics['total_simulation_time'] * 1000
    print("🌍 MÉTRICAS GLOBALES:")
    print(f"  • Tiempo total de simulación: {simulation_time_ms:.1f}ms")
    print(f"  • Throughput: {metrics['throughput']:.2f} peticiones/segundo")
    print(f"  • Peticiones exitosas totales: {metrics['total_successful_requests']}")
    print(f"  • Peticiones fallidas totales: {metrics['total_failed_requests']}")
    print(f"  • Tasa de éxito: {metrics['success_rate']:.1f}%")
    
    # Métricas predictivas
    print(f"\n🎯 MÉTRICAS PREDICTIVAS:")
    print(f"  • Precisión promedio de predicción: {metrics['avg_prediction_accuracy']:.1f}%")
    print(f"  • Total de predicciones realizadas: {metrics['prediction_count']}")
    
    # Tiempos de respuesta globales
    print(f"\n⏱️  TIEMPOS DE RESPUESTA GLOBALES:")
    print(f"  • Tiempo promedio de respuesta: {metrics['global_avg_response_time']:.3f}s")
    print(f"  • Tiempo mínimo de respuesta: {metrics['global_min_response_time']:.3f}s")
    print(f"  • Tiempo máximo de respuesta: {metrics['global_max_response_time']:.3f}s")
    
    # Métricas por servidor
    print(f"\n🖥️  MÉTRICAS POR SERVIDOR:")
    print("-" * 90)
    header = f"{'Servidor':<15} {'Exitosas':<10} {'RPS':<8} {'Avg(s)':<8} {'Min(s)':<8} {'Max(s)':<8} {'Pred Err':<9}"
    print(header)
    print("-" * 90)
    
    for server_data in metrics['server_metrics']:
        print(f"{server_data['name']:<15} "
              f"{server_data['processed_requests']:<10} "
              f"{server_data['rps']:<8.2f} "
              f"{server_data['avg_response_time']:<8.3f} "
              f"{server_data['min_response_time']:<8.3f} "
              f"{server_data['max_response_time']:<8.3f} "
              f"{server_data['avg_prediction_error']:<9.3f}")
    
    # Distribución de asignaciones del balanceador
    print(f"\n🎯 DISTRIBUCIÓN DE ASIGNACIONES DEL BALANCEADOR:")
    total_assignments = sum(metrics['server_assignment_distribution'].values())
    for server_name, count in metrics['server_assignment_distribution'].items():
        percentage = (count / total_assignments * 100) if total_assignments > 0 else 0
        cores = next(config['cpu_cores'] for config in [
            {"name": "WebServer-Light", "cpu_cores": 2},
            {"name": "WebServer-Medium", "cpu_cores": 4},
            {"name": "WebServer-Heavy", "cpu_cores": 8},
            {"name": "WebServer-Premium", "cpu_cores": 6}
        ] if config['name'] == server_name)
        print(f"  • {server_name} ({cores} cores): {count} asignaciones ({percentage:.1f}%)")
    
    # Análisis de eficiencia del balanceador
    print(f"\n📊 ANÁLISIS DE EFICIENCIA:")
    if metrics['processing_times']:
        prediction_errors = [item['prediction_error'] for item in metrics['processing_times'] if 'prediction_error' in item]
        if prediction_errors:
            avg_error = statistics.mean(prediction_errors)
            max_error = max(prediction_errors)
            min_error = min(prediction_errors)
            print(f"  • Error promedio de predicción: {avg_error:.3f}s")
            print(f"  • Error máximo de predicción: {max_error:.3f}s")
            print(f"  • Error mínimo de predicción: {min_error:.3f}s")
    
    # Top decisiones del balanceador
    if metrics['balancing_decisions']:
        print(f"\n🔍 ÚLTIMAS 5 DECISIONES DEL BALANCEADOR:")
        recent_decisions = metrics['balancing_decisions'][-5:]
        for decision in recent_decisions:
            scores = {s['server_name']: s['score'] for s in decision['server_scores']}
            selected = decision['selected_server_name']
            print(f"  • {decision['request_id']} → {selected} (Scores: {', '.join(f'{k}:{v:.2f}' for k, v in sorted(scores.items()))})")

def analyze_predictive_load_balancer_performance():
    """Análisis completo de rendimiento del balanceador predictivo"""
    print("🌐 ANÁLISIS DE RENDIMIENTO - BALANCEADOR DE CARGA PREDICTIVO")
    print("Algoritmo: Weighted Least Connections with Response Time Prediction")
    print("Configuración: 4 Servidores (Light, Medium, Heavy, Premium)")
    print("=" * 80)
    
    test_cases = [15, 50, 100]
    all_results = {}
    
    for num_requests in test_cases:
        metrics = run_performance_test(num_requests)
        all_results[num_requests] = metrics
        print_enhanced_performance_summary(metrics, num_requests)
        time.sleep(1)
    
    # Comparación final
    print(f"\n{'='*120}")
    print("📊 COMPARACIÓN FINAL - BALANCEADOR PREDICTIVO")
    print(f"{'='*120}")
    
    print(f"{'Peticiones':<12} {'Tiempo(ms)':<12} {'Throughput':<12} {'Exitosas':<10} {'Fallidas':<9} {'Pred Acc%':<10} {'Avg Resp(s)':<12}")
    print("-" * 120)
    
    for num_requests in test_cases:
        m = all_results[num_requests]
        simulation_time_ms = m['total_simulation_time'] * 1000
        print(f"{num_requests:<12} "
              f"{simulation_time_ms:<12.1f} "
              f"{m['throughput']:<12.2f} "
              f"{m['total_successful_requests']:<10} "
              f"{m['total_failed_requests']:<9} "
              f"{m['avg_prediction_accuracy']:<10.1f} "
              f"{m['global_avg_response_time']:<12.3f}")
    
    # Análisis de distribución de carga
    print(f"\n📈 ANÁLISIS DE DISTRIBUCIÓN DE CARGA:")
    print("-" * 80)
    for num_requests in test_cases:
        m = all_results[num_requests]
        print(f"\n{num_requests} Peticiones:")
        for server_name, count in m['server_assignment_distribution'].items():
            total = sum(m['server_assignment_distribution'].values())
            percentage = (count / total * 100) if total > 0 else 0
            print(f"  • {server_name}: {count} ({percentage:.1f}%)")
    
    # Análisis de precisión predictiva
    print(f"\n🎯 ANÁLISIS DE PRECISIÓN PREDICTIVA:")
    print("-" * 60)
    for num_requests in test_cases:
        m = all_results[num_requests]
        prediction_accuracy = m['avg_prediction_accuracy']
        prediction_count = m['prediction_count']
        print(f"{num_requests} Peticiones: {prediction_accuracy:.1f}% precisión ({prediction_count} predicciones)")
    
    print(f"\n✅ Análisis completado. El balanceador predictivo optimiza automáticamente")
    print(f"    la distribución basándose en capacidad, carga actual y tendencias históricas.")
    
    return all_results

# Ejecutar el análisis
if __name__ == "__main__":
    import statistics
    results = analyze_predictive_load_balancer_performance()