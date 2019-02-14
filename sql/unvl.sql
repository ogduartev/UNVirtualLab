-- ======================================================================
-- ===   Sql Script for Database : MySQL db
-- ===
-- === Build : 84
-- ======================================================================

DROP TABLE IF EXISTS controls;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS practices;
DROP TABLE IF EXISTS 2deffects;
DROP TABLE IF EXISTS 2danimations;
DROP TABLE IF EXISTS curves;
DROP TABLE IF EXISTS plots;
DROP TABLE IF EXISTS control_groups;
DROP TABLE IF EXISTS modellers_models;
DROP TABLE IF EXISTS modellers;
DROP TABLE IF EXISTS parameters;
DROP TABLE IF EXISTS variables;
DROP TABLE IF EXISTS models;
DROP TABLE IF EXISTS sections;

-- ======================================================================

CREATE TABLE sections
  (
    id           int            unique not null auto_increment,
    section_id   int,
    name         varchar(255)   not null,
    description  text,
    enabled      tinyint,

    primary key(id),

    foreign key(section_id) references sections(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE models
  (
    id            int            unique not null auto_increment,
    section_id    int            not null,
    name          varchar(255)   not null,
    title         varchar(255),
    description   text,
    bibliography  text,
    exename       varchar(255),
    enabled       tinyint,

    primary key(id),

    foreign key(section_id) references sections(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE variables
  (
    id            int            unique not null auto_increment,
    model_id      int            not null,
    modelicaname  varchar(255)   not null,
    alias         varchar(255),
    description   text,
    units         varchar(255),
    type          varchar(255),
    value         varchar(255),

    primary key(id),

    foreign key(model_id) references models(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE parameters
  (
    id            int            unique not null auto_increment,
    model_id      int            not null,
    modelicaname  varchar(255)   not null,
    alias         varchar(255),
    description   text,
    units         varchar(255),
    type          varchar(255),
    value         varchar(255),

    primary key(id),

    foreign key(model_id) references models(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE modellers
  (
    id         int            unique not null auto_increment,
    firstname  varchar(255)   not null,
    lastname   varchar(255)   not null,
    email      varchar(255),

    primary key(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE modellers_models
  (
    modeller_id  int   not null,
    model_id     int   not null,

    foreign key(modeller_id) references modellers(id),
    foreign key(model_id) references models(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE control_groups
  (
    id           int            unique not null auto_increment,
    model_id     int            not null,
    name         varchar(255)   not null,
    description  text,
    enabled      tinyint,

    primary key(id),

    foreign key(model_id) references models(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE plots
  (
    id           int            unique not null auto_increment,
    model_id     int            not null,
    name         varchar(255)   not null,
    description  text,
    variable_id  int,
    minX         float,
    maxX         float,
    gridX        tinyint,
    autoscaleX   char(1),
    minY         float,
    maxY         float,
    gridY        tinyint,
    autoscaleY   char(1),
    firstdata    int,
    enabled      tinyint,

    primary key(id),

    foreign key(model_id) references models(id) on update CASCADE on delete CASCADE,
    foreign key(variable_id) references variables(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE curves
  (
    id           int            unique not null auto_increment,
    plot_id      int            not null,
    name         varchar(255)   not null,
    legend       varchar(255)   not null,
    description  text,
    variable_id  int            not null,
    colorRGB     varchar(255),
    enabled      tinyint,

    primary key(id),

    foreign key(plot_id) references plots(id) on update CASCADE on delete CASCADE,
    foreign key(variable_id) references variables(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE 2danimations
  (
    id           int            unique not null auto_increment,
    name         varchar(255)   not null,
    description  text,
    model_id     int            not null,
    svg_file     varchar(255),
    duration     float,
    sample_time  float,
    enabled      tinyint,

    primary key(id),

    foreign key(model_id) references models(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE 2deffects
  (
    id               int            unique not null auto_increment,
    name             varchar(255),
    description      text,
    2danimation_id   int,
    svganimation_id  varchar(255),
    variable_id      int,
    variable_aux_id  int,
    sequence         int,
    type             varchar(255),
    offset           decimal,
    scale            decimal,
    colorRGBmin      varchar(255),
    colorRGBmax      varchar(255),
    colorMin         decimal,
    colorMax         decimal,
    enabled          tinyint,

    primary key(id),

    foreign key(2danimation_id) references 2danimations(id) on update CASCADE on delete CASCADE,
    foreign key(variable_id) references variables(id),
    foreign key(variable_aux_id) references variables(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE practices
  (
    id           int       unique not null auto_increment,
    model_id     int,
    name         text,
    header       text,
    description  text,
    enabled      tinyint,

    primary key(id),

    foreign key(model_id) references models(id) on update CASCADE on delete CASCADE
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE users
  (
    id         int            unique not null auto_increment,
    user_name  varchar(255)   not null,
    password   varchar(255)   not null,

    primary key(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

CREATE TABLE controls
  (
    id                int            unique not null auto_increment,
    control_group_id  int            not null,
    name              varchar(255),
    parameter_id      int            not null,
    description       text,
    allowedvalues     text,
    value             varchar(255),
    min               float,
    max               float,
    step              float,
    enabled           tinyint,

    primary key(id),

    foreign key(control_group_id) references control_groups(id) on update CASCADE on delete CASCADE,
    foreign key(parameter_id) references parameters(id)
  )
 ENGINE = InnoDB;

-- ======================================================================

