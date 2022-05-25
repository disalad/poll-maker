CREATE DATABASE IF NOT EXISTS `voting_system`;

CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(300) UNIQUE NOT NULL,
    username VARCHAR(300) UNIQUE NOT NULL,
    nic_number VARCHAR(300) UNIQUE NOT NULL,
    `password` VARCHAR(400) NOT NULL,
    profile_photo VARCHAR(300),
    website VARCHAR(300) NULL,
    twitter_username VARCHAR(300) NULL,
    `role` VARCHAR(20) DEFAULT 'voter',
    profile_last_updated TIMESTAMP DEFAULT NULL,
    approved BOOLEAN DEFAULT FALSE NOT NULL
);

CREATE TABLE polls (
	id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(300),
    `description` VARCHAR(3000),
    owner_id INT REFERENCES users (id) ON DELETE SET NULL,
    approved BOOLEAN DEFAULT FALSE NOT NULL
);

CREATE TABLE candidates (
	id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(300) NULL,
    display_name VARCHAR(300) NOT NULL,
    profile_photo VARCHAR(300),
    user_id INT REFERENCES users (id) ON DELETE CASCADE,
    poll_id INT REFERENCES poll (id) ON DELETE CASCADE
);

CREATE TABLE votes (
	id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT REFERENCES polls (id) ON DELETE CASCADE,
    candidate_id INT REFERENCES cadidates (id) ON DELETE CASCADE,
    user_id INT REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE KEY `unique_vote` (candidate_id, user_id)
);