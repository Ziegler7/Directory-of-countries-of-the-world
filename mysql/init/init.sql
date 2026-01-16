
CREATE DATABASE IF NOT EXISTS countries_db;
USE countries_db;

DROP TABLE IF EXISTS countries;

CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    iso_alpha2 CHAR(2) NOT NULL,
    iso_alpha3 CHAR(3) NOT NULL,
    iso_numeric CHAR(3) NOT NULL,
    population BIGINT NOT NULL,
    square DECIMAL(12,2) NOT NULL,
    UNIQUE(short_name),
    UNIQUE(full_name),
    UNIQUE(iso_alpha2),
    UNIQUE(iso_alpha3),
    UNIQUE(iso_numeric)
);


INSERT INTO countries (short_name, full_name, iso_alpha2, iso_alpha3, iso_numeric, population, square) VALUES
('Russia', 'Russian Federation', 'RU', 'RUS', '643', 146150789, 17125191.00),
('USA', 'United States of America', 'US', 'USA', '840', 331893745, 9833517.00),
('China', 'People''s Republic of China', 'CN', 'CHN', '156', 1411778724, 9596961.00),
('Germany', 'Federal Republic of Germany', 'DE', 'DEU', '276', 83190556, 357022.00),
('France', 'French Republic', 'FR', 'FRA', '250', 67413000, 551695.00);

