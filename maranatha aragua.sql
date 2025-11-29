  CREATE DATABASE maranatha_aragua;
  USE maranatha_aragua;


  CREATE TABLE contactos_nuevos (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nombre_apellido VARCHAR(255) NOT NULL,
      edad INT NOT NULL,
      direccion VARCHAR(300) NOT NULL,
      telefono VARCHAR(20) NOT NULL,
      correo VARCHAR(255),
      peticion_oracion VARCHAR(50),
      peticion_texto VARCHAR(50),
      como_enteraste VARCHAR(50),
      como_enteraste_otro VARCHAR(50),
      fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );


  CREATE TABLE miembros_activos (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nombre_apellido VARCHAR(255) NOT NULL,
      edad INT NOT NULL,
      telefono VARCHAR(20) NOT NULL,
      area_servicio VARCHAR(100) NOT NULL,
      direccion VARCHAR(300) NOT NULL,
      fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE donaciones (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nombre_donante VARCHAR(255) NOT NULL,
      id_referencia_pago VARCHAR(100) NOT NULL,
      comentario TEXT,
      fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );


  CREATE TABLE usuarios_admin (
      id INT AUTO_INCREMENT PRIMARY KEY,
      usuario VARCHAR(50) NOT NULL UNIQUE,
      nombre VARCHAR(100) NOT NULL,
      contrasena VARCHAR(255) NOT NULL,
      rol ENUM('superadmin', 'visualizador') NOT NULL DEFAULT 'visualizador',
      fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );


  INSERT INTO usuarios_admin (usuario, nombre, contrasena, rol) VALUES
  ('admin', 'Administrador Principal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

  INSERT INTO usuarios_admin (usuario, nombre, contrasena, rol) VALUES
  ('datos', 'Visualizador de Datos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visualizador');

    -- Tabla para intentos de inicio de sesión fallidos
  CREATE TABLE IF NOT EXISTS admin_login_attempts (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(100) NOT NULL,
      attempts INT NOT NULL DEFAULT 0,
      last_attempt DATETIME DEFAULT NULL,
      locked_until DATETIME DEFAULT NULL,
      ip VARCHAR(45) DEFAULT NULL,
      UNIQUE KEY (username)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabla para códigos de recuperación de contraseña
  CREATE TABLE IF NOT EXISTS admin_recovery_codes (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(100) NOT NULL,
      code_hash VARCHAR(255) NOT NULL,
      expires_at DATETIME NOT NULL,
      used TINYINT(1) NOT NULL DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabla para auditoría de acciones administrativas
  CREATE TABLE IF NOT EXISTS admin_audit_logs (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT DEFAULT NULL,
      username VARCHAR(100) DEFAULT NULL,
      event_type VARCHAR(100) NOT NULL,
      event_detail TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabla para preguntas de seguridad + PIN (recuperación)
    CREATE TABLE IF NOT EXISTS admin_security (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      question1 VARCHAR(255) DEFAULT NULL,
      answer1_hash VARCHAR(255) DEFAULT NULL,
      question2 VARCHAR(255) DEFAULT NULL,
      answer2_hash VARCHAR(255) DEFAULT NULL,
      pin_hash VARCHAR(255) DEFAULT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;