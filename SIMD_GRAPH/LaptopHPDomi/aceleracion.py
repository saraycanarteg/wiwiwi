import matplotlib.pyplot as plt
import numpy as np

aceleraciones = [1.00, 4.00, 3.00, 1.50, 2.33, 1.29, 2.00, 1.33, 5.00, 2.00, 2.50, 3.00, 5.00, 1.50, 
                 4.00, 1.50, 3.00, 1.33, 1.50, 1.50, 2.00, 4.00, 3.00, 4.00, 2.50, 4.00, 1.50, 4.00, 
                 4.00, 4.00, 3.00, 1.50, "n/a", 4.00, 4.00, 4.00, 3.00, 2.50, 2.00, 3.00, 2.00, 1.33, 
                 "n/a", 1.33, 3.00, 1.67, 4.00, 3.00, 2.00, 6.00]

datos = [x if isinstance(x, (int, float)) and np.isfinite(x) else np.nan for x in aceleraciones]

num_mediciones = len(datos)
x = np.arange(1, num_mediciones + 1)

plt.plot(x, datos, linestyle='-', marker='', color='green', linewidth=2)
plt.title("Aceleración SIMD considerando datos atípicos")
plt.xlabel("Número de medición")
plt.ylabel("Aceleración SIMD")
plt.grid(True)
plt.show()