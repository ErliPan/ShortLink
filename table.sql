# SHORTLINK DB

create table Shortener (
	shortID INT UNSIGNED auto_increment PRIMARY KEY,
	short VARCHAR(3) NOT NULL,
	full VARCHAR(2048) NOT NULL,
	expire INT UNSIGNED NOT NULL,
	tracking CHAR(40) NOT NULL
);
create table Clicks (
	clickID INT UNSIGNED auto_increment PRIMARY KEY,
	shortID INT UNSIGNED NOT NULL,
	clientAddr VARCHAR(45) NOT NULL,
	clickTime DATETIME NOT NULL,
	clientLang VARCHAR(256) NOT NULL,
	userAgent VARCHAR(256) NOT NULL,
	FOREIGN KEY (shortID) REFERENCES Shortener(shortID)
);


# SHORTLINK DB SQLite

create table Shortener (
	shortID INTEGER PRIMARY KEY,
	short VARCHAR(3) NOT NULL,
	full VARCHAR(2048) NOT NULL,
	expire INT UNSIGNED NOT NULL,
	tracking CHAR(40) NOT NULL
);
create table Clicks (
	clickID INTEGER PRIMARY KEY,
	shortID INT UNSIGNED NOT NULL,
	clientAddr VARCHAR(45) NOT NULL,
	clickTime DATETIME NOT NULL,
	clientLang VARCHAR(256) NOT NULL,
	userAgent VARCHAR(256) NOT NULL,
	FOREIGN KEY (shortID) REFERENCES Shortener(shortID)
);