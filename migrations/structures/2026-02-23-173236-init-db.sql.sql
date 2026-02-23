CREATE TABLE IF NOT EXISTS movie (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    perex TEXT NOT NULL,
    year INT NOT NULL,
    description TEXT NOT NULL,
    rating FLOAT NOT NULL,
    price FLOAT NOT NULL
);

CREATE TABLE IF NOT EXISTS files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    filepath VARCHAR(1024) NOT NULL
    );

CREATE TABLE IF NOT EXISTS movies_files (
    movie_id INT NOT NULL,
    file_id INT NOT NULL,
    PRIMARY KEY (movie_id, file_id),
    CONSTRAINT fk_movies_files_movie
    FOREIGN KEY (movie_id) REFERENCES movie(movie_id)
    ON DELETE CASCADE,
    CONSTRAINT fk_movies_files_file
    FOREIGN KEY (file_id) REFERENCES files(file_id)
    ON DELETE CASCADE
    );

