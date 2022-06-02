CREATE DATABASE IF NOT EXISTS `voting_system`;

USE voting_system;

CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(300) UNIQUE NOT NULL,
    username VARCHAR(300) UNIQUE NOT NULL,
    `password` VARCHAR(400) NOT NULL,
    gender ENUM('male', 'female', 'other')
);

CREATE TABLE polls (
	id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(300),
    `description` VARCHAR(3000),
    owner_id INT REFERENCES users (id) ON DELETE SET NULL,
    end_date DATE NULL,
    results_visibility ENUM('public', 'private') DEFAULT 'public'
);

CREATE TABLE candidates (
	id INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(300) NOT NULL,
    poll_id INT REFERENCES poll (id) ON DELETE CASCADE
);

CREATE TABLE votes (
	id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT REFERENCES polls (id) ON DELETE CASCADE,
    candidate_id INT REFERENCES cadidates (id) ON DELETE CASCADE,
    user_id INT REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE KEY `unique_vote` (candidate_id, user_id)
);
