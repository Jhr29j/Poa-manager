CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    primer_nombre VARCHAR(50) NOT NULL,
    segundo_nombre VARCHAR(50) DEFAULT NULL,
    primer_apellido VARCHAR(50) NOT NULL,
    segundo_apellido VARCHAR(50) DEFAULT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'editor', 'lector') DEFAULT 'lector',
    telefono VARCHAR(20) DEFAULT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    genero ENUM('masculino', 'femenino', 'otro') DEFAULT NULL,
    activo BOOLEAN DEFAULT TRUE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255),
    descripcion TEXT,
    objetivo_general TEXT,
    año YEAR,
    estado ENUM('borrador', 'en_revision', 'aprobado') DEFAULT 'borrador',
    presupuesto DECIMAL(12,2),
    responsable_id INT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
);

INSERT INTO usuarios (
    primer_nombre,
    segundo_nombre,
    primer_apellido,
    segundo_apellido,
    email,
    contraseña,
    rol,
    telefono,
    fecha_nacimiento,
    genero
) VALUES (
    'Joelvis',
    NULL,
    'Henríquez',
    'Ramírez',
    'juniorjoelvis@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hash de "juniorjoelvishenriquez29|||"
    'administrador',
    '8094526104',
    '2009-09-29',
    'masculino'
);

CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    responsable_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    presupuesto_asignado DECIMAL(12,2) NOT NULL,
    estado ENUM('pendiente', 'en_progreso', 'completada') DEFAULT 'pendiente',
    FOREIGN KEY (plan_id) REFERENCES planes(id),
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
);


INSERT INTO usuarios (
    primer_nombre,
    email,
    contraseña,
    rol
) VALUES (
    'Test',
    'test@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hash de "password"
    'administrador'
);