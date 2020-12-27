CREATE TABLE generic_entity_comment (
    id          INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
  , name        VARCHAR(250)
  , body        TEXT
  , UNIQUE INDEX idx_generic_entity_comment_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE generic_entity_comment_state (
    id          INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
  , user_id     INTEGER NOT NULL
  , comment_id  INTEGER NOT NULL
  , is_visible  INTEGER NOT NULL DEFAULT 0
  , CONSTRAINT fk_generic_entity_comment_state_comment FOREIGN KEY (comment_id) REFERENCES generic_entity_comment (id)
  , UNIQUE INDEX idx_generic_entity_comment_state (user_id, comment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
