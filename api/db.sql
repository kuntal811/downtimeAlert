
/*
CREATE  TABLE IF NOT EXISTS `users` (
  `user_id` INT  AUTO_INCREMENT ,
  `name` VARCHAR(150) NOT NULL ,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `hash` VARCHAR(100),
  `hash_created_at` TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
);

CREATE TABLE monitors (
  monitor_id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  title varchar(60) NOT NULL,
  protocol varchar(10) NOT NULL,
  check_interval int NOT NULL,
  url varchar(60) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (monitor_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
)

CREATE TABLE checks (
  check_id int NOT NULL AUTO_INCREMENT,
  monitor_id int NOT NULL,
  status boolean NOT NULL,
  response_time int NOT NULL,
  response_code int NOT NULL,
  checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (check_id),
  FOREIGN KEY (monitor_id) REFERENCES monitors (monitor_id) ON DELETE CASCADE
)

CREATE TABLE last_checks (
  monitor_id int NOT NULL,
  checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (monitor_id) REFERENCES monitors (monitor_id) ON DELETE CASCADE
)

delete from monitors WHERE user_id=7;
delete from checks;
delete from last_checks;

update monitors set check_interval=60 where monitor_id=8;

select * from monitors;
select * from checks;
select * from last_checks;

select * from checks where checked_at;

*/
/*
SELECT last_checks.checked_at,monitors.check_interval FROM last_checks,monitors
WHERE last_checks.monitor_id = monitors.monitor_id AND checked_at > NOW()- INTERVAL monitors.interval SECONDS
*/



/* dashboard content */

select distinct(title),response_time,status from monitors,checks 
where  monitors.monitor_id = checks.monitor_id

select monitor_id, count(response_code) from checks where response_code=200 group by monitor_id;

select monitor_id, count(response_code),avg(response_time) from checks group by monitor_id;

select monitor_id,user_id,title from monitors
where user_id=7

select monitor_id, count(response_code),avg(response_time)
from checks
group by monitor_id
having monitor_id in (8,9)
;


select monitor_id, count(response_code)
from checks 
where response_code=200
group by monitor_id
having monitor_id in(8,9);

select monitor_id, response_code,response_time from checks;

/*
select monitor_id, count(response_code)
from checks 
group by monitor_id
having response_code=200;
*/

select monitor_id,status,checked_at
from checks
where monitor_id in (8,9)
order by checked_at desc


create table domains(
  domain_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  url varchar(100) NOT NULL,
  protocol varchar(10) NOT NULL,
  organization varchar(100) DEFAULT NULL,
  expiry date DEFAULT NULL
);