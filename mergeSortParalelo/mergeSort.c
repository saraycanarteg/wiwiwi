#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <omp.h>
#include <string.h>

#define SIZE 50000
#define THRESHOLD 1000
#define NUM_RUNS 100

void merge(int arr[], int left, int mid, int right) {
    int n1 = mid - left + 1;
    int n2 = right - mid;
    int* leftArr = (int*)malloc(n1 * sizeof(int));
    int* rightArr = (int*)malloc(n2 * sizeof(int));

    for (int i = 0; i < n1; i++)
        leftArr[i] = arr[left + i];
    for (int j = 0; j < n2; j++)
        rightArr[j] = arr[mid + 1 + j];

    int i = 0, j = 0, k = left;
    while (i < n1 && j < n2) {
        if (leftArr[i] <= rightArr[j]) {
            arr[k] = leftArr[i];
            i++;
        } else {
            arr[k] = rightArr[j];
            j++;
        }
        k++;
    }

    while (i < n1) {
        arr[k] = leftArr[i];
        i++;
        k++;
    }

    while (j < n2) {
        arr[k] = rightArr[j];
        j++;
        k++;
    }

    free(leftArr);
    free(rightArr);
}

void mergeSortParallel(int arr[], int left, int right) {
    if (left < right) {
        int mid = left + (right - left) / 2;

        // Usar paralelización solo para subarreglos grandes
        if (right - left > THRESHOLD) {
            #pragma omp parallel sections
            {
                #pragma omp section
                {
                    mergeSortParallel(arr, left, mid);
                }
                #pragma omp section
                {
                    mergeSortParallel(arr, mid + 1, right);
                }
            }
        } else {
            // Para subarreglos pequeños, usar versión secuencial
            mergeSortParallel(arr, left, mid);
            mergeSortParallel(arr, mid + 1, right);
        }
        merge(arr, left, mid, right);
    }
}

// Función para copiar un arreglo
void copyArray(int source[], int dest[], int size) {
    for (int i = 0; i < size; i++) {
        dest[i] = source[i];
    }
}



int main() {
    // Arreglo original y copia para cada ejecución
    int* originalArr = (int*)malloc(SIZE * sizeof(int));
    int* workingArr = (int*)malloc(SIZE * sizeof(int));

    // Array para almacenar los tiempos de ejecución
    double executionTimes[NUM_RUNS];

    // Generar arreglo aleatorio una sola vez
    srand(time(NULL));
    printf("Generando arreglo de %d elementos...\n", SIZE);
    for (int i = 0; i < SIZE; i++) {
        originalArr[i] = rand() % 10000;
    }

    printf("Primeros 10 elementos del arreglo original: ");
    for (int i = 0; i < 10; i++) {
        printf("%d ", originalArr[i]);
    }
    printf("\n");

    printf("Número de hilos disponibles: %d\n", omp_get_max_threads());
    printf("Iniciando %d ejecuciones del algoritmo de ordenamiento...\n\n", NUM_RUNS);

    for (int run = 0; run < NUM_RUNS; run++) {
        copyArray(originalArr, workingArr, SIZE);

        double startTime = omp_get_wtime();

        #pragma omp parallel
        {
            #pragma omp single
            {
                mergeSortParallel(workingArr, 0, SIZE - 1);
            }
        }

        double endTime = omp_get_wtime();
        executionTimes[run] = endTime - startTime;

        if ((run + 1) % 10 == 0) {
            printf("Completadas %d ejecuciones...\n", run + 1);
        }
    }

    // Crear archivo CSV
    FILE* csvFile = fopen("ETMergeSortParalela.csv", "w");
    if (csvFile == NULL) {
        printf("Error: No se pudo crear el archivo CSV\n");
        free(originalArr);
        free(workingArr);
        return 1;
    }

    fprintf(csvFile, "Ejecucion,Tiempo_Segundos\n");

    double totalTime = 0;
    double minTime = executionTimes[0];
    double maxTime = executionTimes[0];

    for (int i = 0; i < NUM_RUNS; i++) {
        fprintf(csvFile, "%d,%.9f\n", i + 1, executionTimes[i]);

        totalTime += executionTimes[i];
        if (executionTimes[i] < minTime) minTime = executionTimes[i];
        if (executionTimes[i] > maxTime) maxTime = executionTimes[i];
    }

    fclose(csvFile);

    double avgTime = totalTime / NUM_RUNS;

    printf("\n=== RESULTADOS FINALES ===\n");
    printf("Archivo CSV generado: ETMergeSortParalela.csv\n");
    printf("Total de ejecuciones: %d\n", NUM_RUNS);
    printf("Tamaño del arreglo: %d elementos\n", SIZE);
    printf("Umbral de paralelización: %d\n", THRESHOLD);
    printf("Hilos utilizados: %d\n", omp_get_max_threads());
    printf("\n=== ESTADÍSTICAS DE TIEMPO ===\n");
    printf("Tiempo promedio: %.9f segundos\n", avgTime);
    printf("Tiempo minimo: %.9f segundos\n", minTime);
    printf("Tiempo maximo: %.9f segundos\n", maxTime);

    printf("\nPrimeros 10 elementos después del último ordenamiento: ");
    for (int i = 0; i < 10; i++) {
        printf("%d ", workingArr[i]);
    }
    printf("\n");

    // Liberar memoria
    free(originalArr);
    free(workingArr);

    return 0;
}
