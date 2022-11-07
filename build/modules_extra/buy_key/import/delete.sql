DROP TABLE `bk_services`, `bk_services_times`;
ALTER TABLE `servers`
  DROP `bk_host`,
  DROP `bk_user`,
  DROP `bk_pass`,
  DROP `bk_db`,
  DROP `bk_code`;