use const_company;

select * from projects;

select * from testimonials;
select * from service_images;
DELETE FROM services WHERE id IN (62,63,64,65,66);
DELETE FROM service_images WHERE id BETWEEN 1 AND 83;


-- safe mode using]\\ change 0 to 1 after  --  
-- SET SQL_SAFE_UPDATES = 0;

# Tables_in_const_company

show tables;
