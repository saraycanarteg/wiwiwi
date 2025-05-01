import matplotlib.pyplot as plt
import numpy as np

aceleraciones = [2.00, 2.50, 1.67, 2.5, 3.00, 3.00, 3.00, 2.33, 5.00, 3.50, 1.67, 2.50, 2.33, 6.00, 3.00, 1.67, 2.50, 2.33, 1.00,
             1.20, 0.00, 0.00,'n/a', 1.00, 'n/a',3.00, 'n/a', 0.00, 2.00, 0.00, 2.50, 1.75, 3.00, 2.50, 3.00, 2.50, 3.33, 3.00,
             3.00, 1.67, 5.00, 1.67, 4.00, 2.00, 3.00, 3.00, 1.75, 3.50, 4.00, 2.33]

datos = [x if isinstance(x, (int, float)) and np.isfinite(x) else np.nan for x in aceleraciones]

num_mediciones = len(datos)
x = np.arange(1, num_mediciones + 1)

plt.plot(x, datos, linestyle='-', marker='', color='green', linewidth=2)
plt.title("Aceleración SIMD considerando datos atípicos")
plt.xlabel("Número de medición")
plt.ylabel("Aceleración SIMD")
plt.grid(True)
plt.show()