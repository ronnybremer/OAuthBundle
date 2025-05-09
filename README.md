# OAuthBundle
> [!IMPORTANT]  
> This is under active development to support Contao 5.3

This bundle adds OAuth Authentication functionality for Contao frontend users. In addition, you can also make the entire frontend accessible exclusively via an upstream login.

Currently this bundle only supports OpenID Connect Servers.

Originally developed by https://github.com/Kuestenschmiede/OAuthBundle for Contao 4.
Original (c) Küstenschmiede GmbH Software & Design

## Installation

### Step 1: Install the bundle

You can install the bundle using composer or the contao manager. For composer use the following command:

```bash
$ composer require ronnybremer/oauth
```

In the contao manager, you can find the bundle under "ronnybremer/oauth".

### Step 2: Configure the bundle

After the installation you need to configure the app under config/config.yml.

**OpenID-Connect:**

```yml
parameters:
  oauth.oidc.client_id: "### The client id for the openid connect server ###"
  oauth.oidc.client_secret: "### The client secret for the openid connect server ###"
  oauth.oidc.auth_server_url: "### The authentication url for the openid connect server ###"
  oauth.oidc.realm: "### The realm for the openid connect server ####"
  # Uncomment the following option if you want to access the entire frontend via an upstream login
  #oauth.oidc.secured: "true"
```

### Step 3: Clear the cache

You can now clear the cache via the contao manager or the contao console:

```bash
$ php vendor/bin/contao-console cache:clear -e prod
```

### Step 4: Configure the login module

This step is only necessary if you don't make the entire frontend accessible via an upstream login.

In the contao backend you can create the new OAuth login module. There you can set up, which groups will be assigned to new logins and which user data will be stored in the contao database. The username is saved by default. 

The module creates a new login button, that you can place anywhere on your site.

## Callback URLs

To be able to login successfully you need to allow the following callback urls in your OAuth authorization server:

| OAuth Provider | Callback URL |
|-|-|
|OpenID Connect | https://your-domain/oidc/callback |
