DROP TABLE messages;
DROP TABLE topics;
DROP TABLE users;

CREATE TABLE users (
	id                        SERIAL PRIMARY KEY,
	login                     VARCHAR NOT NULL UNIQUE,
	name                      VARCHAR NOT NULL,
	auth_level                VARCHAR NOT NULL DEFAULT 'User' CHECK(auth_level IN ('User', 'Moderator', 'Admin')),
	from_github               BOOLEAN NOT NULL DEFAULT FALSE,
	gravatar_id               VARCHAR NOT NULL DEFAULT '',
	email                     VARCHAR UNIQUE,
	subscribe_to_all_topics   BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE topics (
	id                        SERIAL PRIMARY KEY,
	date_time                 TIMESTAMP NOT NULL,
	author                    INTEGER NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
	subject                   VARCHAR NOT NULL,
	body                      VARCHAR NOT NULL,
	format                    VARCHAR NOT NULL DEFAULT 'Wiki' CHECK(format IN ('Wiki', 'Markdown'))
);

CREATE TABLE messages (
	id                        SERIAL PRIMARY KEY,
	date_time                 TIMESTAMP NOT NULL,
	author                    INTEGER NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
	topic_id                  INTEGER NOT NULL REFERENCES topics(id) ON DELETE CASCADE,
	parent_id                 INTEGER REFERENCES messages(id) ON DELETE CASCADE,
	body                      VARCHAR NOT NULL,
	format                    VARCHAR NOT NULL DEFAULT 'Wiki' CHECK(format IN ('Wiki', 'Markdown'))
);

CREATE TABLE subscriptions (
	user_id                   INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	topic_id                  INTEGER NOT NULL REFERENCES topics(id) ON DELETE CASCADE,
	PRIMARY KEY(user_id, topic_id)
);

CREATE TABLE queued_emails (
	id                        SERIAL PRIMARY KEY,
	"to"                      VARCHAR NOT NULL,
	to_name                   VARCHAR NOT NULL,
	subject                   VARCHAR NOT NULL,
	body                      VARCHAR NOT NULL
);

CREATE TABLE bounced_emails (
	id                        SERIAL PRIMARY KEY,
	user_id                   INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	sent                      TIMESTAMP NOT NULL,
	message_id                VARCHAR NOT NULL UNIQUE,
	"from"                    VARCHAR NOT NULL,
	"to"                      VARCHAR NOT NULL,
	subject                   VARCHAR NOT NULL,
	body                      TEXT NOT NULL,
	bounce_message            TEXT NOT NULL,
	bounce_source             TEXT NOT NULL
);