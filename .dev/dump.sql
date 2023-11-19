-- public.card definition

-- Drop table

-- DROP TABLE public.card;

CREATE TABLE public.card (
	card_id varchar(36) NOT NULL,
	card_name text NULL,
	CONSTRAINT card_pk PRIMARY KEY (card_id)
);
CREATE INDEX card_card_name_idx ON public.card USING btree (card_name);

-- Permissions

ALTER TABLE public.card OWNER TO spherecube;
GRANT ALL ON TABLE public.card TO spherecube;


-- public.cardfield definition

-- Drop table

-- DROP TABLE public.cardfield;

CREATE TABLE public.cardfield (
	cardfield_id varchar(36) NOT NULL,
	cardfield_name text NULL,
	plugin_code varchar(64) NULL,
	plugin_field int4 NOT NULL,
	CONSTRAINT cardfield_pk PRIMARY KEY (cardfield_id)
);
CREATE INDEX cardfield_cardfield_name_idx ON public.cardfield USING btree (cardfield_name);
CREATE INDEX cardfield_plugin_code_idx ON public.cardfield USING btree (plugin_code);
CREATE INDEX cardfield_plugin_field_idx ON public.cardfield USING btree (plugin_field);

-- Permissions

ALTER TABLE public.cardfield OWNER TO spherecube;
GRANT ALL ON TABLE public.cardfield TO spherecube;


-- public.cardfieldvalue definition

-- Drop table

-- DROP TABLE public.cardfieldvalue;

CREATE TABLE public.cardfieldvalue (
	cardfieldvalue_id varchar(36) NOT NULL,
	card_id varchar(36) NULL,
	cardfield_id varchar(36) NULL,
	value text NULL,
	cardfieldvalue_pos int4 NOT NULL,
	CONSTRAINT cardfieldvalue_pk PRIMARY KEY (cardfieldvalue_id)
);
CREATE INDEX cardfieldvalue_card_id_idx ON public.cardfieldvalue USING btree (card_id);
CREATE INDEX cardfieldvalue_cardfield_id_idx ON public.cardfieldvalue USING btree (cardfield_id);
CREATE INDEX cardfieldvalue_cardfieldvalue_pos_idx ON public.cardfieldvalue USING btree (cardfieldvalue_pos);
CREATE INDEX cardfieldvalue_value_idx ON public.cardfieldvalue USING btree (value);

-- Permissions

ALTER TABLE public.cardfieldvalue OWNER TO spherecube;
GRANT ALL ON TABLE public.cardfieldvalue TO spherecube;


-- public.plugin definition

-- Drop table

-- DROP TABLE public.plugin;

CREATE TABLE public.plugin (
	plugin_name varchar(50) NOT NULL,
	plugin_version varchar(20) NULL,
	plugin_code varchar(64) NULL,
	plugin_class varchar(400) NULL,
	plugin_desc text NULL,
	plugin_model int4 NOT NULL,
	plugin_field int4 NOT NULL,
	plugin_meta int4 NOT NULL,
	active int4 NOT NULL,
	CONSTRAINT plugin_pk PRIMARY KEY (plugin_name)
);
CREATE INDEX plugin_active_idx ON public.plugin USING btree (active);
CREATE INDEX plugin_plugin_code_idx ON public.plugin USING btree (plugin_code);

-- Permissions

ALTER TABLE public.plugin OWNER TO spherecube;
GRANT ALL ON TABLE public.plugin TO spherecube;


INSERT INTO public.card (card_id,card_name) VALUES
	 ('1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','Server 1'),
	 ('1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','NAS Storage on Rack 106.10.250.20'),
	 ('1dfe69ea-5f40-11eb-8d14-02004c4f4f50','Server 3'),
	 ('1f912a9b-5f40-11eb-8d14-02004c4f4f50','Server 4'),
	 ('207f47b9-5f40-11eb-8d14-02004c4f4f50','Server 5'),
	 ('ac7e17c1-5f40-11eb-8d14-02004c4f4f50','Rack (#23 - user segment)');


INSERT INTO public.cardfield (cardfield_id,cardfield_name,plugin_code,plugin_field) VALUES
	 ('151f62cc-0870-4574-8bc1-ca8a4ebfed5f','Info text','text',1),
	 ('6327f2f4-5ed3-11eb-8d14-02004c4f4f50','Linked to','link',1),
	 ('a10241f7-5f40-11eb-8d14-02004c4f4f50','Type','text',1),
	 ('a10341f7-5f40-11eb-8d14-02004c4f4f50','Memo','text',1),
	 ('a10541f7-5f40-11eb-8d14-02004c4f4f50','IP','text',1),
	 ('a10541f8-5f40-11eb-8d14-02004c4f4f50','DateTime','ts',1);


INSERT INTO public.cardfieldvalue (cardfieldvalue_id,card_id,cardfield_id,value,cardfieldvalue_pos) VALUES
	 ('bb26065b-27cb-4678-89ea-89142d75601a','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10341f7-5f40-11eb-8d14-02004c4f4f50','Info text',3),
	 ('da2d2dca-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','#23 - user segment',1),
	 ('da2f535d-618f-11eb-869a-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),
	 ('da2f548f-618f-11eb-869a-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),
	 ('da2f5511-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),
	 ('da2f558d-618f-11eb-869a-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),
	 ('da2f5609-618f-11eb-869a-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),
	 ('da2f5684-618f-11eb-869a-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','42.3.10.43',2),
	 ('da2f5703-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50',2),
	 ('da2f5782-618f-11eb-869a-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','58.43.43.84',2),
	 ('da2f57f8-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','54.16.39.26',2),
	 ('da2f586d-618f-11eb-869a-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','13.106.12.101',2),
	 ('da2f58e3-618f-11eb-869a-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','108.116.20.106',2),
	 ('da2f5958-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50',3),
	 ('da2f59cb-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50',3),
	 ('da2f5a40-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50',4),
	 ('da2f5ab3-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50',5),
	 ('da2f5ab4-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50',6),
	 ('da2f5ab5-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','a10541f8-5f40-11eb-8d14-02004c4f4f50','125458669698',7);


INSERT INTO public.plugin (plugin_name,plugin_version,plugin_code,plugin_class,plugin_desc,plugin_model,plugin_field,plugin_meta,active) VALUES
	 ('Card','1.0.0','card','Card','Base Card model',1,0,0,1),
	 ('Link','1.0.0','link','Link','Fields such as links allow you to associate a card with another.',0,1,1,1),
	 ('Text','1.0.0','text','Text','Standart text fields',0,1,0,1),
	 ('User','1.0.0','user','User','Base User model',1,0,1,0),
	 ('Ts','1.0.0','ts','Ts','Standart timestamp',0,0,1,0);
