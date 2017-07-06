REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(7, 'Gift certificate', 'gift_certificates.add', '', 'en');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(8, 'Holiday gift guide', 'gift_certificates.add', '', 'en');

REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(16, 7, 'en');
REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(18, 8, 'en');

REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(16, 'Final sale', 'index.php?dispatch=products.final_sale', '', 'en');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(17, 'X-Box', 'index.php?dispatch=products.view&product_id=248', '', 'en');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(18, 'Bonus points', 'index.php?dispatch=pages.view&page_id=23', '', 'en');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(19, 'Gift certificates', 'index.php?dispatch=pages.view&page_id=19', '', 'en');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(6, 'Free shipping', 'index.php?dispatch=pages.view&page_id=22', '', 'en');
REPLACE INTO ?:banner_descriptions (`banner_id`, `banner`, `url`, `description`, `lang_code`) VALUES(9, 'Discount if select pickup', 'index.php?dispatch=pages.view&page_id=20', '', 'en');

REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(35, 16, 'en');
REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(36, 17, 'en');
REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(37, 18, 'en');
REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(38, 19, 'en');
REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(39, 6, 'en');
REPLACE INTO ?:banner_images (`banner_image_id`, `banner_id`, `lang_code`) VALUES(40, 9, 'en');
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