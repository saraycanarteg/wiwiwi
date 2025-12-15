using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Windows.Forms;

namespace Proyecto_3D
{
    public partial class Form1 : Form
    {
        private Motor3D motor;
        private List<Figura3D> figuras;
        private Figura3D figuraSeleccionada;
        
        // Para el renderizado
        private Bitmap bufferImagen;
        private Timer timerRender;
        
        // Para la interacción con mouse
        private bool mousePresionado = false;
        private bool mousePanear = false;
        private Point ultimaPosicionMouse;

        public Form1()
        {
            InitializeComponent();
            InicializarEscena();
            ConfigurarEventos();
        }

        private void InicializarEscena()
        {
            figuras = new List<Figura3D>();
            motor = new Motor3D(panelViewport.Width, panelViewport.Height);
            bufferImagen = new Bitmap(panelViewport.Width, panelViewport.Height);

            // Configurar timer para renderizado continuo
            timerRender = new Timer();
            timerRender.Interval = 16; // ~60 FPS
            timerRender.Tick += (s, e) => RenderizarEscena();
            timerRender.Start();

            // Agregar un cubo inicial
            AgregarCubo();
        }

        private void ConfigurarEventos()
        {
            // Eventos del viewport
            panelViewport.Paint += PanelViewport_Paint;
            panelViewport.MouseDown += PanelViewport_MouseDown;
            panelViewport.MouseMove += PanelViewport_MouseMove;
            panelViewport.MouseUp += PanelViewport_MouseUp;
            panelViewport.MouseWheel += PanelViewport_MouseWheel;
            panelViewport.Resize += (s, e) => {
                if (panelViewport.Width > 0 && panelViewport.Height > 0)
                {
                    bufferImagen = new Bitmap(panelViewport.Width, panelViewport.Height);
                    motor.AnchoVista = panelViewport.Width;
                    motor.AltoVista = panelViewport.Height;
                    motor.AspectRatio = (double)panelViewport.Width / panelViewport.Height;
                }
            };

            // Eventos de botones de figuras
            btnCubo.Click += (s, e) => AgregarCubo();
            btnEsfera.Click += (s, e) => AgregarEsfera();
            btnCilindro.Click += (s, e) => AgregarCilindro();
            btnCono.Click += (s, e) => AgregarCono();
            btnPiramide.Click += (s, e) => AgregarPiramide();
            btnToroide.Click += (s, e) => AgregarToroide();

            // Eventos de transformaciones
            numPosX.ValueChanged += (s, e) => ActualizarTransformacion();
            numPosY.ValueChanged += (s, e) => ActualizarTransformacion();
            numPosZ.ValueChanged += (s, e) => ActualizarTransformacion();

            numRotX.ValueChanged += (s, e) => ActualizarTransformacion();
            numRotY.ValueChanged += (s, e) => ActualizarTransformacion();
            numRotZ.ValueChanged += (s, e) => ActualizarTransformacion();

            numEscX.ValueChanged += (s, e) => ActualizarTransformacion();
            numEscY.ValueChanged += (s, e) => ActualizarTransformacion();
            numEscZ.ValueChanged += (s, e) => ActualizarTransformacion();

            // Eventos de lista de objetos
            listObjetos.SelectedIndexChanged += ListObjetos_SelectedIndexChanged;
            listObjetos.KeyDown += (s, e) => {
                if (e.KeyCode == Keys.Delete && listObjetos.SelectedItem != null)
                {
                    EliminarFiguraSeleccionada();
                }
            };

            // Eventos de propiedades visuales
            btnColorLinea.Click += (s, e) => CambiarColorLinea();
            btnColorRelleno.Click += (s, e) => CambiarColorRelleno();
            chkMostrarRelleno.CheckedChanged += (s, e) => {
                if (figuraSeleccionada != null)
                {
                    figuraSeleccionada.MostrarRelleno = chkMostrarRelleno.Checked;
                }
            };
            chkVisible.CheckedChanged += (s, e) => {
                if (figuraSeleccionada != null)
                {
                    figuraSeleccionada.Visible = chkVisible.Checked;
                    ActualizarListaObjetos();
                }
            };

            // Botones de utilidad
            btnDuplicar.Click += (s, e) => DuplicarFiguraSeleccionada();
            btnEliminar.Click += (s, e) => EliminarFiguraSeleccionada();
            btnResetCamara.Click += (s, e) => ResetearCamara();

            chkMostrarEjes.CheckedChanged += (s, e) => RenderizarEscena();
            chkMostrarGrid.CheckedChanged += (s, e) => RenderizarEscena();
        }

        #region Agregar Figuras

        private void AgregarCubo()
        {
            var cubo = Figura3D.CrearCubo(1.0);
            cubo.Nombre = $"Cubo_{figuras.Count + 1}";
            AgregarFigura(cubo);
        }

        private void AgregarEsfera()
        {
            var esfera = Figura3D.CrearEsfera(1.0, 16, 12);
            esfera.Nombre = $"Esfera_{figuras.Count + 1}";
            AgregarFigura(esfera);
        }

        private void AgregarCilindro()
        {
            var cilindro = Figura3D.CrearCilindro(0.5, 2.0, 16);
            cilindro.Nombre = $"Cilindro_{figuras.Count + 1}";
            AgregarFigura(cilindro);
        }

        private void AgregarCono()
        {
            var cono = Figura3D.CrearCono(1.0, 2.0, 16);
            cono.Nombre = $"Cono_{figuras.Count + 1}";
            AgregarFigura(cono);
        }

        private void AgregarPiramide()
        {
            var piramide = Figura3D.CrearPiramide(1.0);
            piramide.Nombre = $"Pirámide_{figuras.Count + 1}";
            AgregarFigura(piramide);
        }

        private void AgregarToroide()
        {
            var toroide = Figura3D.CrearToroide(1.5, 0.5, 24, 16);
            toroide.Nombre = $"Toroide_{figuras.Count + 1}";
            AgregarFigura(toroide);
        }

        private void AgregarFigura(Figura3D figura)
        {
            figuras.Add(figura);
            ActualizarListaObjetos();
            SeleccionarFigura(figura);
        }

        #endregion

        #region Interacción con Mouse

        private void PanelViewport_MouseDown(object sender, MouseEventArgs e)
        {
            if (e.Button == MouseButtons.Middle || 
                (e.Button == MouseButtons.Left && ModifierKeys == Keys.Shift))
            {
                mousePanear = true;
            }
            else if (e.Button == MouseButtons.Left)
            {
                mousePresionado = true;
            }
            
            ultimaPosicionMouse = e.Location;
        }

        private void PanelViewport_MouseMove(object sender, MouseEventArgs e)
        {
            if (mousePresionado && !mousePanear)
            {
                // Rotar cámara
                double deltaX = e.X - ultimaPosicionMouse.X;
                double deltaY = e.Y - ultimaPosicionMouse.Y;

                motor.RotarCamara(deltaX * 0.5, -deltaY * 0.5);
            }
            else if (mousePanear)
            {
                // Panear cámara
                double deltaX = e.X - ultimaPosicionMouse.X;
                double deltaY = e.Y - ultimaPosicionMouse.Y;

                motor.PanearCamara(-deltaX, deltaY);
            }

            ultimaPosicionMouse = e.Location;
        }

        private void PanelViewport_MouseUp(object sender, MouseEventArgs e)
        {
            mousePresionado = false;
            mousePanear = false;
        }

        private void PanelViewport_MouseWheel(object sender, MouseEventArgs e)
        {
            motor.ZoomCamara(-e.Delta * 0.005);
        }

        #endregion

        #region Renderizado

        private void RenderizarEscena()
        {
            if (bufferImagen == null) return;

            using (Graphics g = Graphics.FromImage(bufferImagen))
            {
                // Fondo oscuro estilo Blender
                g.Clear(Color.FromArgb(50, 50, 50));
                g.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.AntiAlias;

                // Dibujar grid si está habilitado
                if (chkMostrarGrid.Checked)
                {
                    motor.DibujarGrid(g, 10, 1);
                }

                // Dibujar ejes si está habilitado
                if (chkMostrarEjes.Checked)
                {
                    motor.DibujarEjes(g, 2);
                }

                // Aplicar transformaciones y dibujar figuras
                foreach (var figura in figuras)
                {
                    motor.AplicarTransformaciones(figura);
                    motor.DibujarFigura(g, figura);
                }
            }

            panelViewport.Invalidate();
        }

        private void PanelViewport_Paint(object sender, PaintEventArgs e)
        {
            if (bufferImagen != null)
            {
                e.Graphics.DrawImage(bufferImagen, 0, 0);
            }
        }

        #endregion

        #region Gestión de Objetos

        private void ActualizarListaObjetos()
        {
            int selectedIndex = listObjetos.SelectedIndex;
            listObjetos.Items.Clear();
            
            foreach (var figura in figuras)
            {
                string icono = figura.Visible ? "👁" : "🚫";
                listObjetos.Items.Add($"{icono} {figura.Nombre}");
            }

            if (selectedIndex >= 0 && selectedIndex < listObjetos.Items.Count)
            {
                listObjetos.SelectedIndex = selectedIndex;
            }
        }

        private void ListObjetos_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (listObjetos.SelectedIndex >= 0 && listObjetos.SelectedIndex < figuras.Count)
            {
                SeleccionarFigura(figuras[listObjetos.SelectedIndex]);
            }
        }

        private void SeleccionarFigura(Figura3D figura)
        {
            // Deseleccionar todas
            foreach (var f in figuras)
                f.Seleccionada = false;

            figuraSeleccionada = figura;
            figura.Seleccionada = true;

            // Actualizar UI
            ActualizarPanelPropiedades();
            
            int index = figuras.IndexOf(figura);
            if (index >= 0)
                listObjetos.SelectedIndex = index;
        }

        private void ActualizarPanelPropiedades()
        {
            if (figuraSeleccionada == null)
            {
                panelPropiedades.Enabled = false;
                return;
            }

            panelPropiedades.Enabled = true;

            // Actualizar valores sin disparar eventos
            numPosX.ValueChanged -= ActualizarTransformacion;
            numPosY.ValueChanged -= ActualizarTransformacion;
            numPosZ.ValueChanged -= ActualizarTransformacion;
            numRotX.ValueChanged -= ActualizarTransformacion;
            numRotY.ValueChanged -= ActualizarTransformacion;
            numRotZ.ValueChanged -= ActualizarTransformacion;
            numEscX.ValueChanged -= ActualizarTransformacion;
            numEscY.ValueChanged -= ActualizarTransformacion;
            numEscZ.ValueChanged -= ActualizarTransformacion;

            numPosX.Value = (decimal)figuraSeleccionada.Posicion.X;
            numPosY.Value = (decimal)figuraSeleccionada.Posicion.Y;
            numPosZ.Value = (decimal)figuraSeleccionada.Posicion.Z;

            numRotX.Value = (decimal)figuraSeleccionada.Rotacion.X;
            numRotY.Value = (decimal)figuraSeleccionada.Rotacion.Y;
            numRotZ.Value = (decimal)figuraSeleccionada.Rotacion.Z;

            numEscX.Value = (decimal)figuraSeleccionada.Escala.X;
            numEscY.Value = (decimal)figuraSeleccionada.Escala.Y;
            numEscZ.Value = (decimal)figuraSeleccionada.Escala.Z;

            chkMostrarRelleno.Checked = figuraSeleccionada.MostrarRelleno;
            chkVisible.Checked = figuraSeleccionada.Visible;

            btnColorLinea.BackColor = figuraSeleccionada.ColorLinea;
            btnColorRelleno.BackColor = figuraSeleccionada.ColorRelleno;

            // Re-suscribir eventos
            numPosX.ValueChanged += ActualizarTransformacion;
            numPosY.ValueChanged += ActualizarTransformacion;
            numPosZ.ValueChanged += ActualizarTransformacion;
            numRotX.ValueChanged += ActualizarTransformacion;
            numRotY.ValueChanged += ActualizarTransformacion;
            numRotZ.ValueChanged += ActualizarTransformacion;
            numEscX.ValueChanged += ActualizarTransformacion;
            numEscY.ValueChanged += ActualizarTransformacion;
            numEscZ.ValueChanged += ActualizarTransformacion;
        }

        private void ActualizarTransformacion(object sender = null, EventArgs e = null)
        {
            if (figuraSeleccionada == null) return;

            figuraSeleccionada.Posicion.X = (double)numPosX.Value;
            figuraSeleccionada.Posicion.Y = (double)numPosY.Value;
            figuraSeleccionada.Posicion.Z = (double)numPosZ.Value;

            figuraSeleccionada.Rotacion.X = (double)numRotX.Value;
            figuraSeleccionada.Rotacion.Y = (double)numRotY.Value;
            figuraSeleccionada.Rotacion.Z = (double)numRotZ.Value;

            figuraSeleccionada.Escala.X = (double)numEscX.Value;
            figuraSeleccionada.Escala.Y = (double)numEscY.Value;
            figuraSeleccionada.Escala.Z = (double)numEscZ.Value;
        }

        private void CambiarColorLinea()
        {
            if (figuraSeleccionada == null) return;

            using (ColorDialog dlg = new ColorDialog())
            {
                dlg.Color = figuraSeleccionada.ColorLinea;
                if (dlg.ShowDialog() == DialogResult.OK)
                {
                    figuraSeleccionada.ColorLinea = dlg.Color;
                    btnColorLinea.BackColor = dlg.Color;
                }
            }
        }

        private void CambiarColorRelleno()
        {
            if (figuraSeleccionada == null) return;

            using (ColorDialog dlg = new ColorDialog())
            {
                dlg.Color = figuraSeleccionada.ColorRelleno;
                if (dlg.ShowDialog() == DialogResult.OK)
                {
                    figuraSeleccionada.ColorRelleno = dlg.Color;
                    btnColorRelleno.BackColor = dlg.Color;
                }
            }
        }

        private void DuplicarFiguraSeleccionada()
        {
            if (figuraSeleccionada == null) return;

            var duplicado = figuraSeleccionada.Clonar();
            duplicado.Posicion.X += 1.5; // Desplazar un poco
            AgregarFigura(duplicado);
        }

        private void EliminarFiguraSeleccionada()
        {
            if (figuraSeleccionada == null) return;

            figuras.Remove(figuraSeleccionada);
            figuraSeleccionada = null;
            ActualizarListaObjetos();
            panelPropiedades.Enabled = false;
        }

        private void ResetearCamara()
        {
            motor.AnguloOrbitaH = 45;
            motor.AnguloOrbitaV = 30;
            motor.DistanciaCamara = 5;
            motor.ObjetivoCamara = new Punto3D(0, 0, 0);
            motor.ActualizarPosicionCamara();
        }

        #endregion
    }
}
