<template>
	<button class="btn" :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
	export default {
		props: ['active'],

		data() {
			return {
				isSubscribed: this.active
			}
		},

		computed: {
			classes() {
				return ['btn', this.isSubscribed ? 'btn-primary' : 'btn-default'];
			},
		},

		methods: {
			subscribe() {
				let requestType = this.isSubscribed ? 'delete' : 'post';
				axios[requestType](location.pathname + '/subscriptions');
				this.isSubscribed = ! this.isSubscribed;
				flash('You have subscribe to this thread');
			}
		}
	}
</script>