CREATE TABLE auth_codes
(
  ID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  mc_name varchar(16) UNIQUE NOT NULL,
  auth_code varchar(10) NOT NULL,
  created_time datetime
);
CREATE TRIGGER create_date BEFORE INSERT ON auth_codes
FOR EACH ROW BEGIN
  SET new.created_time = now();
END;
CREATE TRIGGER create_date_update BEFORE UPDATE ON auth_codes
FOR EACH ROW BEGIN
  SET new.created_time = now();
END;




