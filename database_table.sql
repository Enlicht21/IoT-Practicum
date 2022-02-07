CREATE TABLE outputs (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64),
    gpio INT(6),
    state INT(6)
);
CREATE TABLE output_esp (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    out_gpio INT(6),
    output_logic INT(6)
);
