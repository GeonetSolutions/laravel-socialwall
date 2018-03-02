 <template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            	<h1>Geonet Social Wall</h1>
            	<div class="row row-eq-height">
            		<div class="social-loading text-center" v-if="loading">
				<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
				<span class="sr-only">Loading...</span>
			</div>
			<template v-for="post in feed">
		        	<div v-if="wall_style === 'feed'" class="row social_post social_post_feed" :class="'social_' + post.social_type" >
					<div class="col-md-4 social-img text-center" >
						<img v-if="post.media" :src="post.media" />
					</div>
					<div class="col-md-8">
						<p class="social_message" v-if="post.message">{{ post.message }}</p>
						<a :href="post.link" class="btn btn-secondary" target="_BLANK">
							Read More on 
							<i class="fa fa-instagram" v-if="post.social_type == 'instagram'" aria-hidden="true"></i>
							<i class="fa fa-twitter" v-if="post.social_type == 'twitter'" aria-hidden="true"></i>
							<i class="fa fa-facebook" v-if="post.social_type == 'facebook'" aria-hidden="true"></i>
						</a>
						<div class="profile_info">
							<p><img class="profile_pic" :src="post.profile_pic" /> Posted By: {{ post.name }}</p>
							<p class="date">Date Posted: {{ post.frontend_timestamp }}</p>
						</div>
					</div>
				</div>
				<div v-else class="col-md-4 social_post" >
					<p v-if="post.media" style="text-align:center">
						<img style="max-height:200px; max-width:300px;" :src="post.media" />
					</p>
					<p v-if="post.message">{{ post.message }}</p>
					<a :href="post.link" target="_BLANK" class="btn btn-secondary btn-social_block"> 
						Read More on 
						<i class="fa fa-instagram" v-if="post.social_type == 'instagram'" aria-hidden="true"></i>
						<i class="fa fa-twitter" v-if="post.social_type == 'twitter'" aria-hidden="true"></i>
						<i class="fa fa-facebook" v-if="post.social_type == 'facebook'" aria-hidden="true"></i>
					</a>
					<div class="profile_info">
						<p><img class="profile_pic" :src="post.profile_pic" /> Posted By: {{ post.name }}</p>
						<p class="date">Date Posted: {{ post.frontend_timestamp }}</p>
					</div>
				</div>
			</template>
		</div>
            </div>
        </div>
    </div>
</template>
<script>
	module.exports = {
		props: ['wall_style'],

		data: function() {
			return {
				feed: [],
				loading : false
			};
		},
		created: function() {
			this.loading = true;
			axios.get("/ajax/socialwall/")
              .then(response => {
              	this.feed = response.data
              	this.loading = false;
              })
		}
	}
</script>
<style>
	.social-loading { width: 100% }
	.profile_pic { 
		height:25px;
		vertical-align: middle; 
	}
	.row.row-eq-height {
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		flex-wrap: wrap;
	}
	.row.row-eq-height > [class*='col-'] {
		display: flex;
		flex-direction: column;
		justify-content: center;
	}
	.profile_info {
		text-align: right;
	}
	.text-center {
		text-align: center;
	}
	.btn-secondary{
		color: #292b2c;
    	background-color: #fff;
    	border-color: #ccc;
	}
	.btn-secondary:hover {
    	color: #292b2c;
    	background-color: #e6e6e6;
    	border-color: #adadad;
	}
	.btn-social_block{
		margin: 20px;
	}
	.social_post_feed {
		width: 100%;
		position: relative;
		min-height:200px;
		overflow:hidden;
	}
	.social_post_feed::after {
	    content: "\F087";
	    font-family: FontAwesome;
	    font-style: normal;
	    font-weight: normal;
	    text-decoration: inherit;
	    position: absolute;
	    font-size: 150px;
	    color: lightblue;
	    right: 26px;
	    z-index: 1;
	}
	.social_post_feed.social_twitter::after {
		content: "\F099";
	    font-family: FontAwesome;
	}
	.social_post_feed.social_instagram::after {
		content: "\f16d";
	    font-family: FontAwesome;
	}
	.social_post_feed.social_facebook::after {
		content: "\f09a";
	    font-family: FontAwesome;
	}
	.social_post_feed > [class*='col-'] {
		z-index: 2;
	}
	.social_message{
		max-width: 500px;
		padding: 20px 0px;
	}
	.social_post:nth-child(even) {
		background: #fefefe
	}
	.social_post:nth-child(odd) {
		background: #ececec
	}
	.social-img img { 
		max-height:200px;
		max-width:300px;
		margin: 10px 0px;
	}
</style>
