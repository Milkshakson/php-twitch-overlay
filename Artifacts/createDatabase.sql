
create table twitch_user
(
    login varchar(100) not null,
    display_name varchar(100), 
    profile_image_url varchar(200),
    userId integer,
    description varchar(500),
    created_at datetime,
    primary key(login)
);

create table twitch_authorization
(
    login varchar(100) not null, 
    access_token varchar(300) not null,
    scope varchar(300) not null,
    expires_in integer,
    refresh_token  varchar(300) not null,
    token_type varchar(20),
    authorization_date datetime,
    userId integer,
    expire_date datetime,
    primary key (login,scope)
);

