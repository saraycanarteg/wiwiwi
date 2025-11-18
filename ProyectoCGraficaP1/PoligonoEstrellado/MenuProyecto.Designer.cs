namespace PoligonoEstrellado
{
    partial class MenuProyecto
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.menuStrip1 = new System.Windows.Forms.MenuStrip();
            this.figurasToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.y8PuntasToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.puntasYPentagonosToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.puntasYGema10LadosToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.figurasCompuestasToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.puntasToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.pentagonoToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.floresToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.menuStrip1.SuspendLayout();
            this.SuspendLayout();
            // 
            // menuStrip1
            // 
            this.menuStrip1.ImageScalingSize = new System.Drawing.Size(24, 24);
            this.menuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.figurasToolStripMenuItem,
            this.figurasCompuestasToolStripMenuItem});
            this.menuStrip1.Location = new System.Drawing.Point(0, 0);
            this.menuStrip1.Name = "menuStrip1";
            this.menuStrip1.Padding = new System.Windows.Forms.Padding(5, 1, 0, 1);
            this.menuStrip1.Size = new System.Drawing.Size(1105, 26);
            this.menuStrip1.TabIndex = 0;
            this.menuStrip1.Text = "menuStrip1";
            // 
            // figurasToolStripMenuItem
            // 
            this.figurasToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.y8PuntasToolStripMenuItem,
            this.puntasYPentagonosToolStripMenuItem,
            this.puntasYGema10LadosToolStripMenuItem});
            this.figurasToolStripMenuItem.Name = "figurasToolStripMenuItem";
            this.figurasToolStripMenuItem.Size = new System.Drawing.Size(164, 24);
            this.figurasToolStripMenuItem.Text = "Poligonos Estrellados";
            // 
            // y8PuntasToolStripMenuItem
            // 
            this.y8PuntasToolStripMenuItem.Name = "y8PuntasToolStripMenuItem";
            this.y8PuntasToolStripMenuItem.Size = new System.Drawing.Size(240, 26);
            this.y8PuntasToolStripMenuItem.Text = "16 y 8 puntas";
            this.y8PuntasToolStripMenuItem.Click += new System.EventHandler(this.y8PuntasToolStripMenuItem_Click);
            // 
            // puntasYPentagonosToolStripMenuItem
            // 
            this.puntasYPentagonosToolStripMenuItem.Name = "puntasYPentagonosToolStripMenuItem";
            this.puntasYPentagonosToolStripMenuItem.Size = new System.Drawing.Size(240, 26);
            this.puntasYPentagonosToolStripMenuItem.Text = "5 puntas y Pentagonos";
            this.puntasYPentagonosToolStripMenuItem.Click += new System.EventHandler(this.puntasYPentagonosToolStripMenuItem_Click);
            // 
            // puntasYGema10LadosToolStripMenuItem
            // 
            this.puntasYGema10LadosToolStripMenuItem.Name = "puntasYGema10LadosToolStripMenuItem";
            this.puntasYGema10LadosToolStripMenuItem.Size = new System.Drawing.Size(240, 26);
            this.puntasYGema10LadosToolStripMenuItem.Text = "Gema de 10 lados";
            this.puntasYGema10LadosToolStripMenuItem.Click += new System.EventHandler(this.puntasYGema10LadosToolStripMenuItem_Click);
            // 
            // figurasCompuestasToolStripMenuItem
            // 
            this.figurasCompuestasToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.puntasToolStripMenuItem,
            this.pentagonoToolStripMenuItem,
            this.floresToolStripMenuItem});
            this.figurasCompuestasToolStripMenuItem.Name = "figurasCompuestasToolStripMenuItem";
            this.figurasCompuestasToolStripMenuItem.Size = new System.Drawing.Size(70, 24);
            this.figurasCompuestasToolStripMenuItem.Text = "Figuras";
            // 
            // puntasToolStripMenuItem
            // 
            this.puntasToolStripMenuItem.Name = "puntasToolStripMenuItem";
            this.puntasToolStripMenuItem.Size = new System.Drawing.Size(224, 26);
            this.puntasToolStripMenuItem.Text = "Estrella 8 puntas";
            this.puntasToolStripMenuItem.Click += new System.EventHandler(this.puntasToolStripMenuItem_Click);
            // 
            // pentagonoToolStripMenuItem
            // 
            this.pentagonoToolStripMenuItem.Name = "pentagonoToolStripMenuItem";
            this.pentagonoToolStripMenuItem.Size = new System.Drawing.Size(224, 26);
            this.pentagonoToolStripMenuItem.Text = "Hexagono Ciclico";
            this.pentagonoToolStripMenuItem.Click += new System.EventHandler(this.pentagonoToolStripMenuItem_Click);
            // 
            // floresToolStripMenuItem
            // 
            this.floresToolStripMenuItem.Name = "floresToolStripMenuItem";
            this.floresToolStripMenuItem.Size = new System.Drawing.Size(224, 26);
            this.floresToolStripMenuItem.Text = "Símbolo Afrodita";
            this.floresToolStripMenuItem.Click += new System.EventHandler(this.floresToolStripMenuItem_Click);
            // 
            // MenuProyecto
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(8F, 16F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.AutoSize = true;
            this.ClientSize = new System.Drawing.Size(1105, 629);
            this.Controls.Add(this.menuStrip1);
            this.IsMdiContainer = true;
            this.MainMenuStrip = this.menuStrip1;
            this.Margin = new System.Windows.Forms.Padding(3, 2, 3, 2);
            this.Name = "MenuProyecto";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Menu Proyecto";
            this.menuStrip1.ResumeLayout(false);
            this.menuStrip1.PerformLayout();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.MenuStrip menuStrip1;
        private System.Windows.Forms.ToolStripMenuItem figurasToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem figurasCompuestasToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem y8PuntasToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem puntasYPentagonosToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem puntasYGema10LadosToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem puntasToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem pentagonoToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem floresToolStripMenuItem;
    }
}