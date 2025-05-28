<<<<<<< HEAD
-- Crear tabla USUARIOS
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY,
    primer_nombre VARCHAR(50) NOT NULL,
    segundo_nombre VARCHAR(50),
    primer_apellido VARCHAR(50) NOT NULL,
    segundo_apellido VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('administrador','editor','lector') NOT NULL
);

-- Crear tabla PLANES
CREATE TABLE planes (
    id_plan INT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    objetivo_general TEXT NOT NULL,
    año INT NOT NULL,
    estado ENUM('borrador','en_revision','aprobado') NOT NULL,
    presupuesto DECIMAL(12,2),
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Crear tabla ACTIVIDADES
CREATE TABLE actividades (
    id INT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    estado ENUM('pendiente','en_progreso','completada') NOT NULL,
    id_plan INT NOT NULL,
    FOREIGN KEY (id_plan) REFERENCES planes(id_plan)
);
=======
-- Crear tabla USUARIOS
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY,
    primer_nombre VARCHAR(50) NOT NULL,
    segundo_nombre VARCHAR(50),
    primer_apellido VARCHAR(50) NOT NULL,
    segundo_apellido VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('administrador','editor','lector') NOT NULL
);

-- Crear tabla PLANES
CREATE TABLE planes (
    id_plan INT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    objetivo_general TEXT NOT NULL,
    año INT NOT NULL,
    estado ENUM('borrador','en_revision','aprobado') NOT NULL,
    presupuesto DECIMAL(12,2),
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Crear tabla ACTIVIDADES
CREATE TABLE actividades (
    id INT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    estado ENUM('pendiente','en_progreso','completada') NOT NULL,
    id_plan INT NOT NULL,
    FOREIGN KEY (id_plan) REFERENCES planes(id_plan)
);
>>>>>>> 1bc4df184ba8a2a882bc880481269965a3efa759
