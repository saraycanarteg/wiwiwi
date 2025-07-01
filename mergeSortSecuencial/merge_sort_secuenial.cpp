#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#define SIZE 100000

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

void mergeSortIterative(int arr[], int n) {
    int curr_size, left_start;

    for (curr_size = 1; curr_size <= n - 1; curr_size = 2 * curr_size) {
        for (left_start = 0; left_start < n - 1; left_start += 2 * curr_size) {
            int mid = left_start + curr_size - 1;
            int right_end = (left_start + 2 * curr_size - 1 < n - 1) ?
                           left_start + 2 * curr_size - 1 : n - 1;

            if (mid < right_end)
                merge(arr, left_start, mid, right_end);
        }
    }
}

void printArray(int arr[], int size) {
    for (int i = 0; i < size; i++)
        printf("%d ", arr[i]);
    printf("\n");
}

int main() {
    FILE *file = fopen("merge_sort_sequential_results.csv", "w");
    if (file == NULL) {
        printf("Error al crear el archivo CSV\n");
        return 1;
    }

    // Escribir encabezado del CSV
    fprintf(file, "Medicion;Tiempo_Segundos\n");

    printf("Ejecutando Merge Sort Secuencial 100 veces...\n");
    printf("Guardando resultados en merge_sort_sequential_results.csv\n\n");

    // Generar un solo arreglo aleatorio que se usará en todas las mediciones
    int original_arr[SIZE];
    srand(time(NULL));
    printf("Generando arreglo único de %d elementos...\n", SIZE);
    for (int i = 0; i < SIZE; i++) {
        original_arr[i] = rand() % 10000;
    }

    printf("Primeros 10 elementos del arreglo: ");
    for (int i = 0; i < 10; i++) {
        printf("%d ", original_arr[i]);
    }
    printf("\n\n");

    for (int medicion = 1; medicion <= 100; medicion++) {
        int arr[SIZE];

        // Copiar el arreglo original para cada medición
        for (int i = 0; i < SIZE; i++) {
            arr[i] = original_arr[i];
        }

        // Mostrar progreso cada 10 mediciones
        if (medicion % 10 == 0) {
            printf("Progreso: %d/100 mediciones completadas\n", medicion);
        }

        clock_t start = clock();
        mergeSortIterative(arr, SIZE);
        clock_t end = clock();

        // Mostrar ejemplo solo en la primera medición
        if (medicion == 1) {
            printf("Ejemplo - Primeros 10 elementos después de ordenar: ");
            for (int i = 0; i < 10; i++) {
                printf("%d ", arr[i]);
            }
            printf("\n");
            double time_taken_seconds = ((double)(end - start)) / CLOCKS_PER_SEC;
            printf("Ejemplo - Tiempo de ejecución: %.6f segundos\n\n");
        }

        double time_taken_seconds = ((double)(end - start)) / CLOCKS_PER_SEC;

        // Escribir datos al CSV
        fprintf(file, "%d;%.6f\n", medicion, time_taken_seconds);
    }

    fclose(file);
    printf("\n¡Proceso completado!\n");
    printf("Resultados guardados en: merge_sort_sequential_results.csv\n");

    return 0;
}
