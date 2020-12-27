CREATE TABLE acl_action (
    id   INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , code VARCHAR(100) NOT NULL
  , name VARCHAR(250) NOT NULL
);

INSERT INTO acl_action(code, name) VALUES ('.*', 'All Actions');
INSERT INTO acl_action(code, name) VALUES ('render', 'Render');
INSERT INTO acl_action(code, name) VALUES ('edit', 'Edit');
INSERT INTO acl_action(code, name) VALUES ('view', 'View');
INSERT INTO acl_action(code, name) VALUES ('insert', 'Insert');
INSERT INTO acl_action(code, name) VALUES ('delete', 'Delete');
INSERT INTO acl_action(code, name) VALUES ('execute', 'Execute');

CREATE TABLE acl_scope_group (
    id   INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , name VARCHAR(250) NOT NULL
);

CREATE INDEX idx_acl_scope_group ON acl_scope (group_id);

INSERT INTO acl_scope_group(name) VALUES ('Special');

CREATE TABLE acl_scope (
    id       INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , code     VARCHAR(100) NOT NULL
  , name     VARCHAR(250) NOT NULL
  , group_id INTEGER
);

CREATE INDEX idx_acl_scope_group ON acl_scope (group_id);

INSERT INTO acl_scope(code, name, group_id) VALUES ('.*', 'Any Scope', 1);

CREATE TABLE acl_scope_action (
    id        INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY
  , scope_id  INTEGER NOT NULL
  , action_id INTEGER NOT NULL
);

INSERT INTO acl_scope_action(scope_id, action_id) VALUES (1, 1);

CREATE INDEX idx_acl_scope_action ON acl_scope_action (scope_id, action_id);

CREATE TABLE acl (
    id            INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY
  , user_id       INTEGER 
  , user_group_id INTEGER 
  , scope_id      INTEGER NOT NULL
  , action_id     INTEGER NOT NULL
  , is_allowed    INTEGER NOT NULL DEFAULT 0
  , CONSTRAINT ck_acl_user_or_group CHECK (user_id IS NOT NULL OR user_group_id IS NOT NULL)
);

CREATE INDEX idx_acl_user ON acl (user_id, scope_id, action_id);
CREATE INDEX idx_acl_user_group ON acl (user_group_id, scope_id, action_id);
