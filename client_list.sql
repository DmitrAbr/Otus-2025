create table client_lists (
	ID INTEGER NOT NULL auto_increment PRIMARY KEY,
	UF_NAME VARCHAR(50),
	UF_LASTNAME VARCHAR(50),
	UF_PHONE VARCHAR(50),
	UF_JOBPOSITION VARCHAR(50),
	UF_SCORE VARCHAR(50)
);

insert into client_lists (ID, UF_NAME, UF_LASTNAME, UF_PHONE, UF_JOBPOSITION, UF_SCORE) values (1, 'Владислав', 'Михайлов', '8(401)213-11-64', 'JS developer', 'A');
insert into client_lists (ID, UF_NAME, UF_LASTNAME, UF_PHONE, UF_JOBPOSITION, UF_SCORE) values (2, 'Екатерина', 'Шарова', '8(401)253-12-49', 'PHP developer', 'B');
insert into client_lists (ID, UF_NAME, UF_LASTNAME, UF_PHONE, UF_JOBPOSITION, UF_SCORE) values (3, 'Елена', 'Шашкова', '8(401)312-21-12', 'С# developer', 'C');



