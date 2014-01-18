DROP TABLE IF EXISTS auth_codes;
DROP TABLE IF EXISTS ts_uuids;
CREATE TABLE auth_codes
(
  ID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  mc_name varchar(16) UNIQUE NOT NULL,
  auth_code varchar(10) NOT NULL,
  created_time datetime
);
CREATE TRIGGER create_date_AUTH BEFORE INSERT ON auth_codes
FOR EACH ROW BEGIN
  SET new.created_time = now();
END;
CREATE TABLE sonmica_stats.ts_uuids
(
  ID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ts_uuid varchar(128) UNIQUE NOT NULL,
  auth_code varchar(10) NOT NULL,
  created_time datetime
);
CREATE TRIGGER create_date_UUID BEFORE INSERT ON ts_uuids
FOR EACH ROW BEGIN
  SET new.created_time = now();
END;


