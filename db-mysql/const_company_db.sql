use const_company;

select * from services;

DELETE FROM services WHERE id IN (7,12);


-- safe mode using]\\ change 0 to 1 after  --  
SET SQL_SAFE_UPDATES = 0;



show tables;
