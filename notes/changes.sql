ALTER TABLE `hosts` ADD `website` VARCHAR(500) NOT NULL AFTER `status`;

ALTER TABLE `audios` CHANGE `order` `sequence` INT(2) NOT NULL;
ALTER TABLE `tests` CHANGE `order` `sequence` INT(2) NOT NULL;
ALTER TABLE `topics` CHANGE `order` `sequence` INT(2) NOT NULL;
ALTER TABLE `videos` CHANGE `order` `sequence` INT(2) NOT NULL;
ALTER TABLE `worksheets` CHANGE `orders` `sequence` INT(2) NOT NULL;

