CREATE TABLE generic_audit_info (
    id            INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , table_name    VARCHAR(100) NOT NULL
  , object_id     INTEGER      NOT NULL
  , created_by    INTEGER      
  , created_at    DATETIME     
  , created_from  VARCHAR(15)  
  , modified_by   INTEGER
  , modified_at   DATETIME
  , modified_from VARCHAR(15)
  , deleted_by    INTEGER
  , deleted_at    DATETIME
  , deleted_from  VARCHAR(15)
  , CONSTRAINT fk_audit_info_created_by FOREIGN KEY (created_by) REFERENCES acn_user (id)
  , INDEX idx_audit_info_object_table (object_id, table_name)
  , INDEX idx_audit_info_created_at (created_at)
  , INDEX idx_audit_info_created_from (created_from)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE generic_audit (
    id          INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , action_date DATETIME     NOT NULL
  , table_name  VARCHAR(100) NOT NULL
  , action_name CHAR(1)      NOT NULL
  , object_id   INTEGER      NOT NULL
  , author_id   INTEGER      
  , ip_address  VARCHAR(15)
  , INDEX idx_audit_date (action_date)
  , INDEX idx_audit_table (table_name)
  , INDEX idx_audit_action (action_name)
  , INDEX idx_audit_object (object_id)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE generic_audit_data (
    id          INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , audit_id    INTEGER      NOT NULL
  , field_name  VARCHAR(100) NOT NULL
  , old_value   TEXT 
  , new_value   TEXT 
  , CONSTRAINT fk_audit_data_audit FOREIGN KEY (audit_id) REFERENCES audit (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
