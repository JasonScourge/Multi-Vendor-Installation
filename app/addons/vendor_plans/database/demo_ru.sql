REPLACE INTO `?:vendor_plan_descriptions` (`plan_id`, `lang_code`, `plan`, `description`)
VALUES
    (1, 'ru', 'Голд', ''),
    (2, 'ru', 'Премиум', ''),
    (3, 'ru', 'Безлимит', ''),
    (4, 'ru', 'Эксклюзив', ''),
    (9, 'ru', 'Бесплатный', '');
DELETE FROM ?:vendor_plan_descriptions WHERE `plan_id` > 5;
REPLACE INTO ?:vendor_plan_descriptions (`plan_id`, `lang_code`, `plan`, `description`) VALUES (1,'ru','Silver','');
REPLACE INTO ?:vendor_plan_descriptions (`plan_id`, `lang_code`, `plan`, `description`) VALUES (2,'ru','Bronze','');
REPLACE INTO ?:vendor_plan_descriptions (`plan_id`, `lang_code`, `plan`, `description`) VALUES (3,'ru','Gold','');
REPLACE INTO ?:vendor_plan_descriptions (`plan_id`, `lang_code`, `plan`, `description`) VALUES (4,'ru','Platinum','');
REPLACE INTO ?:vendor_plan_descriptions (`plan_id`, `lang_code`, `plan`, `description`) VALUES (5,'ru','Exclusive','');