-- Severites
create table severites( 
	idSeverite serial not null  primary key, 
	nom varchar(50)  not null
);
-- Logs
create TABLE logs ( 
	idLog serial not null primary key, 
	dateAction DATE NOT NULL ,
	message varchar(500) not null, 
	idSeverite int not null,
	constraint fk_severite foreign key(idSeverite) references severites(idSeverite)
 );
 -- vue vérifier le formatage de date
create view v_listlogs as select logs.idLog AS id,to_char(logs.dateAction,'DD/MM/YYYY HH24:MI:SS') AS dateLog,
logs.message AS message,severites.nom AS severite from
logs join severites using(idSeverite) order by logs.dateAction desc;
-- données de base pour la sévérité
insert into severites (nom) values('warning');
insert into severites (nom) values('info');
insert into severites (nom) values('severe');
insert into severites (nom) values ('critique');