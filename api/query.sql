alter TABLE `downtime_alert`.`users` modify column `created_at` VARCHAR(200) NOT NULL; 
desc `downtime_alert`.`users`;



select distinct(checks.monitor_id), checked_at from monitors,checks
where monitors.monitor_id = checks.monitor_id 
AND user_id = 7
AND is_active = true
;

select monitor_id,max(checked_at)
from checks
where  monitor_id in (select monitor_id from monitors where user_id=7)
group by monitor_id
;

select * from users;

select * from monitors where monitor_id=12


select monitor_id,status,max(checked_at) as mts
from checks
where checked_at = mts AND monitor_id in (select monitor_id from monitors where user_id=7)
;