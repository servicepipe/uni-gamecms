DROP TABLE `sk_services`;
ALTER TABLE `servers`
  DROP `sk_host`,
  DROP `sk_user`,
  DROP `sk_pass`,
  DROP `sk_db`,
  DROP `sk_code`;