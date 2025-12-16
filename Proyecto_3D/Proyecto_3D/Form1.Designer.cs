namespace Proyecto_3D
{
    partial class Form1
    {
        /// <summary>
        /// Variable del diseñador necesaria.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Limpiar los recursos que se estén usando.
        /// </summary>
        /// <param name="disposing">true si los recursos administrados se deben desechar; false en caso contrario.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Código generado por el Diseñador de Windows Forms

        /// <summary>
        /// Método necesario para admitir el Diseñador. No se puede modificar
        /// el contenido de este método con el editor de código.
        /// </summary>
        private void InitializeComponent()
        {
            this.panelSuperior = new System.Windows.Forms.Panel();
            this.lblTitulo = new System.Windows.Forms.Label();
            this.panelIzquierdo = new System.Windows.Forms.Panel();
            this.groupBoxObjetos = new System.Windows.Forms.GroupBox();
            this.listObjetos = new System.Windows.Forms.ListBox();
            this.panelBotonesObj = new System.Windows.Forms.Panel();
            this.btnDuplicar = new System.Windows.Forms.Button();
            this.btnEliminar = new System.Windows.Forms.Button();
            this.groupBoxAgregar = new System.Windows.Forms.GroupBox();
            this.btnToroide = new System.Windows.Forms.Button();
            this.btnPiramide = new System.Windows.Forms.Button();
            this.btnCono = new System.Windows.Forms.Button();
            this.btnCilindro = new System.Windows.Forms.Button();
            this.btnEsfera = new System.Windows.Forms.Button();
            this.btnCubo = new System.Windows.Forms.Button();
            this.panelDerecho = new System.Windows.Forms.Panel();
            this.panelPropiedades = new System.Windows.Forms.Panel();
            this.groupBoxApariencia = new System.Windows.Forms.GroupBox();
            this.chkVisible = new System.Windows.Forms.CheckBox();
            this.chkMostrarRelleno = new System.Windows.Forms.CheckBox();
            this.btnColorRelleno = new System.Windows.Forms.Button();
            this.lblColorRelleno = new System.Windows.Forms.Label();
            this.btnColorLinea = new System.Windows.Forms.Button();
            this.lblColorLinea = new System.Windows.Forms.Label();
            this.groupBoxEscala = new System.Windows.Forms.GroupBox();
            this.numEscZ = new System.Windows.Forms.NumericUpDown();
            this.numEscY = new System.Windows.Forms.NumericUpDown();
            this.numEscX = new System.Windows.Forms.NumericUpDown();
            this.lblEscZ = new System.Windows.Forms.Label();
            this.lblEscY = new System.Windows.Forms.Label();
            this.lblEscX = new System.Windows.Forms.Label();
            this.groupBoxRotacion = new System.Windows.Forms.GroupBox();
            this.numRotZ = new System.Windows.Forms.NumericUpDown();
            this.numRotY = new System.Windows.Forms.NumericUpDown();
            this.numRotX = new System.Windows.Forms.NumericUpDown();
            this.lblRotZ = new System.Windows.Forms.Label();
            this.lblRotY = new System.Windows.Forms.Label();
            this.lblRotX = new System.Windows.Forms.Label();
            this.groupBoxPosicion = new System.Windows.Forms.GroupBox();
            this.numPosZ = new System.Windows.Forms.NumericUpDown();
            this.numPosY = new System.Windows.Forms.NumericUpDown();
            this.numPosX = new System.Windows.Forms.NumericUpDown();
            this.lblPosZ = new System.Windows.Forms.Label();
            this.lblPosY = new System.Windows.Forms.Label();
            this.lblPosX = new System.Windows.Forms.Label();
            this.groupBoxVista = new System.Windows.Forms.GroupBox();
            this.btnResetCamara = new System.Windows.Forms.Button();
            this.chkMostrarGrid = new System.Windows.Forms.CheckBox();
            this.chkMostrarEjes = new System.Windows.Forms.CheckBox();
            this.panelViewport = new System.Windows.Forms.Panel();
            this.lblIntensidadLuz = new System.Windows.Forms.Label();
            this.trackBarIntensidadLuz = new System.Windows.Forms.TrackBar();
            this.lblTextura = new System.Windows.Forms.Label();
            this.cmbTextura = new System.Windows.Forms.ComboBox();
            this.panelSuperior.SuspendLayout();
            this.panelIzquierdo.SuspendLayout();
            this.groupBoxObjetos.SuspendLayout();
            this.panelBotonesObj.SuspendLayout();
            this.groupBoxAgregar.SuspendLayout();
            this.panelDerecho.SuspendLayout();
            this.panelPropiedades.SuspendLayout();
            this.groupBoxApariencia.SuspendLayout();
            this.groupBoxEscala.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.numEscZ)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.numEscY)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.numEscX)).BeginInit();
            this.groupBoxRotacion.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.numRotZ)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.numRotY)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.numRotX)).BeginInit();
            this.groupBoxPosicion.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.numPosZ)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.numPosY)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.numPosX)).BeginInit();
            this.groupBoxVista.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.trackBarIntensidadLuz)).BeginInit();
            this.SuspendLayout();
            // 
            // panelSuperior
            // 
            this.panelSuperior.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(40)))), ((int)(((byte)(40)))), ((int)(((byte)(40)))));
            this.panelSuperior.Controls.Add(this.lblTitulo);
            this.panelSuperior.Dock = System.Windows.Forms.DockStyle.Top;
            this.panelSuperior.Location = new System.Drawing.Point(0, 0);
            this.panelSuperior.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.panelSuperior.Name = "panelSuperior";
            this.panelSuperior.Size = new System.Drawing.Size(1800, 67);
            this.panelSuperior.TabIndex = 0;
            // 
            // lblTitulo
            // 
            this.lblTitulo.AutoSize = true;
            this.lblTitulo.Font = new System.Drawing.Font("Segoe UI", 16F, System.Drawing.FontStyle.Bold);
            this.lblTitulo.ForeColor = System.Drawing.Color.White;
            this.lblTitulo.Location = new System.Drawing.Point(15, 12);
            this.lblTitulo.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblTitulo.Name = "lblTitulo";
            this.lblTitulo.Size = new System.Drawing.Size(442, 45);
            this.lblTitulo.TabIndex = 0;
            this.lblTitulo.Text = "Editor 3D - Proyecto Gráfica";
            // 
            // panelIzquierdo
            // 
            this.panelIzquierdo.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(60)))), ((int)(((byte)(60)))), ((int)(((byte)(60)))));
            this.panelIzquierdo.Controls.Add(this.groupBoxObjetos);
            this.panelIzquierdo.Controls.Add(this.groupBoxAgregar);
            this.panelIzquierdo.Dock = System.Windows.Forms.DockStyle.Left;
            this.panelIzquierdo.Location = new System.Drawing.Point(0, 67);
            this.panelIzquierdo.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.panelIzquierdo.Name = "panelIzquierdo";
            this.panelIzquierdo.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.panelIzquierdo.Size = new System.Drawing.Size(321, 983);
            this.panelIzquierdo.TabIndex = 1;
            // 
            // groupBoxObjetos
            // 
            this.groupBoxObjetos.Controls.Add(this.listObjetos);
            this.groupBoxObjetos.Controls.Add(this.panelBotonesObj);
            this.groupBoxObjetos.Dock = System.Windows.Forms.DockStyle.Fill;
            this.groupBoxObjetos.ForeColor = System.Drawing.Color.White;
            this.groupBoxObjetos.Location = new System.Drawing.Point(13, 333);
            this.groupBoxObjetos.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxObjetos.Name = "groupBoxObjetos";
            this.groupBoxObjetos.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxObjetos.Size = new System.Drawing.Size(295, 637);
            this.groupBoxObjetos.TabIndex = 1;
            this.groupBoxObjetos.TabStop = false;
            this.groupBoxObjetos.Text = "Objetos en Escena";
            // 
            // listObjetos
            // 
            this.listObjetos.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.listObjetos.Dock = System.Windows.Forms.DockStyle.Fill;
            this.listObjetos.ForeColor = System.Drawing.Color.White;
            this.listObjetos.FormattingEnabled = true;
            this.listObjetos.ItemHeight = 20;
            this.listObjetos.Location = new System.Drawing.Point(13, 32);
            this.listObjetos.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.listObjetos.Name = "listObjetos";
            this.listObjetos.Size = new System.Drawing.Size(269, 539);
            this.listObjetos.TabIndex = 1;
            // 
            // panelBotonesObj
            // 
            this.panelBotonesObj.Controls.Add(this.btnDuplicar);
            this.panelBotonesObj.Controls.Add(this.btnEliminar);
            this.panelBotonesObj.Dock = System.Windows.Forms.DockStyle.Bottom;
            this.panelBotonesObj.Location = new System.Drawing.Point(13, 571);
            this.panelBotonesObj.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.panelBotonesObj.Name = "panelBotonesObj";
            this.panelBotonesObj.Size = new System.Drawing.Size(269, 53);
            this.panelBotonesObj.TabIndex = 0;
            // 
            // btnDuplicar
            // 
            this.btnDuplicar.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(70)))), ((int)(((byte)(120)))), ((int)(((byte)(180)))));
            this.btnDuplicar.Dock = System.Windows.Forms.DockStyle.Left;
            this.btnDuplicar.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnDuplicar.ForeColor = System.Drawing.Color.White;
            this.btnDuplicar.Location = new System.Drawing.Point(0, 0);
            this.btnDuplicar.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnDuplicar.Name = "btnDuplicar";
            this.btnDuplicar.Size = new System.Drawing.Size(129, 53);
            this.btnDuplicar.TabIndex = 1;
            this.btnDuplicar.Text = "Duplicar";
            this.btnDuplicar.UseVisualStyleBackColor = false;
            // 
            // btnEliminar
            // 
            this.btnEliminar.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(180)))), ((int)(((byte)(60)))), ((int)(((byte)(60)))));
            this.btnEliminar.Dock = System.Windows.Forms.DockStyle.Right;
            this.btnEliminar.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnEliminar.ForeColor = System.Drawing.Color.White;
            this.btnEliminar.Location = new System.Drawing.Point(140, 0);
            this.btnEliminar.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnEliminar.Name = "btnEliminar";
            this.btnEliminar.Size = new System.Drawing.Size(129, 53);
            this.btnEliminar.TabIndex = 0;
            this.btnEliminar.Text = "Eliminar";
            this.btnEliminar.UseVisualStyleBackColor = false;
            // 
            // groupBoxAgregar
            // 
            this.groupBoxAgregar.Controls.Add(this.btnToroide);
            this.groupBoxAgregar.Controls.Add(this.btnPiramide);
            this.groupBoxAgregar.Controls.Add(this.btnCono);
            this.groupBoxAgregar.Controls.Add(this.btnCilindro);
            this.groupBoxAgregar.Controls.Add(this.btnEsfera);
            this.groupBoxAgregar.Controls.Add(this.btnCubo);
            this.groupBoxAgregar.Dock = System.Windows.Forms.DockStyle.Top;
            this.groupBoxAgregar.ForeColor = System.Drawing.Color.White;
            this.groupBoxAgregar.Location = new System.Drawing.Point(13, 13);
            this.groupBoxAgregar.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxAgregar.Name = "groupBoxAgregar";
            this.groupBoxAgregar.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxAgregar.Size = new System.Drawing.Size(295, 320);
            this.groupBoxAgregar.TabIndex = 0;
            this.groupBoxAgregar.TabStop = false;
            this.groupBoxAgregar.Text = "Agregar Figura";
            // 
            // btnToroide
            // 
            this.btnToroide.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(80)))), ((int)(((byte)(80)))), ((int)(((byte)(80)))));
            this.btnToroide.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnToroide.Location = new System.Drawing.Point(17, 260);
            this.btnToroide.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnToroide.Name = "btnToroide";
            this.btnToroide.Size = new System.Drawing.Size(262, 47);
            this.btnToroide.TabIndex = 5;
            this.btnToroide.Text = "🍩 Toroide";
            this.btnToroide.UseVisualStyleBackColor = false;
            // 
            // btnPiramide
            // 
            this.btnPiramide.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(80)))), ((int)(((byte)(80)))), ((int)(((byte)(80)))));
            this.btnPiramide.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnPiramide.Location = new System.Drawing.Point(17, 213);
            this.btnPiramide.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnPiramide.Name = "btnPiramide";
            this.btnPiramide.Size = new System.Drawing.Size(262, 47);
            this.btnPiramide.TabIndex = 4;
            this.btnPiramide.Text = "🔺 Pirámide";
            this.btnPiramide.UseVisualStyleBackColor = false;
            // 
            // btnCono
            // 
            this.btnCono.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(80)))), ((int)(((byte)(80)))), ((int)(((byte)(80)))));
            this.btnCono.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCono.Location = new System.Drawing.Point(17, 167);
            this.btnCono.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnCono.Name = "btnCono";
            this.btnCono.Size = new System.Drawing.Size(262, 47);
            this.btnCono.TabIndex = 3;
            this.btnCono.Text = "🎩 Cono";
            this.btnCono.UseVisualStyleBackColor = false;
            // 
            // btnCilindro
            // 
            this.btnCilindro.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(80)))), ((int)(((byte)(80)))), ((int)(((byte)(80)))));
            this.btnCilindro.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCilindro.Location = new System.Drawing.Point(17, 120);
            this.btnCilindro.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnCilindro.Name = "btnCilindro";
            this.btnCilindro.Size = new System.Drawing.Size(262, 47);
            this.btnCilindro.TabIndex = 2;
            this.btnCilindro.Text = "📦 Cilindro";
            this.btnCilindro.UseVisualStyleBackColor = false;
            // 
            // btnEsfera
            // 
            this.btnEsfera.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(80)))), ((int)(((byte)(80)))), ((int)(((byte)(80)))));
            this.btnEsfera.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnEsfera.Location = new System.Drawing.Point(17, 73);
            this.btnEsfera.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnEsfera.Name = "btnEsfera";
            this.btnEsfera.Size = new System.Drawing.Size(262, 47);
            this.btnEsfera.TabIndex = 1;
            this.btnEsfera.Text = "⚽ Esfera";
            this.btnEsfera.UseVisualStyleBackColor = false;
            // 
            // btnCubo
            // 
            this.btnCubo.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(80)))), ((int)(((byte)(80)))), ((int)(((byte)(80)))));
            this.btnCubo.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCubo.Location = new System.Drawing.Point(17, 27);
            this.btnCubo.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnCubo.Name = "btnCubo";
            this.btnCubo.Size = new System.Drawing.Size(262, 47);
            this.btnCubo.TabIndex = 0;
            this.btnCubo.Text = "🎲 Cubo";
            this.btnCubo.UseVisualStyleBackColor = false;
            // 
            // panelDerecho
            // 
            this.panelDerecho.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(60)))), ((int)(((byte)(60)))), ((int)(((byte)(60)))));
            this.panelDerecho.Controls.Add(this.panelPropiedades);
            this.panelDerecho.Controls.Add(this.groupBoxVista);
            this.panelDerecho.Dock = System.Windows.Forms.DockStyle.Right;
            this.panelDerecho.Location = new System.Drawing.Point(1414, 67);
            this.panelDerecho.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.panelDerecho.Name = "panelDerecho";
            this.panelDerecho.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.panelDerecho.Size = new System.Drawing.Size(386, 983);
            this.panelDerecho.TabIndex = 2;
            // 
            // panelPropiedades
            // 
            this.panelPropiedades.AutoScroll = true;
            this.panelPropiedades.Controls.Add(this.groupBoxApariencia);
            this.panelPropiedades.Controls.Add(this.groupBoxEscala);
            this.panelPropiedades.Controls.Add(this.groupBoxRotacion);
            this.panelPropiedades.Controls.Add(this.groupBoxPosicion);
            this.panelPropiedades.Dock = System.Windows.Forms.DockStyle.Fill;
            this.panelPropiedades.Enabled = false;
            this.panelPropiedades.Location = new System.Drawing.Point(13, 160);
            this.panelPropiedades.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.panelPropiedades.Name = "panelPropiedades";
            this.panelPropiedades.Size = new System.Drawing.Size(360, 810);
            this.panelPropiedades.TabIndex = 1;
            // 
            // groupBoxApariencia
            // 
            this.groupBoxApariencia.Controls.Add(this.cmbTextura);
            this.groupBoxApariencia.Controls.Add(this.lblTextura);
            this.groupBoxApariencia.Controls.Add(this.trackBarIntensidadLuz);
            this.groupBoxApariencia.Controls.Add(this.lblIntensidadLuz);
            this.groupBoxApariencia.Controls.Add(this.chkVisible);
            this.groupBoxApariencia.Controls.Add(this.chkMostrarRelleno);
            this.groupBoxApariencia.Controls.Add(this.btnColorRelleno);
            this.groupBoxApariencia.Controls.Add(this.lblColorRelleno);
            this.groupBoxApariencia.Controls.Add(this.btnColorLinea);
            this.groupBoxApariencia.Controls.Add(this.lblColorLinea);
            this.groupBoxApariencia.Dock = System.Windows.Forms.DockStyle.Top;
            this.groupBoxApariencia.ForeColor = System.Drawing.Color.White;
            this.groupBoxApariencia.Location = new System.Drawing.Point(0, 519);
            this.groupBoxApariencia.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxApariencia.Name = "groupBoxApariencia";
            this.groupBoxApariencia.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxApariencia.Size = new System.Drawing.Size(334, 458);
            this.groupBoxApariencia.TabIndex = 3;
            this.groupBoxApariencia.TabStop = false;
            this.groupBoxApariencia.Text = "Apariencia";
            // 
            // chkVisible
            // 
            this.chkVisible.AutoSize = true;
            this.chkVisible.Checked = true;
            this.chkVisible.CheckState = System.Windows.Forms.CheckState.Checked;
            this.chkVisible.Location = new System.Drawing.Point(17, 193);
            this.chkVisible.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.chkVisible.Name = "chkVisible";
            this.chkVisible.Size = new System.Drawing.Size(81, 24);
            this.chkVisible.TabIndex = 5;
            this.chkVisible.Text = "Visible";
            this.chkVisible.UseVisualStyleBackColor = true;
            // 
            // chkMostrarRelleno
            // 
            this.chkMostrarRelleno.AutoSize = true;
            this.chkMostrarRelleno.Checked = true;
            this.chkMostrarRelleno.CheckState = System.Windows.Forms.CheckState.Checked;
            this.chkMostrarRelleno.Location = new System.Drawing.Point(17, 160);
            this.chkMostrarRelleno.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.chkMostrarRelleno.Name = "chkMostrarRelleno";
            this.chkMostrarRelleno.Size = new System.Drawing.Size(147, 24);
            this.chkMostrarRelleno.TabIndex = 4;
            this.chkMostrarRelleno.Text = "Mostrar Relleno";
            this.chkMostrarRelleno.UseVisualStyleBackColor = true;
            // 
            // btnColorRelleno
            // 
            this.btnColorRelleno.BackColor = System.Drawing.Color.Blue;
            this.btnColorRelleno.Location = new System.Drawing.Point(17, 107);
            this.btnColorRelleno.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnColorRelleno.Name = "btnColorRelleno";
            this.btnColorRelleno.Size = new System.Drawing.Size(327, 40);
            this.btnColorRelleno.TabIndex = 3;
            this.btnColorRelleno.UseVisualStyleBackColor = false;
            // 
            // lblColorRelleno
            // 
            this.lblColorRelleno.AutoSize = true;
            this.lblColorRelleno.Location = new System.Drawing.Point(17, 83);
            this.lblColorRelleno.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblColorRelleno.Name = "lblColorRelleno";
            this.lblColorRelleno.Size = new System.Drawing.Size(119, 20);
            this.lblColorRelleno.TabIndex = 2;
            this.lblColorRelleno.Text = "Color de relleno";
            // 
            // btnColorLinea
            // 
            this.btnColorLinea.BackColor = System.Drawing.Color.White;
            this.btnColorLinea.Location = new System.Drawing.Point(17, 53);
            this.btnColorLinea.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnColorLinea.Name = "btnColorLinea";
            this.btnColorLinea.Size = new System.Drawing.Size(327, 40);
            this.btnColorLinea.TabIndex = 1;
            this.btnColorLinea.UseVisualStyleBackColor = false;
            // 
            // lblColorLinea
            // 
            this.lblColorLinea.AutoSize = true;
            this.lblColorLinea.Location = new System.Drawing.Point(17, 29);
            this.lblColorLinea.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblColorLinea.Name = "lblColorLinea";
            this.lblColorLinea.Size = new System.Drawing.Size(113, 20);
            this.lblColorLinea.TabIndex = 0;
            this.lblColorLinea.Text = "Color de líneas";
            // 
            // groupBoxEscala
            // 
            this.groupBoxEscala.Controls.Add(this.numEscZ);
            this.groupBoxEscala.Controls.Add(this.numEscY);
            this.groupBoxEscala.Controls.Add(this.numEscX);
            this.groupBoxEscala.Controls.Add(this.lblEscZ);
            this.groupBoxEscala.Controls.Add(this.lblEscY);
            this.groupBoxEscala.Controls.Add(this.lblEscX);
            this.groupBoxEscala.Dock = System.Windows.Forms.DockStyle.Top;
            this.groupBoxEscala.ForeColor = System.Drawing.Color.White;
            this.groupBoxEscala.Location = new System.Drawing.Point(0, 346);
            this.groupBoxEscala.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxEscala.Name = "groupBoxEscala";
            this.groupBoxEscala.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxEscala.Size = new System.Drawing.Size(334, 173);
            this.groupBoxEscala.TabIndex = 2;
            this.groupBoxEscala.TabStop = false;
            this.groupBoxEscala.Text = "Escala";
            // 
            // numEscZ
            // 
            this.numEscZ.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numEscZ.DecimalPlaces = 2;
            this.numEscZ.ForeColor = System.Drawing.Color.White;
            this.numEscZ.Increment = new decimal(new int[] {
            1,
            0,
            0,
            65536});
            this.numEscZ.Location = new System.Drawing.Point(51, 120);
            this.numEscZ.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numEscZ.Maximum = new decimal(new int[] {
            10,
            0,
            0,
            0});
            this.numEscZ.Minimum = new decimal(new int[] {
            1,
            0,
            0,
            131072});
            this.numEscZ.Name = "numEscZ";
            this.numEscZ.Size = new System.Drawing.Size(292, 26);
            this.numEscZ.TabIndex = 5;
            this.numEscZ.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
            // 
            // numEscY
            // 
            this.numEscY.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numEscY.DecimalPlaces = 2;
            this.numEscY.ForeColor = System.Drawing.Color.White;
            this.numEscY.Increment = new decimal(new int[] {
            1,
            0,
            0,
            65536});
            this.numEscY.Location = new System.Drawing.Point(51, 80);
            this.numEscY.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numEscY.Maximum = new decimal(new int[] {
            10,
            0,
            0,
            0});
            this.numEscY.Minimum = new decimal(new int[] {
            1,
            0,
            0,
            131072});
            this.numEscY.Name = "numEscY";
            this.numEscY.Size = new System.Drawing.Size(292, 26);
            this.numEscY.TabIndex = 4;
            this.numEscY.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
            // 
            // numEscX
            // 
            this.numEscX.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numEscX.DecimalPlaces = 2;
            this.numEscX.ForeColor = System.Drawing.Color.White;
            this.numEscX.Increment = new decimal(new int[] {
            1,
            0,
            0,
            65536});
            this.numEscX.Location = new System.Drawing.Point(51, 40);
            this.numEscX.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numEscX.Maximum = new decimal(new int[] {
            10,
            0,
            0,
            0});
            this.numEscX.Minimum = new decimal(new int[] {
            1,
            0,
            0,
            131072});
            this.numEscX.Name = "numEscX";
            this.numEscX.Size = new System.Drawing.Size(292, 26);
            this.numEscX.TabIndex = 3;
            this.numEscX.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
            // 
            // lblEscZ
            // 
            this.lblEscZ.AutoSize = true;
            this.lblEscZ.Location = new System.Drawing.Point(17, 123);
            this.lblEscZ.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblEscZ.Name = "lblEscZ";
            this.lblEscZ.Size = new System.Drawing.Size(23, 20);
            this.lblEscZ.TabIndex = 2;
            this.lblEscZ.Text = "Z:";
            // 
            // lblEscY
            // 
            this.lblEscY.AutoSize = true;
            this.lblEscY.Location = new System.Drawing.Point(17, 83);
            this.lblEscY.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblEscY.Name = "lblEscY";
            this.lblEscY.Size = new System.Drawing.Size(24, 20);
            this.lblEscY.TabIndex = 1;
            this.lblEscY.Text = "Y:";
            // 
            // lblEscX
            // 
            this.lblEscX.AutoSize = true;
            this.lblEscX.Location = new System.Drawing.Point(17, 43);
            this.lblEscX.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblEscX.Name = "lblEscX";
            this.lblEscX.Size = new System.Drawing.Size(24, 20);
            this.lblEscX.TabIndex = 0;
            this.lblEscX.Text = "X:";
            // 
            // groupBoxRotacion
            // 
            this.groupBoxRotacion.Controls.Add(this.numRotZ);
            this.groupBoxRotacion.Controls.Add(this.numRotY);
            this.groupBoxRotacion.Controls.Add(this.numRotX);
            this.groupBoxRotacion.Controls.Add(this.lblRotZ);
            this.groupBoxRotacion.Controls.Add(this.lblRotY);
            this.groupBoxRotacion.Controls.Add(this.lblRotX);
            this.groupBoxRotacion.Dock = System.Windows.Forms.DockStyle.Top;
            this.groupBoxRotacion.ForeColor = System.Drawing.Color.White;
            this.groupBoxRotacion.Location = new System.Drawing.Point(0, 173);
            this.groupBoxRotacion.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxRotacion.Name = "groupBoxRotacion";
            this.groupBoxRotacion.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxRotacion.Size = new System.Drawing.Size(334, 173);
            this.groupBoxRotacion.TabIndex = 1;
            this.groupBoxRotacion.TabStop = false;
            this.groupBoxRotacion.Text = "Rotación (grados)";
            // 
            // numRotZ
            // 
            this.numRotZ.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numRotZ.DecimalPlaces = 1;
            this.numRotZ.ForeColor = System.Drawing.Color.White;
            this.numRotZ.Location = new System.Drawing.Point(51, 120);
            this.numRotZ.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numRotZ.Maximum = new decimal(new int[] {
            360,
            0,
            0,
            0});
            this.numRotZ.Minimum = new decimal(new int[] {
            360,
            0,
            0,
            -2147483648});
            this.numRotZ.Name = "numRotZ";
            this.numRotZ.Size = new System.Drawing.Size(292, 26);
            this.numRotZ.TabIndex = 5;
            // 
            // numRotY
            // 
            this.numRotY.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numRotY.DecimalPlaces = 1;
            this.numRotY.ForeColor = System.Drawing.Color.White;
            this.numRotY.Location = new System.Drawing.Point(51, 80);
            this.numRotY.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numRotY.Maximum = new decimal(new int[] {
            360,
            0,
            0,
            0});
            this.numRotY.Minimum = new decimal(new int[] {
            360,
            0,
            0,
            -2147483648});
            this.numRotY.Name = "numRotY";
            this.numRotY.Size = new System.Drawing.Size(292, 26);
            this.numRotY.TabIndex = 4;
            // 
            // numRotX
            // 
            this.numRotX.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numRotX.DecimalPlaces = 1;
            this.numRotX.ForeColor = System.Drawing.Color.White;
            this.numRotX.Location = new System.Drawing.Point(51, 40);
            this.numRotX.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numRotX.Maximum = new decimal(new int[] {
            360,
            0,
            0,
            0});
            this.numRotX.Minimum = new decimal(new int[] {
            360,
            0,
            0,
            -2147483648});
            this.numRotX.Name = "numRotX";
            this.numRotX.Size = new System.Drawing.Size(292, 26);
            this.numRotX.TabIndex = 3;
            // 
            // lblRotZ
            // 
            this.lblRotZ.AutoSize = true;
            this.lblRotZ.Location = new System.Drawing.Point(17, 123);
            this.lblRotZ.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblRotZ.Name = "lblRotZ";
            this.lblRotZ.Size = new System.Drawing.Size(23, 20);
            this.lblRotZ.TabIndex = 2;
            this.lblRotZ.Text = "Z:";
            // 
            // lblRotY
            // 
            this.lblRotY.AutoSize = true;
            this.lblRotY.Location = new System.Drawing.Point(17, 83);
            this.lblRotY.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblRotY.Name = "lblRotY";
            this.lblRotY.Size = new System.Drawing.Size(24, 20);
            this.lblRotY.TabIndex = 1;
            this.lblRotY.Text = "Y:";
            // 
            // lblRotX
            // 
            this.lblRotX.AutoSize = true;
            this.lblRotX.Location = new System.Drawing.Point(17, 43);
            this.lblRotX.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblRotX.Name = "lblRotX";
            this.lblRotX.Size = new System.Drawing.Size(24, 20);
            this.lblRotX.TabIndex = 0;
            this.lblRotX.Text = "X:";
            // 
            // groupBoxPosicion
            // 
            this.groupBoxPosicion.Controls.Add(this.numPosZ);
            this.groupBoxPosicion.Controls.Add(this.numPosY);
            this.groupBoxPosicion.Controls.Add(this.numPosX);
            this.groupBoxPosicion.Controls.Add(this.lblPosZ);
            this.groupBoxPosicion.Controls.Add(this.lblPosY);
            this.groupBoxPosicion.Controls.Add(this.lblPosX);
            this.groupBoxPosicion.Dock = System.Windows.Forms.DockStyle.Top;
            this.groupBoxPosicion.ForeColor = System.Drawing.Color.White;
            this.groupBoxPosicion.Location = new System.Drawing.Point(0, 0);
            this.groupBoxPosicion.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxPosicion.Name = "groupBoxPosicion";
            this.groupBoxPosicion.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxPosicion.Size = new System.Drawing.Size(334, 173);
            this.groupBoxPosicion.TabIndex = 0;
            this.groupBoxPosicion.TabStop = false;
            this.groupBoxPosicion.Text = "Posición";
            // 
            // numPosZ
            // 
            this.numPosZ.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numPosZ.DecimalPlaces = 2;
            this.numPosZ.ForeColor = System.Drawing.Color.White;
            this.numPosZ.Location = new System.Drawing.Point(51, 120);
            this.numPosZ.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numPosZ.Minimum = new decimal(new int[] {
            100,
            0,
            0,
            -2147483648});
            this.numPosZ.Name = "numPosZ";
            this.numPosZ.Size = new System.Drawing.Size(292, 26);
            this.numPosZ.TabIndex = 5;
            // 
            // numPosY
            // 
            this.numPosY.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numPosY.DecimalPlaces = 2;
            this.numPosY.ForeColor = System.Drawing.Color.White;
            this.numPosY.Location = new System.Drawing.Point(51, 80);
            this.numPosY.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numPosY.Minimum = new decimal(new int[] {
            100,
            0,
            0,
            -2147483648});
            this.numPosY.Name = "numPosY";
            this.numPosY.Size = new System.Drawing.Size(292, 26);
            this.numPosY.TabIndex = 4;
            // 
            // numPosX
            // 
            this.numPosX.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(45)))), ((int)(((byte)(45)))), ((int)(((byte)(45)))));
            this.numPosX.DecimalPlaces = 2;
            this.numPosX.ForeColor = System.Drawing.Color.White;
            this.numPosX.Location = new System.Drawing.Point(51, 40);
            this.numPosX.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.numPosX.Minimum = new decimal(new int[] {
            100,
            0,
            0,
            -2147483648});
            this.numPosX.Name = "numPosX";
            this.numPosX.Size = new System.Drawing.Size(292, 26);
            this.numPosX.TabIndex = 3;
            // 
            // lblPosZ
            // 
            this.lblPosZ.AutoSize = true;
            this.lblPosZ.Location = new System.Drawing.Point(17, 123);
            this.lblPosZ.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblPosZ.Name = "lblPosZ";
            this.lblPosZ.Size = new System.Drawing.Size(23, 20);
            this.lblPosZ.TabIndex = 2;
            this.lblPosZ.Text = "Z:";
            // 
            // lblPosY
            // 
            this.lblPosY.AutoSize = true;
            this.lblPosY.Location = new System.Drawing.Point(17, 83);
            this.lblPosY.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblPosY.Name = "lblPosY";
            this.lblPosY.Size = new System.Drawing.Size(24, 20);
            this.lblPosY.TabIndex = 1;
            this.lblPosY.Text = "Y:";
            // 
            // lblPosX
            // 
            this.lblPosX.AutoSize = true;
            this.lblPosX.Location = new System.Drawing.Point(17, 43);
            this.lblPosX.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblPosX.Name = "lblPosX";
            this.lblPosX.Size = new System.Drawing.Size(24, 20);
            this.lblPosX.TabIndex = 0;
            this.lblPosX.Text = "X:";
            // 
            // groupBoxVista
            // 
            this.groupBoxVista.Controls.Add(this.btnResetCamara);
            this.groupBoxVista.Controls.Add(this.chkMostrarGrid);
            this.groupBoxVista.Controls.Add(this.chkMostrarEjes);
            this.groupBoxVista.Dock = System.Windows.Forms.DockStyle.Top;
            this.groupBoxVista.ForeColor = System.Drawing.Color.White;
            this.groupBoxVista.Location = new System.Drawing.Point(13, 13);
            this.groupBoxVista.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBoxVista.Name = "groupBoxVista";
            this.groupBoxVista.Padding = new System.Windows.Forms.Padding(13, 13, 13, 13);
            this.groupBoxVista.Size = new System.Drawing.Size(360, 147);
            this.groupBoxVista.TabIndex = 0;
            this.groupBoxVista.TabStop = false;
            this.groupBoxVista.Text = "Vista";
            // 
            // btnResetCamara
            // 
            this.btnResetCamara.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(70)))), ((int)(((byte)(120)))), ((int)(((byte)(180)))));
            this.btnResetCamara.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnResetCamara.Location = new System.Drawing.Point(17, 93);
            this.btnResetCamara.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.btnResetCamara.Name = "btnResetCamara";
            this.btnResetCamara.Size = new System.Drawing.Size(327, 40);
            this.btnResetCamara.TabIndex = 2;
            this.btnResetCamara.Text = "Resetear Cámara";
            this.btnResetCamara.UseVisualStyleBackColor = false;
            // 
            // chkMostrarGrid
            // 
            this.chkMostrarGrid.AutoSize = true;
            this.chkMostrarGrid.Checked = true;
            this.chkMostrarGrid.CheckState = System.Windows.Forms.CheckState.Checked;
            this.chkMostrarGrid.Location = new System.Drawing.Point(17, 60);
            this.chkMostrarGrid.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.chkMostrarGrid.Name = "chkMostrarGrid";
            this.chkMostrarGrid.Size = new System.Drawing.Size(123, 24);
            this.chkMostrarGrid.TabIndex = 1;
            this.chkMostrarGrid.Text = "Mostrar Grid";
            this.chkMostrarGrid.UseVisualStyleBackColor = true;
            // 
            // chkMostrarEjes
            // 
            this.chkMostrarEjes.AutoSize = true;
            this.chkMostrarEjes.Checked = true;
            this.chkMostrarEjes.CheckState = System.Windows.Forms.CheckState.Checked;
            this.chkMostrarEjes.Location = new System.Drawing.Point(17, 29);
            this.chkMostrarEjes.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.chkMostrarEjes.Name = "chkMostrarEjes";
            this.chkMostrarEjes.Size = new System.Drawing.Size(124, 24);
            this.chkMostrarEjes.TabIndex = 0;
            this.chkMostrarEjes.Text = "Mostrar Ejes";
            this.chkMostrarEjes.UseVisualStyleBackColor = true;
            // 
            // panelViewport
            // 
            this.panelViewport.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(50)))), ((int)(((byte)(50)))), ((int)(((byte)(50)))));
            this.panelViewport.Dock = System.Windows.Forms.DockStyle.Fill;
            this.panelViewport.Location = new System.Drawing.Point(321, 67);
            this.panelViewport.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.panelViewport.Name = "panelViewport";
            this.panelViewport.Size = new System.Drawing.Size(1093, 983);
            this.panelViewport.TabIndex = 3;
            // 
            // lblIntensidadLuz
            // 
            this.lblIntensidadLuz.AutoSize = true;
            this.lblIntensidadLuz.Location = new System.Drawing.Point(17, 232);
            this.lblIntensidadLuz.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblIntensidadLuz.Name = "lblIntensidadLuz";
            this.lblIntensidadLuz.Size = new System.Drawing.Size(89, 20);
            this.lblIntensidadLuz.TabIndex = 6;
            this.lblIntensidadLuz.Text = "Iluminación";
            // 
            // trackBarIntensidadLuz
            // 
            this.trackBarIntensidadLuz.Location = new System.Drawing.Point(21, 255);
            this.trackBarIntensidadLuz.Name = "trackBarIntensidadLuz";
            this.trackBarIntensidadLuz.Size = new System.Drawing.Size(297, 69);
            this.trackBarIntensidadLuz.TabIndex = 7;
            // 
            // lblTextura
            // 
            this.lblTextura.AutoSize = true;
            this.lblTextura.Location = new System.Drawing.Point(17, 316);
            this.lblTextura.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.lblTextura.Name = "lblTextura";
            this.lblTextura.Size = new System.Drawing.Size(62, 20);
            this.lblTextura.TabIndex = 8;
            this.lblTextura.Text = "Textura";
            // 
            // cmbTextura
            // 
            this.cmbTextura.FormattingEnabled = true;
            this.cmbTextura.Items.AddRange(new object[] {
            "Cristal",
            "Piedra",
            "Esponja",
            "Oro",
            "Diamante"});
            this.cmbTextura.Location = new System.Drawing.Point(107, 313);
            this.cmbTextura.Name = "cmbTextura";
            this.cmbTextura.Size = new System.Drawing.Size(211, 28);
            this.cmbTextura.TabIndex = 9;
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(9F, 20F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(1800, 1050);
            this.Controls.Add(this.panelViewport);
            this.Controls.Add(this.panelDerecho);
            this.Controls.Add(this.panelIzquierdo);
            this.Controls.Add(this.panelSuperior);
            this.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.MinimumSize = new System.Drawing.Size(1537, 915);
            this.Name = "Form1";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Editor 3D - Proyecto de Computación Gráfica";
            this.panelSuperior.ResumeLayout(false);
            this.panelSuperior.PerformLayout();
            this.panelIzquierdo.ResumeLayout(false);
            this.groupBoxObjetos.ResumeLayout(false);
            this.panelBotonesObj.ResumeLayout(false);
            this.groupBoxAgregar.ResumeLayout(false);
            this.panelDerecho.ResumeLayout(false);
            this.panelPropiedades.ResumeLayout(false);
            this.groupBoxApariencia.ResumeLayout(false);
            this.groupBoxApariencia.PerformLayout();
            this.groupBoxEscala.ResumeLayout(false);
            this.groupBoxEscala.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.numEscZ)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.numEscY)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.numEscX)).EndInit();
            this.groupBoxRotacion.ResumeLayout(false);
            this.groupBoxRotacion.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.numRotZ)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.numRotY)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.numRotX)).EndInit();
            this.groupBoxPosicion.ResumeLayout(false);
            this.groupBoxPosicion.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.numPosZ)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.numPosY)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.numPosX)).EndInit();
            this.groupBoxVista.ResumeLayout(false);
            this.groupBoxVista.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.trackBarIntensidadLuz)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Panel panelSuperior;
        private System.Windows.Forms.Label lblTitulo;
        private System.Windows.Forms.Panel panelIzquierdo;
        private System.Windows.Forms.GroupBox groupBoxAgregar;
        private System.Windows.Forms.Button btnCubo;
        private System.Windows.Forms.Button btnEsfera;
        private System.Windows.Forms.Button btnCilindro;
        private System.Windows.Forms.Button btnCono;
        private System.Windows.Forms.Button btnPiramide;
        private System.Windows.Forms.Button btnToroide;
        private System.Windows.Forms.GroupBox groupBoxObjetos;
        private System.Windows.Forms.ListBox listObjetos;
        private System.Windows.Forms.Panel panelBotonesObj;
        private System.Windows.Forms.Button btnDuplicar;
        private System.Windows.Forms.Button btnEliminar;
        private System.Windows.Forms.Panel panelDerecho;
        private System.Windows.Forms.Panel panelPropiedades;
        private System.Windows.Forms.GroupBox groupBoxPosicion;
        private System.Windows.Forms.NumericUpDown numPosZ;
        private System.Windows.Forms.NumericUpDown numPosY;
        private System.Windows.Forms.NumericUpDown numPosX;
        private System.Windows.Forms.Label lblPosZ;
        private System.Windows.Forms.Label lblPosY;
        private System.Windows.Forms.Label lblPosX;
        private System.Windows.Forms.GroupBox groupBoxRotacion;
        private System.Windows.Forms.NumericUpDown numRotZ;
        private System.Windows.Forms.NumericUpDown numRotY;
        private System.Windows.Forms.NumericUpDown numRotX;
        private System.Windows.Forms.Label lblRotZ;
        private System.Windows.Forms.Label lblRotY;
        private System.Windows.Forms.Label lblRotX;
        private System.Windows.Forms.GroupBox groupBoxEscala;
        private System.Windows.Forms.NumericUpDown numEscZ;
        private System.Windows.Forms.NumericUpDown numEscY;
        private System.Windows.Forms.NumericUpDown numEscX;
        private System.Windows.Forms.Label lblEscZ;
        private System.Windows.Forms.Label lblEscY;
        private System.Windows.Forms.Label lblEscX;
        private System.Windows.Forms.GroupBox groupBoxApariencia;
        private System.Windows.Forms.Button btnColorLinea;
        private System.Windows.Forms.Label lblColorLinea;
        private System.Windows.Forms.Button btnColorRelleno;
        private System.Windows.Forms.Label lblColorRelleno;
        private System.Windows.Forms.CheckBox chkMostrarRelleno;
        private System.Windows.Forms.CheckBox chkVisible;
        private System.Windows.Forms.GroupBox groupBoxVista;
        private System.Windows.Forms.CheckBox chkMostrarEjes;
        private System.Windows.Forms.CheckBox chkMostrarGrid;
        private System.Windows.Forms.Button btnResetCamara;
        private System.Windows.Forms.Panel panelViewport;
        private System.Windows.Forms.Label lblTextura;
        private System.Windows.Forms.TrackBar trackBarIntensidadLuz;
        private System.Windows.Forms.Label lblIntensidadLuz;
        private System.Windows.Forms.ComboBox cmbTextura;
    }
}

