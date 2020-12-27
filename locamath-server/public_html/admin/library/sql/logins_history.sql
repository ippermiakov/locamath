CREATE TABLE generic_logins_history (
    id                             INTEGER      NOT NULL AUTO_INCREMENT PRIMARY KEY
  , date_time                      DATETIME     NOT NULL
  , login                          VARCHAR(50)  NOT NULL
  , ip_address                     VARCHAR(50)  NOT NULL
  , failed                         INTEGER      NOT NULL DEFAULT 0
  , error                          TEXT
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
