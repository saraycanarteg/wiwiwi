# Caterbid Project

Caterbid is a web application designed to facilitate interactions between clients and providers, offering features such as user authentication, dashboards, and management tools. This README provides an overview of the project structure, setup instructions, and usage guidelines.

## Project Structure

The project is organized into several directories and files, each serving a specific purpose:

- **config/**: Contains database connection settings and configuration files.
  - `database.php`: Manages database connections and queries.

- **recursos/**: Holds resources for the application.
  - **css/**: Contains stylesheets for the application.
    - `style.css`: Defines the visual presentation of HTML elements.
  - **js/**: Includes JavaScript files for interactivity.
    - `main.js`: Contains functions for dynamic behavior.
  - **images/**: Directory for storing image files.

- **includes/**: Contains common files used across the application.
  - `header.php`: HTML code for the header section.
  - `navbar.php`: HTML code for the vertical navigation bar.

- **controles/**: Contains server-side control logic.
  - `control.php`: Manages requests and responses.

- **paneles/**: Contains different user panels.
  - **auth/**: Files for user authentication.
    - `login.php`: Login functionality.
    - `register.php`: User registration functionality.
  - **cliente/**: Client panel files.
    - `dashboard.php`: Client dashboard.
    - `cotizacion.php`: Quote management.
    - `historial.php`: Transaction history.
  - **proveedor/**: Provider panel files.
    - `dashboard.php`: Provider dashboard.
    - `productos.php`: Product management.
  - **admin/**: Admin panel files.
    - `dashboard.php`: Complete admin dashboard.

- **complementos/**: Contains additional functionalities.
  - `mail.php`: Functions for sending emails.
  - `pdf.php`: Functions for generating PDF documents.

- `README.md`: Documentation for the project.
- `index.php`: Entry point of the application.

## Setup Instructions

1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Configure the database settings in `config/database.php`.
4. Ensure all dependencies are installed (if applicable).
5. Start the web server and access the application via your browser.

## Features

- User authentication for clients and providers.
- Dynamic dashboards for different user roles.
- Management of quotes and product listings.
- Email notifications and PDF generation.

## Usage Guidelines

- Access the application through `index.php`.
- Use the navigation bar to switch between different sections.
- Follow the prompts for login and registration.

For further assistance, please refer to the individual file documentation or contact the project maintainers.