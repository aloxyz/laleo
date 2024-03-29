create table roles(
   role_name VARCHAR(10),
   PRIMARY KEY(role_name)
);

create table accounts(
   account_ID INT AUTO_INCREMENT,
   nickname VARCHAR(15) NOT NULL,
   email VARCHAR(50) NOT NULL,
<<<<<<< HEAD
   password VARCHAR(60) NOT NULL,
=======
   password VARCHAR(50) NOT NULL,
>>>>>>> cb3699642341f983310f5cbe751d7564d5b00a91
   name VARCHAR(20),
   surname VARCHAR(20),
   country VARCHAR(30),
   birthdate DATE,
   registration_date DATE,
   role VARCHAR(10) NOT NULL,
   PRIMARY KEY (account_ID),
   FOREIGN KEY (role) REFERENCES roles(role_name),
   UNIQUE (email),
   UNIQUE (nickname)
);

create table languages(
   language_name VARCHAR(15),
   PRIMARY KEY(language_name)
);

create table stories(
   story_ID INT AUTO_INCREMENT,
   title VARCHAR(50) NOT NULL,
   pubblication_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   total_votes INT NOT NULL DEFAULT 0,
   hidden_flag BOOL NOT NULL DEFAULT FALSE,
   thumbnail_path VARCHAR(30) DEFAULT NULL,
   language VARCHAR(15),
   author_ID INT NOT NULL,
   PRIMARY KEY (story_ID),
   FOREIGN KEY (language) REFERENCES languages(language_name),
   FOREIGN KEY (author_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE
);

create table chapters(
   chapter_ID INT AUTO_INCREMENT,
   title VARCHAR(50) NOT NULL,
   content TEXT,
   pubblication_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   total_votes INT NOT NULL DEFAULT 0,
   hidden_flag BOOL NOT NULL DEFAULT FALSE,
   story_ID INT NOT NULL,
   PRIMARY KEY (chapter_ID),
   FOREIGN KEY (story_ID) REFERENCES stories(story_ID) ON DELETE CASCADE
);

create table thoughts(
   thought_ID INT AUTO_INCREMENT,
   content VARCHAR(4096) NOT NULL,
   pubblication_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   hidden_flag BOOL NOT NULL DEFAULT FALSE,
   chapter_ID INT NOT NULL,
   thought_padre_ID INT,
   author varchar(15),
   PRIMARY KEY (thought_ID),
   FOREIGN KEY (chapter_ID) REFERENCES chapters(chapter_ID) ON DELETE CASCADE,
   FOREIGN KEY (thought_padre_ID) REFERENCES thoughts(thought_ID) ON DELETE CASCADE,   
   FOREIGN KEY (author) REFERENCES accounts(nickname) ON DELETE CASCADE
);


create table genres(
   genre_name VARCHAR(15),
   PRIMARY KEY(genre_name)
);


create table reactions(
   reaction CHAR,
   PRIMARY KEY(reaction)
);


create table accounts_stories(
   account_ID INT,
   story_ID INT,
   PRIMARY KEY(account_ID, story_ID),
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(story_ID) REFERENCES stories(story_ID) ON DELETE CASCADE
);

create table accounts_genres(
   account_ID INT,
   genre_name VARCHAR(15),
   PRIMARY KEY(account_ID, genre_name),
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(genre_name) REFERENCES genres(genre_name) ON DELETE CASCADE
);

create table followers_followeds(
   follower_ID INT,
   followed_ID INT,
   PRIMARY KEY(follower_ID, followed_ID),
   FOREIGN KEY(follower_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(followed_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE
);

create table accounts_languages(
   account_ID INT,
   language_name VARCHAR(15),
   PRIMARY KEY(account_ID, language_name),
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(language_name) REFERENCES languages(language_name) ON DELETE CASCADE
);

create table thoughts_accounts_reactions(
   thought_ID INT,
   account_ID INT,
   reaction CHAR,   
   PRIMARY KEY(thought_ID, account_ID, reaction),
   FOREIGN KEY(thought_ID) REFERENCES thoughts(thought_ID) ON DELETE CASCADE,
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(reaction) REFERENCES reactions(reaction) ON DELETE CASCADE
);

create table chapters_accounts_reactions(
   chapter_ID INT,
   account_ID INT,
   reaction CHAR,   
   PRIMARY KEY(chapter_ID, account_ID, reaction),
   FOREIGN KEY(chapter_ID) REFERENCES chapters(chapter_ID) ON DELETE CASCADE,
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(reaction) REFERENCES reactions(reaction) ON DELETE CASCADE
);

create table moderators_languages(
   moderator_ID INT,
   language_name VARCHAR(15),
   PRIMARY KEY(moderator_ID, language_name),
   FOREIGN KEY(moderator_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(language_name) REFERENCES languages(language_name) ON DELETE CASCADE
);

create table genres_stories(
   genre_name VARCHAR(15),
   story_ID INT,
   PRIMARY KEY(genre_name, story_ID),
   FOREIGN KEY(genre_name) REFERENCES genres(genre_name) ON DELETE CASCADE,
   FOREIGN KEY(story_ID) REFERENCES stories(story_ID) ON DELETE CASCADE
);

create table votes_stories(
   account_ID INT,
   story_ID INT,
   vote BOOL NOT NULL,
   PRIMARY KEY(account_ID, story_ID),
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(story_ID) REFERENCES stories(story_ID) ON DELETE CASCADE
);

create table votes_chapters(
   account_ID INT,
   chapter_ID INT,
   vote BOOL NOT NULL,
   PRIMARY KEY(account_ID, chapter_ID),
   FOREIGN KEY(account_ID) REFERENCES accounts(account_ID) ON DELETE CASCADE,
   FOREIGN KEY(chapter_ID) REFERENCES chapters(chapter_ID) ON DELETE CASCADE
);


INSERT INTO `roles` (`role_name`) VALUES ('user');
INSERT INTO `roles` (`role_name`) VALUES ('moderator');
INSERT INTO `roles` (`role_name`) VALUES ('admin');

INSERT INTO `languages` (`language_name`) VALUES ('italian');
INSERT INTO `languages` (`language_name`) VALUES ('english');

INSERT INTO `genres` (`genre_name`) VALUES ('Horror');
INSERT INTO `genres` (`genre_name`) VALUES ('Fantasy');