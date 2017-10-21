<template>
	<button type="submit" :class="classes" @click="toggle">
		<span class="glyphicon glyphicon-heart"></span>
		<span v-text="count"></span>
	</button>
</template>

<script>
	export default {
		props: ['reply'],

		data () {
			return {
				count: this.reply.favoritesCount,
				active: this.reply.isFavorited
			}
		},

		computed: {
			classes () {
				return ['btn', this.active ? 'btn-primary' : 'btn-default'];
			},

			endpoint() {
				return '/replies/' + this.reply.id + '/favorites';
			}
		},

		methods: {
			toggle() {
				this.active ? this.destroy() : this.created();
			},
			destroy() {
				axios.delete(this.endpoint);
				this.active = false;
				this.count--;
			},
			created() {
				axios.post(this.endpoint);
				this.active = true;
				this.count++;
			}
		}
	}
	// {{ $reply->favorites_count }}
</script>