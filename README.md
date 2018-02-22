![Geonet Solutions](/brand/banner.jpg)

[![Total Downloads](https://poser.pugx.org/geonetsolutions/socialwall/downloads)](https://packagist.org/packages/geonetsolutions/socialwall)
[![Latest Stable Version](https://poser.pugx.org/geonetsolutions/socialwall/v/stable)](https://packagist.org/packages/geonetsolutions/socialwall)
[![License](https://poser.pugx.org/geonetsolutions/socialwall/license)](https://packagist.org/packages/geonetsolutions/socialwall)

---

### Introduction

A simple package that integrates with different social platforms to provide a easy to use Social Wall for your next project.

---

#### Current Social Integrations

* Facebook
* Instagram
* Twitter

---

### Installation Laravel 5.5 +

1.  Composer require the package:

    ```composer require geonetsolutions/socialwall```

2.  Let Laravel 5.5/5.6 Automatic Package Discovery do its thang!

---

### Installation Laravel 5.4

1.  Composer require the package:

    ```composer require geonetsolutions/socialwall```
    
2.  Register the service provider in ```config\app.php``` providers array:

    ```Geonetsolutions\Socialwall\SocialWallServiceProvider::class```

---

### Publishing the Included Assets / Config

To publish the included Vue.js Social Wall Component Run ```php artisan vendor:publish```.

If you are provided with a list choose the option marked:  ```Geonetsolutions\Socialwall\SocialwallServiceProvider```.

The component will be copied into `resources/assets/js/components/GeonetSocialWallComponent.vue`. Obviously if this doesn't suit your project you can easily override it or implement your own.

The Config file will be published into your main application config folder: ```socialwall.php```.

---

### Implementing the Included Vue Component

Having setup your application front end tooling using the Laravel docs <https://laravel.com/docs/5.6/frontend> implement the included example component as follows:

1. In your application ```app.js``` add the following: 


```js
Vue.component('geonet-socialwall', require('./components/GeonetSocialWallComponent.vue')); <--- Add This
Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app',
});
```


2. Compile your front end assets ```npm run dev``` if using Laravel Mix <https://laravel.com/docs/5.6/mix>

3. Include the ```<geonet-socialwall></geonet-socialwall>``` in your view to see the component output.

---

### Customising the Component Output

If you want to switch the component from the default ```grid``` layout to a ```feed``` layout, simply add the ```wall_style``` property and set it to ```feed```

```<geonet-socialwall wall_style="feed"></geonet-socialwall>```

---

### Package Configuration Options

To override the package configuration use the following environment variables.

| Key  | Required | value | Description | 
| ------------- | ------------- | ------------- | ------------- |
| ```SOCIALWALL_DEV_MODE``` |  | TRUE/FALSE | Default is set to false. Enabled debug mode|
| ```TWITTER_CONSUMER_KEY``` | * | STRING | Consumer key as provided by Twitter |
| ```TWITTER_CONSUMER_SECRET``` | * | STRING | Consumer secret as provided by Twitter |
| ```TWITTER_OAUTH_ACCESS_TOKEN``` | * | STRING | Consumer OAuth access token as provided by Twitter |
| ```TWITTER_API_URL``` |  | STRING | URL of the Twitter API, By Default the Users Timeline |
| ```TWITTER_NUMBER_TWEETS``` | | INTEGER | How many tweets to pull in, default is 10 |
| ```FACEBOOK_APP_ID``` | * | STRING | App ID as Provided by Facebook |
| ```FACEBOOK_APP_SECRET``` | * | STRING | App Secret as provided by Facebook |
| ```FACEBOOK_PAGE_ID``` | * | STRING | Page ID of the Facebook Page to be streamed |
| ```FACEBOOK_REDIRECT_URI``` | * | STRING | Redirect URI for Facebook |
| ```FACEBOOK_NUMBER_POSTS``` |  | INTEGER | Number of Facebook Posts to Pull |
| ```INSTAGRAM_ACCESS_TOKEN``` | * | STRING | Instagram Access Token|
| ```INSTAGRAM_NUMBER_PHOTOS``` |  | INTEGER | Number of Instagram photos to pull, defaults to 10|

**Note:** Use the following link to get the access token for instagram

<https://api.instagram.com/oauth/authorize/?client_id=[CLIENT_ID]&redirect_uri=[REDIRECT_URI]&response_type=token>

---

### ENV Variables
Easy Copy & Paste ENV Variables! You're Welcome! :+1:

```env
SOCIALWALL_DEV_MODE

TWITTER_CONSUMER_KEY

TWITTER_CONSUMER_SECRET

TWITTER_OAUTH_ACCESS_TOKEN

TWITTER_OAUTH_ACCESS_TOKEN_SECRET

TWITTER_API_URL

TWITTER_NUMBER_TWEETS

FACEBOOK_APP_ID

FACEBOOK_APP_SECRET

FACEBOOK_PAGE_ID

FACEBOOK_REDIRECT_URI

FACEBOOK_NUMBER_POSTS

INSTAGRAM_ACCESS_TOKEN

INSTAGRAM_NUMBER_PHOTOS
```

---


[![Lewis Thompson](/brand/footer.jpg)](https://github.com/lewisthompsongeonet)
