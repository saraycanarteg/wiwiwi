import matplotlib.pyplot as plt
import numpy as np

#Mediciones Obtenidas Laptop 1
tiempo_secuencial = np.array([0.00700, 0.00500, 0.00500, 0.00500, 0.00600, 0.00600, 0.00600, 0.00700, 0.00500, 0.00700, 0.00500,
                              0.00500, 0.00700, 0.00600, 0.00600, 0.00500, 0.01000, 0.00700, 0.00500, 0.00600, 0.00000, 0.00000,
                              0.05000, 0.00500, 0.00000, 0.00600, 0.01000, 0.00000, 0.01000, 0.00000, 0.00500, 0.00700, 0.00600,
                              0.00500, 0.00600, 0.00500, 0.01000, 0.0600, 0.00600, 0.00500, 0.00500, 0.00500, 0.00800, 0.00600,
                              0.00600, 0.00600, 0.00700, 0.00700, 0.00800, 0.00700])

tiempo_simd = np.array([0.00300, 0.00200, 0.00300, 0.00200, 0.00200, 0.00200 , 0.00200, 0.00300, 0.00100, 0.00200, 0.00300, 0.00200, 0.00300,
                        0.00100, 0.00200, 0.00300, 0.00400, 0.00300, 0.00500, 0.00500, 0.01000, 0.01000, 0.00000, 0.00500, 0.00000, 0.00200,
                        0.00000, 0.00900, 0.00500, 0.01000, 0.00200, 0.00400, 0.00200, 0.00200, 0.00200, 0.00200, 0.00300, 0.00200, 0.00200,
                        0.00300, 0.00100, 0.00300, 0.00200, 0.00300, 0.00200, 0.00200, 0.00400, 0.00200, 0.00200, 0.00300])

num_mediciones = len(tiempo_secuencial)
eje_x = np.arange(1, num_mediciones + 1)

plt.figure(figsize=(12, 6))
plt.plot(eje_x, tiempo_secuencial, 'b-', label='Suma Secuencial', linewidth=2)
plt.plot(eje_x, tiempo_simd,'r-', label='Suma SIMD', linewidth = 2)
plt.title('Gráfica Tiempo Secuencial', fontsize=15)
plt.xlabel('Número de medición', fontsize=12)
plt.ylabel('Tiempo de secuencial (s)', fontsize=12)
plt.grid(True, linestyle='--', alpha=0.7)
plt.legend(fontsize=12)

plt.show()
