CREATE TABLE users (
	id          SERIAL PRIMARY KEY,
	login       VARCHAR NOT NULL,
	name        VARCHAR NOT NULL
);

CREATE TABLE topics (
	id          SERIAL PRIMARY KEY,
	date_time   TIMESTAMP NOT NULL,
	author      VARCHAR NOT NULL,
	subject     VARCHAR NOT NULL,
	body        VARCHAR NOT NULL
);

CREATE TABLE messages (
	id          SERIAL PRIMARY KEY,
	date_time   TIMESTAMP NOT NULL,
	author      VARCHAR NOT NULL,
	topic_id    INTEGER NOT NULL REFERENCES topics(id) ON DELETE CASCADE,
	parent_id   INTEGER REFERENCES messages(id) ON DELETE CASCADE,
	body        VARCHAR NOT NULL
);