CREATE TABLE IF NOT EXISTS seguidores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seguidor_id INT NOT NULL,
    seguido_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seguidor_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (seguido_id) REFERENCES usuario(id) ON DELETE CASCADE,
    UNIQUE KEY (seguidor_id, seguido_id)
);
