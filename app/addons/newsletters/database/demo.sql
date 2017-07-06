REPLACE INTO `?:newsletters` (`newsletter_id`, `sent_date`, `status`, `type`, `mailing_lists`, `abandoned_type`, `abandoned_days`) VALUES ('1','0','A','N','', 'both', 0);
REPLACE INTO `?:newsletters` (`newsletter_id`, `sent_date`, `status`, `type`, `mailing_lists`, `abandoned_type`, `abandoned_days`) VALUES ('2','0','A','T','', 'both', 0);

REPLACE INTO ?:mailing_lists (`list_id`, `timestamp`, `from_email`, `from_name`, `reply_to`, `show_on_checkout`, `show_on_registration`, `status`) VALUES ('1', '0', 'no-reply@example.com', 'Acme', 'no-reply@example.com', '1', '1', 'A');DELETE FROM ?:newsletters WHERE newsletter_id <> 1;
DELETE FROM ?:newsletter_descriptions WHERE newsletter_id <> 1;

REPLACE INTO ?:newsletters (`newsletter_id`, `campaign_id`, `sent_date`, `status`, `type`, `mailing_lists`, `users`, `abandoned_type`, `abandoned_days`) VALUES (1,0,0,'A','N','','17,18,19,20,21,22,23,24,25,26','cart',2);