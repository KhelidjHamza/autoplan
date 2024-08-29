CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    type ENUM('nutritionist', 'fitness-coach') NOT NULL
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    age INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    height DECIMAL(5,2) NOT NULL,
    calorie_goal INT NOT NULL,
    number_of_meals INT NOT NULL,
    goal ENUM('lose-weight', 'gain-weight', 'maintain-weight') NOT NULL,
    exercise ENUM('everyday', '2-times-week', 'not-everyday', 'none') NOT NULL,
    diseases VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE meals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    calories INT NOT NULL,
    fat DECIMAL(5,2) NOT NULL,
    carbs DECIMAL(5,2) NOT NULL,
    protein DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);
