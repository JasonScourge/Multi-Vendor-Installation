REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(7, 'Подарочный сертификат', 'gift_certificates.add', '', 'ru');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(8, 'Выбираем праздничный подарок', 'gift_certificates.add', '', 'ru');

REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(16, 'Финальная распродажа', 'index.php?dispatch=products.final_sale', '', 'ru');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(17, 'X-Box', 'index.php?dispatch=products.view&product_id=248', '', 'ru');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(18, 'Бонусные баллы', 'index.php?dispatch=pages.view&page_id=23', '', 'ru');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(19, 'Подарочные сертификаты', 'index.php?dispatch=pages.view&page_id=19', '', 'ru');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(6, 'Бесплатная доставка', 'index.php?dispatch=pages.view&page_id=22', '', 'ru');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(9, 'Скидка при выборе пункта самовывоза', 'index.php?dispatch=pages.view&page_id=20', '', 'ru');
UPDATE ?:banners SET position=0 WHERE banner_id=6;
UPDATE ?:banners SET position=10 WHERE banner_id=8;
UPDATE ?:banners SET position=20 WHERE banner_id=9;
UPDATE ?:banners SET position=30 WHERE banner_id=16;
UPDATE ?:banners SET position=40 WHERE banner_id=18;

UPDATE ?:banner_descriptions SET banner='Welcome to Multi-Vendor demo marketplace', url='pages.view&page_id=24', description='' WHERE banner_id=6;
UPDATE ?:banner_descriptions SET banner='Acme Corporation: Join our loyalty program to get special prices', url='pages.view&page_id=25', description='' WHERE banner_id=8;
UPDATE ?:banner_descriptions SET banner='Stark Industries: buy two for the price of one', url='pages.view&page_id=26', description='' WHERE banner_id=9;
UPDATE ?:banner_descriptions SET banner='Become our vendor with no transaction fee', url='pages.view&page_id=27', description='' WHERE banner_id=16;
UPDATE ?:banner_descriptions SET banner='No transaction fee', url='companies.apply_for_vendor&plan_id=2', description='' WHERE banner_id=18;

UPDATE ?:images_links SET image_id=8270 WHERE object_type='promo' AND object_id IN (SELECT banner_image_id FROM ?:banner_images WHERE banner_id=6);
UPDATE ?:images_links SET image_id=8277 WHERE object_type='promo' AND object_id IN (SELECT banner_image_id FROM ?:banner_images WHERE banner_id=8);
UPDATE ?:images_links SET image_id=8278 WHERE object_type='promo' AND object_id IN (SELECT banner_image_id FROM ?:banner_images WHERE banner_id=9);
UPDATE ?:images_links SET image_id=8279 WHERE object_type='promo' AND object_id IN (SELECT banner_image_id FROM ?:banner_images WHERE banner_id=16);
UPDATE ?:images_links SET image_id=8290 WHERE object_type='promo' AND object_id IN (SELECT banner_image_id FROM ?:banner_images WHERE banner_id=18);