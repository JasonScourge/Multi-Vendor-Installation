REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('7', '0', '7', 'A', 'B', '0', '1415336000', '0');
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('8', '7', '7/8', 'A', 'B', '0', '1415316000', '0');
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('9', '7', '7/9', 'A', 'B', '0', '1415526000', '0');
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('10', '7', '7/10', 'A', 'B', '0', '1415736000', '0');

REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (7, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (8, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (9, 1);
REPLACE INTO ?:blog_authors (page_id, user_id) VALUES (10, 1);

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`)
VALUES
  (1074, '1.png', 894, 305),
  (1073, '2.png', 894, 305),
  (1072, '3.png', 894, 305);


REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`)
VALUES
  (953, 8, 'blog', 1074, 0, 'M', 0),
  (952, 9, 'blog', 1073, 0, 'M', 0),
  (951, 10, 'blog', 1072, 0, 'M', 0);DELETE FROM ?:page_descriptions WHERE page_id IN (SELECT page_id FROM ?:pages WHERE page_type = 'B') AND page_id NOT IN (33, 34, 35, 36, 37);
DELETE FROM ?:pages WHERE page_type = 'B' AND page_id NOT IN (33, 34, 35, 36, 37);
DELETE FROM ?:blog_authors WHERE page_id NOT IN (33, 34, 35, 36, 37);

REPLACE INTO ?:pages (`page_id`, `company_id`, `parent_id`, `id_path`, `status`, `page_type`, `position`, `timestamp`, `usergroup_ids`, `localization`, `new_window`, `use_avail_period`, `avail_from_timestamp`, `avail_till_timestamp`) VALUES (33,0,0,'33','A','B',0,1485291600,'0','',0,'N',0,0);
REPLACE INTO ?:pages (`page_id`, `company_id`, `parent_id`, `id_path`, `status`, `page_type`, `position`, `timestamp`, `usergroup_ids`, `localization`, `new_window`, `use_avail_period`, `avail_from_timestamp`, `avail_till_timestamp`) VALUES (34,0,33,'33/34','A','B',0,1485291600,'0','',0,'N',0,0);
REPLACE INTO ?:pages (`page_id`, `company_id`, `parent_id`, `id_path`, `status`, `page_type`, `position`, `timestamp`, `usergroup_ids`, `localization`, `new_window`, `use_avail_period`, `avail_from_timestamp`, `avail_till_timestamp`) VALUES (35,0,33,'33/35','A','B',0,1485291600,'0','',0,'N',0,0);
REPLACE INTO ?:pages (`page_id`, `company_id`, `parent_id`, `id_path`, `status`, `page_type`, `position`, `timestamp`, `usergroup_ids`, `localization`, `new_window`, `use_avail_period`, `avail_from_timestamp`, `avail_till_timestamp`) VALUES (36,0,33,'33/36','A','B',0,1485291600,'0','',0,'N',0,0);
REPLACE INTO ?:pages (`page_id`, `company_id`, `parent_id`, `id_path`, `status`, `page_type`, `position`, `timestamp`, `usergroup_ids`, `localization`, `new_window`, `use_avail_period`, `avail_from_timestamp`, `avail_till_timestamp`) VALUES (37,0,33,'33/37','A','B',0,1485291600,'0','',0,'N',0,0);

REPLACE INTO ?:blog_authors (`page_id`, `user_id`) VALUES (33,1);
REPLACE INTO ?:blog_authors (`page_id`, `user_id`) VALUES (34,1);
REPLACE INTO ?:blog_authors (`page_id`, `user_id`) VALUES (35,1);
REPLACE INTO ?:blog_authors (`page_id`, `user_id`) VALUES (36,1);
REPLACE INTO ?:blog_authors (`page_id`, `user_id`) VALUES (37,1);


