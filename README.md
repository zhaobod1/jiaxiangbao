Project JiaXiangbao 
======================
    author:zhaobo
    data:2017-01-08
-------------------
managerPlant 

account:admin

psw:123456


安装步骤：
1、
database change（修改原来系统的数据库错误）:


    ALTER TABLE `jxb`.`ecs_supplier_shop_config` CHANGE COLUMN `id` `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;



2、把文件直接覆盖到根目录即可。注意覆盖之前，对原系统先做备份。