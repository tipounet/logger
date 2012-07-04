CREATE SEQUENCE sq_severites MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 21  noCACHE  NOORDER NOCYCLE;
CREATE SEQUENCE sq_logs MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 21 noCACHE  NOORDER NOCYCLE ;
create table severites( 
	idSeverite int not null  primary key, 
	nom varchar2(50 char)  not null
);
create TABLE logs ( 
	idLog integer not null primary key, 
	dateAction DATE NOT NULL ,
	message varchar2(500 char) not null, 
	idSeverite int not null,
	constraint fk_severite foreign key(idSeverite) references severites(idSeverite)
 );
create view v_listlogs as select logs.idLog AS id,to_char(logs.dateAction,'D/MM/YYYY HH24:MI:SS') AS dateLog,
logs.message AS message,severites.nom AS severite from
logs join severites using(idSeverite) order by logs.dateAction desc;

create or replace
TRIGGER trg_severite before insert
on severites for each row
declare
    integrity_error  exception;
    errno            integer;
    errmsg           char(200);
    dummy            integer;
    found            boolean;
begin
    if :new.idSeverite is null or :new.idSeverite = 0 then
  		select sq_severites.NEXTVAL INTO :new.idSeverite from dual;
    end if;
--  Traitement d'erreurs
exception
    when integrity_error then
       raise_application_error(errno, errmsg);
end;
-- trigger des logs
create or replace
TRIGGER trg_log before insert
on logs for each row
declare
    integrity_error  exception;
    errno            integer;
    errmsg           char(200);
    dummy            integer;
    found            boolean;
begin
    if :new.idLog is null or :new.idLog = 0 then
  		select sq_logs.NEXTVAL INTO :new.idLog from dual;
    end if;
--  Traitement d'erreurs
exception
    when integrity_error then
       raise_application_error(errno, errmsg);
end;
-- données de base pour la sévérité
insert into severites (nom) values('warning');
insert into severites (nom) values('info');
insert into severites (nom) values('severe');
insert into severites (nom) values ('critique');


select count(*) from USER_TABLES