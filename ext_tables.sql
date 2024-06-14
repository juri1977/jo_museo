CREATE TABLE sys_file_reference (
 KEY sorting (tablenames,uid_foreign,fieldname,sorting_foreign),
);

#
# Table structure for table 'tx_jomuseo_domain_model_institute'
#
CREATE TABLE tx_jomuseo_domain_model_institute (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	active tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	shorttitle varchar(255) DEFAULT '' NOT NULL,
	subtitle varchar(255) DEFAULT '' NOT NULL,
	externlink varchar(255) DEFAULT '' NOT NULL,
	description text DEFAULT '',
	descriptionlong text DEFAULT '',
	contact text DEFAULT '',
	image int(11) DEFAULT '0',
	subobjectstitle varchar(255) DEFAULT '' NOT NULL,
	moreimagestitle varchar(255) DEFAULT '' NOT NULL,
	moreimages int(11) DEFAULT '0',
	bannerimage int(11) DEFAULT '0',
	morelinklabel varchar(255) DEFAULT '' NOT NULL,
	morelinkimg int(11) DEFAULT '0',
	bannerimg tinyint(4) unsigned DEFAULT '0',
	datatype varchar(255) DEFAULT '' NOT NULL,
	topicdb int(11) DEFAULT '0',
	times int(11) DEFAULT '0',
	classfication int(11) unsigned DEFAULT '0',
	keywords int(11) unsigned DEFAULT '0',
	geodata varchar(255) DEFAULT '' NOT NULL,
  	geotext varchar(255) DEFAULT '' NOT NULL,
	gnddata varchar(255) DEFAULT '' NOT NULL,
	isilnummer varchar(255) DEFAULT '' NOT NULL,
	tenantreference varchar(255) DEFAULT '' NOT NULL,
	idreference varchar(255) DEFAULT '' NOT NULL,
	website varchar(255) DEFAULT '' NOT NULL,
	relatedsingleitemsobjectstorage tinytext DEFAULT '',
	relateditems tinytext DEFAULT '',
	datastorage tinytext DEFAULT '',
	relatedsingleitemstitle varchar(255) DEFAULT '' NOT NULL,
	relatedsingleitems tinytext DEFAULT '',
	barrierfree tinyint(4) DEFAULT '0' NOT NULL,
	externalstock varchar(255) DEFAULT '' NOT NULL,
	notlocalstatus tinyint(4) unsigned DEFAULT '0' NOT NULL,
	outerlink varchar(255) DEFAULT '' NOT NULL,

	metas int(11) DEFAULT '0',
	social int(11) DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_instituteclass'
#
CREATE TABLE tx_jomuseo_domain_model_instituteclass (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_topic'
#
CREATE TABLE tx_jomuseo_domain_model_topic (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_times'
#
CREATE TABLE tx_jomuseo_domain_model_times (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_instituteclass_mm'
#
CREATE TABLE tx_jomuseo_domain_model_instituteclass_mm (
  `uid_local` int(11) DEFAULT '0' NOT NULL,
  `uid_foreign` int(11) DEFAULT '0' NOT NULL,
  `sorting` int(11) DEFAULT '0' NOT NULL,
  KEY `uid_local` (`uid_local`),
  KEY `uid_foreign` (`uid_foreign`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_keywords'
#
CREATE TABLE tx_jomuseo_domain_model_keywords (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_topic_mm'
#
CREATE TABLE tx_jomuseo_domain_model_topic_mm (
  `uid_local` int(11) DEFAULT '0' NOT NULL,
  `uid_foreign` int(11) DEFAULT '0' NOT NULL,
  `sorting` int(11) DEFAULT '0' NOT NULL,
  KEY `uid_local` (`uid_local`),
  KEY `uid_foreign` (`uid_foreign`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_times_mm'
#
CREATE TABLE tx_jomuseo_domain_model_times_mm (
  `uid_local` int(11) DEFAULT '0' NOT NULL,
  `uid_foreign` int(11) DEFAULT '0' NOT NULL,
  `sorting` int(11) DEFAULT '0' NOT NULL,
  KEY `uid_local` (`uid_local`),
  KEY `uid_foreign` (`uid_foreign`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_keywords_mm'
#
CREATE TABLE tx_jomuseo_domain_model_keywords_mm (
  `uid_local` int(11) DEFAULT '0' NOT NULL,
  `uid_foreign` int(11) DEFAULT '0' NOT NULL,
  `sorting` int(11) DEFAULT '0' NOT NULL,
  KEY `uid_local` (`uid_local`),
  KEY `uid_foreign` (`uid_foreign`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_annotation'
#
CREATE TABLE tx_jomuseo_domain_model_annotation (

	`uid` int(11) unsigned DEFAULT '0' NOT NULL primary key auto_increment,
	`pid` int(11) DEFAULT '0' NOT NULL,
	`title` varchar(255) DEFAULT '' NOT NULL,
	
	`coordinates` text DEFAULT '' NOT NULL,
	`comment` text DEFAULT '' NOT NULL,
	`feuser` int(11) unsigned DEFAULT '0',
	`entityid` varchar(255) DEFAULT '0' NOT NULL,
	`asset` varchar(255) DEFAULT '0' NOT NULL,
	`public` tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob
);

CREATE TABLE sys_file_reference (
 	related int(11) unsigned DEFAULT '0',
);
#
# Table structure for table 'tx_jomuseo_domain_model_exhibition'
#
CREATE TABLE tx_jomuseo_domain_model_exhibition (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	subtitle int(11) DEFAULT '0',
	infotextetitle varchar(255) DEFAULT '',
	infotexte int(11) DEFAULT '0',
	infotextecolor varchar(255) DEFAULT '',
	period varchar(255) DEFAULT '',
	place text DEFAULT '',
	placecolor varchar(255) DEFAULT '',
	openinghours varchar(255) DEFAULT '',
	opening int(11) unsigned DEFAULT '0',
	summary text DEFAULT '',
	aboutproject text DEFAULT '',
	aboutprojectcolor varchar(255) DEFAULT '',
	zitiervorschlag text DEFAULT '',
	zitiervorschlagcolor varchar(255) DEFAULT '',
	section int(11) DEFAULT '0',
	intro int(11) unsigned DEFAULT '0',
	audio int(11) unsigned DEFAULT '0',
	flyer int(11) unsigned DEFAULT '0',
	tosectionimg int(11) unsigned DEFAULT '0',
	tosectiondesc text DEFAULT '',
	tosectionbtntext varchar(255) DEFAULT '',
	jsonfile int(11) unsigned DEFAULT '0',
	video varchar(255) DEFAULT '',
	derivate int(11) unsigned DEFAULT '0',
	tags text DEFAULT '',
	location text DEFAULT '',
	entity text DEFAULT '',
	links text DEFAULT '',
	kontextinfo text DEFAULT '',

	maincolor varchar(255) DEFAULT '',
	maincolorborder varchar(255) DEFAULT '',
	maincolorfont varchar(255) DEFAULT '',
	maincolorlinkfont varchar(255) DEFAULT '',
	fontcolor varchar(255) DEFAULT '',
	bggradient1 varchar(255) DEFAULT '',
	bggradient2 varchar(255) DEFAULT '',
	fontcolordetail1 varchar(255) DEFAULT '',
	configuration text DEFAULT '',

	vorschauseite tinyint(4) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_wb_mm'
#
CREATE TABLE tx_jomuseo_domain_model_exhibition_section_mm (
  `uid_local` int(11) DEFAULT '0' NOT NULL,
  `uid_foreign` int(11) DEFAULT '0' NOT NULL,
  `sorting` int(11) DEFAULT '0' NOT NULL,
  KEY `uid_local` (`uid_local`),
  KEY `uid_foreign` (`uid_foreign`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_section'
#
CREATE TABLE tx_jomuseo_domain_model_section (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	subtitle int(11) DEFAULT '0',
	teaser text DEFAULT '',
	startdate varchar(255) DEFAULT '' NOT NULL,
	view varchar(255) DEFAULT '' NOT NULL,
	padding int(11) unsigned DEFAULT '0',
	textpos int(11) unsigned DEFAULT '0',
	description text DEFAULT '',
	literature text DEFAULT '',
	exhibit int(11) DEFAULT '0',
	jsonfile int(11) unsigned DEFAULT '0',
	derivate int(11) unsigned DEFAULT '0',
	tags text DEFAULT '',
	location text DEFAULT '',
	locationprocessed text DEFAULT '',
	entity text DEFAULT '',
	entityprocessed text DEFAULT '',
	links text DEFAULT '',
	kontextinfo text DEFAULT '',
	nextsectiontext text DEFAULT '',
	audio int(11) unsigned DEFAULT '0',
	configuration text DEFAULT '',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jowettbewerbe_domain_model_wb_mm'
#
CREATE TABLE tx_jomuseo_domain_model_section_exhibit_mm (
  `uid_local` int(11) DEFAULT '0' NOT NULL,
  `uid_foreign` int(11) DEFAULT '0' NOT NULL,
  `sorting` int(11) DEFAULT '0' NOT NULL,
  KEY `uid_local` (`uid_local`),
  KEY `uid_foreign` (`uid_foreign`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_exhibit'
#
CREATE TABLE tx_jomuseo_domain_model_exhibit (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	subtitle int(11) DEFAULT '0',
	shorttext text DEFAULT '',
	bodytext text DEFAULT '',
	publikation text DEFAULT '',
	kontextinformation text DEFAULT '',
	links text DEFAULT '',
	derivate int(11) unsigned DEFAULT '0',
	morederivate int(11) unsigned DEFAULT '0',
	video int(11) unsigned DEFAULT '0',
	morevideo int(11) unsigned DEFAULT '0',
	audiotitel varchar(255) DEFAULT '' NOT NULL,
	audiotext text DEFAULT '',
	videotitel varchar(255) DEFAULT '' NOT NULL,
	videotext text DEFAULT '',
	audio int(11) unsigned DEFAULT '0',
	moreaudio int(11) unsigned DEFAULT '0',
	booksites int(11) unsigned DEFAULT '0',
	bookfolder varchar(255) DEFAULT '0',
	tags text DEFAULT '',
	location text DEFAULT '',
	locationprocessed text DEFAULT '',
	entity text DEFAULT '',
	entityprocessed text DEFAULT '',
	jsonfile int(11) unsigned DEFAULT '0',
	objektnummer varchar(255) DEFAULT '',
	koordinaten varchar(255) DEFAULT '',
	datasubtype varchar(255) DEFAULT '',
	ctatext varchar(255) DEFAULT '',
	transkript text DEFAULT '',
	configuration text DEFAULT '',
	datapage varchar(255) DEFAULT '',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_jomuseo_domain_model_data'
#
CREATE TABLE tx_jomuseo_domain_model_data (

	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	parentid int(11) DEFAULT '0' NOT NULL,
	parenttable varchar(255) DEFAULT '' NOT NULL,

	info varchar(255) DEFAULT '' NOT NULL,
	uri varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text DEFAULT '',

	fieldname varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

CREATE TABLE  tx_jomuseo_domain_model_entity (
  `uid` int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
  `pid` int(11) DEFAULT '0' NOT NULL,

  `title` text DEFAULT '' NOT NULL DEFAULT '0',
  `stipendiat` varchar(255) DEFAULT '' NOT NULL,
  `linktosite` text DEFAULT '' NOT NULL DEFAULT '0',
  `linkvideo` text DEFAULT '' NOT NULL DEFAULT '0',
  `geoplace` text DEFAULT '' NOT NULL DEFAULT '0',
  `exhibitcta` varchar(255) DEFAULT '' NOT NULL,
  `exhibit` int(11) DEFAULT '0',
  `roommodel` text DEFAULT '' NOT NULL DEFAULT '0',
  `jsonstring` text DEFAULT '' NOT NULL DEFAULT '0',
  `objecttype` varchar(255) DEFAULT '' NOT NULL,
  `shorttext` text DEFAULT NULL,
  `bodytext` text DEFAULT NULL,
  `datierungstart` varchar(255) DEFAULT '' NOT NULL,
  `datierungend` varchar(255) DEFAULT '' NOT NULL,
  `geoplacegeojson` text DEFAULT NULL,
  `additionalproperties` text DEFAULT '',
  `objectstorage` varchar(255) DEFAULT '' NOT NULL,
  `audio` int(11) unsigned DEFAULT '0',
  `video` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`uid`)
);

#
# Table structure for table 'tx_jomuseo_domain_model_meta'
#
CREATE TABLE tx_jomuseo_domain_model_meta (
	title varchar(255) DEFAULT '' NOT NULL,
	description text DEFAULT '',
	image int(11) DEFAULT '0',
	video int(11) unsigned DEFAULT '0',
	audio int(11) unsigned DEFAULT '0',
	videolink varchar(255) DEFAULT '' NOT NULL,
	audiolink varchar(255) DEFAULT '' NOT NULL,
	otherlinktext varchar(255) DEFAULT '' NOT NULL,
	otherlink varchar(255) DEFAULT '' NOT NULL,
	view varchar(255) DEFAULT '' NOT NULL,

	parentid int(11) DEFAULT '0' NOT NULL,
	parenttable varchar(255) DEFAULT '' NOT NULL,

	t3ver_label varchar(30) DEFAULT '' NOT NULL,
);

#
# Table structure for table 'tx_jomuseo_domain_model_social'
#
CREATE TABLE tx_jomuseo_domain_model_social (
	type varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	uri varchar(255) DEFAULT '' NOT NULL,

	parentid int(11) DEFAULT '0' NOT NULL,
	parenttable varchar(255) DEFAULT '' NOT NULL,

	t3ver_label varchar(30) DEFAULT '' NOT NULL,
);

#
# Table structure for table 'tx_jomuseo_domain_model_collectorbox'
#
CREATE TABLE tx_jomuseo_domain_model_collectorbox (
	feuserid int(11) DEFAULT '0',
	boxdata text DEFAULT '' NOT NULL DEFAULT '0',
	project varchar(255) DEFAULT '' NOT NULL,

	parentid int(11) DEFAULT '0' NOT NULL,
	parenttable varchar(255) DEFAULT '' NOT NULL,

	t3ver_label varchar(30) DEFAULT '' NOT NULL,
);