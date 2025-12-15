namespace Proyecto_3D
{
    /// <summary>
    /// Representa una arista (línea) entre dos puntos
    /// </summary>
    public class Arista
    {
        public int Inicio { get; set; }  // Índice del punto inicial
        public int Fin { get; set; }     // Índice del punto final

        public Arista(int inicio, int fin)
        {
            Inicio = inicio;
            Fin = fin;
        }
    }
}
