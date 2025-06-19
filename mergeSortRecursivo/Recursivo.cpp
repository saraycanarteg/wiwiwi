#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <string.h>

#define SIZE 5000
#define NUM_MEASUREMENTS 100

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

void mergeSortRecursive(int arr[], int left, int right) {
    if (left < right) {
        int mid = left + (right - left) / 2;
        mergeSortRecursive(arr, left, mid);
        mergeSortRecursive(arr, mid + 1, right);
        merge(arr, left, mid, right);
    }
}

void generateRandomArray(int arr[], int size) {
    for (int i = 0; i < size; i++) {
        arr[i] = rand() % 10000;
    }
}

void copyArray(int source[], int dest[], int size) {
    for (int i = 0; i < size; i++) {
        dest[i] = source[i];
    }
}

int main() {
    int originalArr[SIZE];
    int workingArr[SIZE];
    FILE* csvFile;
    double measurements[NUM_MEASUREMENTS];
    double totalTime = 0.0;
    double minTime = 999999.0;
    double maxTime = 0.0;

    srand(time(NULL));
    printf("Generando array aleatorio unico de %d elementos...\n", SIZE);
    generateRandomArray(originalArr, SIZE);

    csvFile = fopen("mergesort_measurements.csv", "w");
    if (csvFile == NULL) {
        printf("Error: No se pudo crear el archivo CSV\n");
        return 1;
    }
    fprintf(csvFile, "Medicion;Tiempo_Segundos\n");

    printf("Realizando %d mediciones de Merge Sort con el mismo arreglo de %d elementos...\n", NUM_MEASUREMENTS, SIZE);

    for (int measurement = 1; measurement <= NUM_MEASUREMENTS; measurement++) {
        copyArray(originalArr, workingArr, SIZE);

        clock_t start = clock();
        mergeSortRecursive(workingArr, 0, SIZE - 1);
        clock_t end = clock();

        double timeTaken = ((double)(end - start)) / CLOCKS_PER_SEC;
        measurements[measurement - 1] = timeTaken;
        totalTime += timeTaken;

        fprintf(csvFile, "%d;%.6f\n", measurement, timeTaken);

    }

    fclose(csvFile);

    printf("\nPrimeros 10 elementos del array original: ");
    for (int i = 0; i < 10; i++) {
        printf("%d ", originalArr[i]);
    }

    printf("\nPrimeros 10 elementos de la ultima medicion ordenada: ");
    for (int i = 0; i < 10; i++) {
        printf("%d ", workingArr[i]);
    }
    printf("\n");

    return 0;
}
